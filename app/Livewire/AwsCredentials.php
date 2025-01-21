<?php

namespace App\Livewire;

use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

/**
 * @property ComponentContainer $form
 */
class AwsCredentials extends MyProfileComponent
{
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
                TextInput::make('aws_access_key_id')->label('AWS Access Key'),
                TextInput::make('aws_secret_access_key')->label('AWS Secret Key')
                    ->password(),
                Select::make('aws_region')
                    ->label('AWS Region')
                    ->searchable()
                    ->options(config('aws.regions')),
                TextInput::make('aws_s3_bucket')->label('AWS S3 Bucket')->readOnly(),
            ])
            ->statePath('data');
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
