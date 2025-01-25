<?php

namespace App\Filament\Pages;

use Aws\Sdk;
use Exception;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Select;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class Onboarding extends Page implements HasForms
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.onboarding';

    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        if (!auth()->user()->needs_aws_credentials) {
            $this->redirect(route('filament.admin.pages.dashboard'));
        }

        $this->form->fill([
            'aws_access_key_id' => auth()->user()->aws_access_key_id,
            'aws_secret_access_key' => auth()->user()->aws_secret_access_key,
            'aws_region' => auth()->user()->aws_region,
            'aws_s3_bucket' => auth()->user()->aws_s3_bucket ?: 'glacier-drip',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('AWS Credentials')
                        ->schema([
                            TextInput::make('aws_access_key_id')->label('AWS Access Key')
                                ->validationMessages([
                                    'required' => 'AWS Access Key is required'
                                ])
                                ->required(),
                            TextInput::make('aws_secret_access_key')->label('AWS Secret Key')
                                ->required()
                                ->validationMessages([
                                    'required' => 'AWS Secret Key is required'
                                ])
                                ->password(),
                        ])
                        ->afterValidation(function (Get $get) {
                            try {
                                auth()->user()->update([
                                    'aws_access_key_id' => $get('aws_access_key_id'),
                                    'aws_secret_access_key' => $get('aws_secret_access_key')
                                ]);

                                $s3Client = app(Sdk::class)->createS3([
                                    'region' => 'us-east-1',
                                    'version' => 'latest',
                                ]);

                                $s3Client->listBuckets();

                            } catch (Exception $e) {
                                auth()->user()->update([
                                    'aws_access_key_id' => '',
                                    'aws_secret_access_key' => ''
                                ]);
                                $message = 'Invalid AWS credentials.';
                                $this->addError('data.aws_access_key_id', $message);
                                $this->addError('data.aws_secret_access_key', $message);
                                throw new Halt();
                            }

                        }),
                    Wizard\Step::make('AWS Region')
                        ->schema([
                            Select::make('aws_region')
                                ->label('AWS Region')
                                ->validationMessages([
                                    'required' => 'AWS Region is required'
                                ])
                                ->searchable()
                                ->required()
                                ->options(config('aws.regions')),
                        ]),
                    Wizard\Step::make('AWS S3 Bucket')
                        ->schema([
                            TextInput::make('aws_s3_bucket')
                                ->label('AWS S3 Bucket')
                                ->required()
                                ->validationMessages(['required' => 'AWS S3 Bucket is required'])
                        ])
                ])
                    ->submitAction(view('filament.submit-button'))
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $bucketName = $this->form->getState()['aws_s3_bucket'];
            $region = $this->form->getState()['aws_region'];

            $s3Client = app(Sdk::class)->createS3([
                'region' => $region,
                'version' => 'latest',
            ]);

            $s3Client->createBucket([
                'Bucket' => $bucketName,
                'CreateBucketConfiguration' => [
                    'LocationConstraint' => $region,
                ],
            ]);

            auth()->user()->update([
                'aws_s3_bucket' => $bucketName,
                'aws_region' => $region
            ]);

            Notification::make()
                ->title('Onboarding complete!')
                ->success()
                ->send();

            $this->redirect(route('filament.admin.pages.dashboard'));
        } catch (Exception $e) {
            $message = 'Invalid AWS S3 Bucket.';
            $this->addError('data.aws_s3_bucket', $message);
            logger()->error($e->getMessage());
        }
    }
}
