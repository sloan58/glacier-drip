<?php

use App\Models\User;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

it('allows authenticated users to access the Filament admin panel', function () {
    // Create a user with access to the admin panel
    $user = User::factory()->onboarded()->create();

    // Simulate logging in as the user
    actingAs($user);

    // Send a GET request to the Filament admin route
    $response = get('/');

    // Assert the response is successful
    $response->assertStatus(200);
});

it('redirects unauthenticated users to the login page', function () {
    // Send a GET request to the Filament admin route without authentication
    $response = get('/');

    // Assert the response redirects to the login page
    $response->assertRedirect('/login');
});
