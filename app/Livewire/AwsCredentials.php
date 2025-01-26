<?php

namespace App\Livewire;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

/**
 * @property ComponentContainer $form
 */
class AwsCredentials extends MyProfileComponent implements HasActions
{
    use InteractsWithActions;

    protected string $view = 'livewire.aws-credentials';
    public array $only = [
        'aws_access_key_id',
        'aws_secret_access_key',
        'aws_region',
        'aws_s3_bucket',
    ];
    public array $data;
    public $user;
    public static $sort = 20;
    public $userClass;

    public function mount(): void
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        $this->userClass = get_class($this->user);
        $this->form->fill($this->user->only($this->only));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('aws_access_key_id')->label('AWS Access Key')
                    ->validationMessages([
                        'required' => 'AWS Access Key is required'
                    ])
                    ->readOnly(),
                TextInput::make('aws_secret_access_key')->label('AWS Secret Key')
                    ->validationMessages([
                        'required' => 'AWS Secret Key is required'
                    ])
                    ->password()
                    ->readOnly(),
                TextInput::make('aws_region')->label('AWS S3 Region')->readOnly(),
                TextInput::make('aws_s3_bucket')->label('AWS S3 Bucket')->readOnly(),
            ])
            ->statePath('data');
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->color('danger')
            ->modalDescription('Deleting your AWS credentials will remove all related configurations
                                          and data.  It will not delete your data on AWS.')
            ->action(function() {
                auth()->user()->update([
                    'aws_access_key_id' => null,
                    'aws_secret_access_key' => null,
                    'aws_region' => null,
                    'aws_s3_bucket' => null,
                ]);
                return redirect()->route('filament.admin.pages.onboarding');
            });
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->all();
        $this->user->update($data);
        Notification::make()
            ->success()
            ->title(__('AWS credentials updated successfully'))
            ->send();
    }
}
