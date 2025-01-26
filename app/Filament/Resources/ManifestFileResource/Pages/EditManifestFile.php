<?php

namespace App\Filament\Resources\ManifestFileResource\Pages;

use App\Filament\Resources\ManifestFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManifestFile extends EditRecord
{
    protected static string $resource = ManifestFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
