<?php

use Aws\Sdk;
use Aws\Sdk as AwsSdk;
use App\Models\User;
use App\Providers\AWSServiceProvider;

it( 'registers AwsSdk singleton in the service container with user credentials', function () {
    // Create a user using the factory
    $user = User::factory()->create([
        'aws_access_key_id' => 'mock-access-key-id',
        'aws_secret_access_key' => 'mock-secret-access-key',
        'aws_region' => 'mock-region',
    ]);

    // Authenticate the created user
    $this->actingAs($user);

    // Register the AWSServiceProvider
    $this->app->register(AWSServiceProvider::class);

    // Resolve the AwsSdk singleton from the service container
    $awsSdk = $this->app->make(AwsSdk::class);

    // Verify that the resolved instance is an AwsSdk and has the expected configuration
    expect($awsSdk)->toBeInstanceOf(AwsSdk::class);

    // Verify the configuration passed to AwsSdk
    $reflectedClass = new ReflectionClass(app(Sdk::class));
    $reflection = $reflectedClass->getProperty('args');
    $reflection->setAccessible(true);

    $config = $reflection->getValue(app(Sdk::class));

    expect($config['credentials']['key'])->toEqual($user->aws_access_key_id)
        ->and($config['credentials']['secret'])->toEqual($user->aws_secret_access_key)
        ->and($config['region'])->toEqual($user->aws_region)
        ->and($config['version'])->toEqual('latest');
});
