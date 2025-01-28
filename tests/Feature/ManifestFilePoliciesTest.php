<?php

use App\Models\User;
use App\Models\ManifestFile;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->userRole = Role::firstOrCreate(['name' => 'user']);

    // Create test users
    $this->admin = User::factory()->onboarded()->create();
    $this->admin->assignRole($this->adminRole);

    $this->user = User::factory()->onboarded()->create();
    $this->user->assignRole($this->userRole);
});

it('allows admin to view any manifest files', function () {
    $this->assertTrue($this->admin->can('viewAny', ManifestFile::class));
});

it('allows user to view any manifest files', function () {
    $this->assertTrue($this->user->can('viewAny', ManifestFile::class));
});

it('allows admin to view specific manifest files', function () {
    $manifestFile = ManifestFile::factory()->create();
    $this->assertTrue($this->admin->can('view', $manifestFile));
});

it('allows user to view their own manifest files', function () {
    $manifestFile = ManifestFile::factory()->create(['user_id' => $this->user->id]);
    $this->assertTrue($this->user->can('view', $manifestFile));
});

it('prevents user from viewing others manifest files', function () {
    $otherUser = User::factory()->create();
    $manifestFile = ManifestFile::factory()->create(['user_id' => $otherUser->id]);
    $this->assertFalse($this->user->can('view', $manifestFile));
});

it('allows admin to create manifest files', function () {
    $this->assertTrue($this->admin->can('create', ManifestFile::class));
});

it('allows user to create manifest files', function () {
    $this->assertTrue($this->user->can('create', ManifestFile::class));
});

it('allows admin to update any manifest files', function () {
    $manifestFile = ManifestFile::factory()->create();
    $this->assertTrue($this->admin->can('update', $manifestFile));
});

it('allows user to update their own manifest files', function () {
    $manifestFile = ManifestFile::factory()->create(['user_id' => $this->user->id]);
    $this->assertTrue($this->user->can('update', $manifestFile));
});

it('prevents user from updating others manifest files', function () {
    $otherUser = User::factory()->create();
    $manifestFile = ManifestFile::factory()->create(['user_id' => $otherUser->id]);
    $this->assertFalse($this->user->can('update', $manifestFile));
});

it('allows admin to delete any manifest files', function () {
    $manifestFile = ManifestFile::factory()->create();
    $this->assertTrue($this->admin->can('delete', $manifestFile));
});

it('allows user to delete their own manifest files', function () {
    $manifestFile = ManifestFile::factory()->create(['user_id' => $this->user->id]);
    $this->assertTrue($this->user->can('delete', $manifestFile));
});

it('prevents user from deleting others manifest files', function () {
    $otherUser = User::factory()->create();
    $manifestFile = ManifestFile::factory()->create(['user_id' => $otherUser->id]);
    $this->assertFalse($this->user->can('delete', $manifestFile));
});
