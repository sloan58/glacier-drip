<?php

namespace App\Filament\Resources\ManifestFileResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ManifestFileResource;

class ListManifestFiles extends ListRecords
{
    protected static string $resource = ManifestFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('request-manifest')
                ->label('Request Manifest')
                ->requiresConfirmation()
                ->modalDescription('This will request a new manifest file from AWS.  It may take several hours to download')
                ->action(function() {
                    try {
                        auth()->user()->manifestFiles()->create();
                        Notification::make()
                            ->title('Manifest Requested!')
                            ->success()
                            ->send();
                    } catch (\Exception $exception) {
                        logger()->error($exception->getMessage());
                        Notification::make()
                            ->title('Oops, something went wrong...')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
