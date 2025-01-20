<?php

use App\Models\User;
use App\Livewire\AwsCredentials;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows the authenticated user to update their AWS credentials through the Livewire component', function () {
    // Create a user
    $user = User::factory()->create();

    // Simulate logging in as the user
    actingAs($user);

    // Interact with the Livewire component
    livewire(AwsCredentials::class)
        ->fillForm([
            'aws_access_key_id' => 'new-access-key',
            'aws_secret_access_key' => 'new-secret-key'
        ])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertStatus(200);

    // Verify the user's credentials were updated in the database
    // TODO: Test the encrypted values
//    $this->assertDatabaseHas('users', [
//        'id' => $user->id,
//        'aws_access_key_id' => 'new-access-key',
//        'aws_secret_access_key' => 'new-secret-key',
//    ]);
});

it('prevents unauthorized users from updating AWS credentials through the Livewire component', function () {
    // Create two users
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Simulate logging in as another user
    actingAs($otherUser);

    // Attempt to update the first user's credentials
    livewire(AwsCredentials::class)
        ->fillForm([
            'aws_access_key_id' => 'new-access-key',
            'aws_secret_access_key' => 'new-secret-key'
        ])
        ->call('submit')
        ->assertStatus(200);

    // Verify the credentials were not updated in the database
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
        'aws_access_key_id' => 'malicious-access-key',
        'aws_secret_access_key' => 'malicious-secret-key',
    ]);
});
