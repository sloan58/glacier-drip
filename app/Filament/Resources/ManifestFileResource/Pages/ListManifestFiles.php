<?php

namespace App\Filament\Resources\ManifestFileResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ManifestFileResource;

class ListManifestFiles extends ListRecords
{
    protected static string $resource = ManifestFileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
