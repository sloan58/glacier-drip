<?php

use App\Http\Middleware\OnboardingMiddleware;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('redirects users with needs_aws_credentials=true to the onboarding route', function () {
    // Create a user with needs_aws_credentials set to true
    $user = User::factory()->create();

    // Simulate logging in as the user
    actingAs($user);

    // Attempt to access the protected route
    $response = get('/');

    // Assert the user is redirected to the onboarding route
    $response->assertRedirect('/onboarding');
});

it('allows users with needs_aws_credentials=false to access the intended route', function () {
    // Create a user with needs_aws_credentials set to false
    $user = User::factory()->onboarded()->create();

    // Simulate logging in as the user
    actingAs($user);

    // Attempt to access the protected route
    $response = get('/');

    // Assert the user can access the intended route
    $response->assertOk();
});

it('redirects unauthenticated users to the login page', function () {
    // Attempt to access the protected route without authentication
    $response = get('/');

    // Assert the user is redirected to the login page
    $response->assertRedirect('/login');
});
