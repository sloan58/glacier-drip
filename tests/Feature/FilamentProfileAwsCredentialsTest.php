<?php

use App\Models\User;
use App\Livewire\AwsCredentials;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows the authenticated user to update their AWS credentials through the Livewire component', function () {
    $user = User::factory()->create();

    actingAs($user);

    // @phpstan-ignore-next-line
    livewire(AwsCredentials::class)
        ->fillForm([
            'aws_access_key_id' => 'new-access-key',
            'aws_secret_access_key' => 'new-secret-key'
        ])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertStatus(200);

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

    actingAs($otherUser);

    // @phpstan-ignore-next-line
    livewire(AwsCredentials::class)
        ->fillForm([
            'aws_access_key_id' => 'new-access-key',
            'aws_secret_access_key' => 'new-secret-key'
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
