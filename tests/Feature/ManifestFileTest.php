<?php

use App\Models\ManifestFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

it('can create a manifest file', function () {
    $manifestFile = ManifestFile::factory()->create();

    $foundManifestFile = ManifestFile::find($manifestFile->id);

    expect($foundManifestFile)->not->toBeNull()
        ->and($foundManifestFile->archive_id)->toBe($manifestFile->archive_id)
        ->and($foundManifestFile->size)->toBe($manifestFile->size)
        ->and($foundManifestFile->description)->toBe($manifestFile->description);
});

it('can update a manifest file', function () {
    $manifestFile = ManifestFile::factory()->create();
    $manifestFile->update(['description' => 'Updated description']);

    expect(ManifestFile::find($manifestFile->id)->description)->toBe('Updated description');
});

it('can delete a manifest file', function () {
    $manifestFile = ManifestFile::factory()->create();
    $manifestFile->delete();

    expect(ManifestFile::find($manifestFile->id))->toBeNull();
});
