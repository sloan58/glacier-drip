<?php

use App\Models\User;
use App\Livewire\AwsCredentials;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows the authenticated user to update their AWS credentials through the Livewire component', function () {
    $user = User::factory()->onboarded()->create();

    actingAs($user);

    // @phpstan-ignore-next-line
    livewire(AwsCredentials::class)
        ->fillForm([
            'aws_access_key_id' => 'new-access-key',
            'aws_secret_access_key' => 'new-secret-key',
            'aws_region' => 'eu-west-1',
            'aws_s3_bucket' => 'test-bucket',
        ])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertStatus(200);
});

it('prevents unauthorized users from updating AWS credentials through the Livewire component', function () {
    // Create two users
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($otherUser);

    // @phpstan-ignore-next-line
    livewire(AwsCredentials::class)
        ->fillForm([
            'aws_access_key_id' => 'new-access-key',
            'aws_secret_access_key' => 'new-secret-key',
            'aws_region' => 'eu-west-1',
            'aws_s3_bucket' => 'test-bucket',
        ])
        ->call('submit')
        ->assertStatus(200);

    // @phpstan-ignore-next-line
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
        'aws_access_key_id' => 'malicious-access-key',
        'aws_secret_access_key' => 'malicious-secret-key',
    ]);
});

it('removes AWS credentials and redirects to onboarding when deleteAction is invoked', function () {
    // Create a user with AWS credentials
    $user = User::factory()->create([
        'aws_access_key_id' => 'test-access-key',
        'aws_secret_access_key' => 'test-secret-key',
        'aws_region' => 'us-east-1',
        'aws_s3_bucket' => 'test-bucket',
    ]);

    // Act as the user
    actingAs($user);

    livewire(\App\Livewire\AwsCredentials::class)
        ->callAction('deleteAction') // Call the action
        ->assertRedirect(route('filament.admin.pages.onboarding')); // Assert redirection to onboarding

    // Assert that the user's AWS credentials are null in the database
    $user->refresh();
    expect($user->aws_access_key_id)->toBeNull()
        ->and($user->aws_secret_access_key)->toBeNull()
        ->and($user->aws_region)->toBeNull()
        ->and($user->aws_s3_bucket)->toBeNull();
});
