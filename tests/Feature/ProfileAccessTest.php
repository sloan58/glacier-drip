<?php

use App\Models\User;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows authenticated users to access their profile page', function () {
    // Create a user
    $user = User::factory()->onboarded()->create();

    // Simulate logging in as the user
    actingAs($user);

    // Send a GET request to the profile page
    $response = get('/profile');

    // Assert the response is successful
    $response->assertStatus(200);
});

it('redirects unauthenticated users to the login page when accessing the profile page', function () {
    // Send a GET request to the profile page without authentication
    $response = get('/profile');

    // Assert the response redirects to the login page
    $response->assertRedirect('/login');
});
