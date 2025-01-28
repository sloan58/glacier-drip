<?php

use App\Filament\Resources\ManifestFileResource;
use App\Models\ManifestFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

// Set up roles and test user before each test
beforeEach(function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $userRole = Role::firstOrCreate(['name' => 'user']);

    // Create test users
    $this->admin = User::factory()->onboarded()->create();
    $this->admin->assignRole($adminRole);

    $this->user = User::factory()->onboarded()->create();
    $this->user->assignRole($userRole);
});

// --------------------------------------------
// Tests for Admin Role
// --------------------------------------------

it('admin can access the list page', function () {
    $this->actingAs($this->admin)
        ->get(ManifestFileResource::getUrl('index'))
        ->assertSuccessful();
});

it('admin can edit a manifest file', function () {
    $manifestFile = ManifestFile::factory()->create();

    $this->actingAs($this->admin);

    livewire(ManifestFileResource\Pages\EditManifestFile::class, ['record' => $manifestFile->getKey()])
        ->fillForm(['description' => 'Updated description'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(ManifestFile::class, [
        'id' => $manifestFile->getKey(),
        'description' => 'Updated description',
    ]);
});

it('admin can delete a manifest file', function () {
    $manifestFile = ManifestFile::factory()->create();

    $this->actingAs($this->admin);

    livewire(ManifestFileResource\Pages\EditManifestFile::class, ['record' => $manifestFile->getKey()])
        ->callAction(\Filament\Actions\DeleteAction::class);

    $this->assertModelMissing($manifestFile);
});

// --------------------------------------------
// Tests for User Role
// --------------------------------------------

it('user can access the list page', function () {
    $this->actingAs($this->user)
        ->get(ManifestFileResource::getUrl('index'))
        ->assertSuccessful();
});

it('user can view only their own manifest files', function () {
    $ownManifestFile = ManifestFile::factory()->create(['user_id' => $this->user->id]);
    $otherManifestFile = ManifestFile::factory()->create();

    $this->actingAs($this->user);

    livewire(ManifestFileResource\Pages\ListManifestFiles::class)
        ->assertCanSeeTableRecords([$ownManifestFile])
        ->assertCanNotSeeTableRecords([$otherManifestFile]);
});

it('user cannot edit a manifest file', function () {
    $manifestFile = ManifestFile::factory()->create();

    $this->actingAs($this->user)
        ->get(ManifestFileResource::getUrl('edit', ['record' => $manifestFile]))
        ->assertForbidden();
});

it('user can delete a manifest file', function () {
    $manifestFile = ManifestFile::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user);

    livewire(ManifestFileResource\Pages\EditManifestFile::class, ['record' => $manifestFile->getKey()])
        ->callAction(\Filament\Actions\DeleteAction::class);
});
