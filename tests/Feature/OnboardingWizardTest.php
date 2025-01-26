<?php

use Aws\Sdk;
use Aws\S3\S3Client;
use App\Models\User;
use function Pest\Livewire\livewire;

it('completes the onboarding wizard successfully while mocking AWS API', function () {
    // Mock the AWS SDK and S3 client
    $mockedS3Client = Mockery::mock(S3Client::class);
    $mockedS3Client->shouldReceive('listBuckets')
        ->once()
        ->andReturn(['Buckets' => []]); // Mock listBuckets call for step 1

    $mockedS3Client->shouldReceive('createBucket')
        ->once()
        ->andReturn([]); // Mock createBucket call for step 3

    $mockedSdk = Mockery::mock(Sdk::class);
    $mockedSdk->shouldReceive('createS3')
        ->times(limit: 2)
        ->andReturn($mockedS3Client);

    // Bind the mocked SDK to the container
    $this->app->instance(Sdk::class, $mockedSdk);

    // Create and authenticate a user
    $user = User::factory()->create(); // Defaults to empty AWS credentials
    $this->actingAs($user);

    // Interact with the OnboardingWizard component
    livewire(\App\Filament\Pages\Onboarding::class)
        // Step 1: Fill AWS credentials and move to the next step
        ->fillForm([
            'aws_access_key_id' => 'valid-access-key',
            'aws_secret_access_key' => 'valid-secret-key',
        ])
        ->goToWizardStep(step: 2)
        ->assertHasNoFormErrors()
        ->assertWizardCurrentStep(step: 2)

        // Step 2: Fill the AWS region field and move to the next step
        ->fillForm([
            'aws_region' => 'us-east-1',
        ])
        ->goToWizardStep(step: 3)
        ->assertHasNoFormErrors()
        ->assertWizardCurrentStep(step: 3)

        // Step 3: Fill the AWS S3 bucket name and complete the wizard
        ->fillForm([
            'aws_s3_bucket' => 'test-bucket',
        ])
        ->call('submit')
        ->assertHasNoFormErrors()
        ->assertNotified();
});

it('redirects onboarded users to the dashboard', function () {
    // Create a user with the "onboarded" state
    $user = User::factory()->onboarded()->create();
    $this->actingAs($user);

    // Attempt to access the onboarding wizard
    livewire(\App\Filament\Pages\Onboarding::class)
        ->assertRedirect(route('filament.admin.pages.dashboard')); // Assert redirection to the dashboard
});

it('handles listBuckets exception and clears AWS credentials', function () {
    // Mock the AWS SDK and S3 client
    $mockedS3Client = Mockery::mock(S3Client::class);
    $mockedS3Client->shouldReceive('listBuckets')
        ->once()
        ->andThrow(new Exception('AWS error: Invalid credentials')); // Simulate an exception

    $mockedSdk = Mockery::mock(Sdk::class);
    $mockedSdk->shouldReceive('createS3')
        ->once()
        ->andReturn($mockedS3Client);

    // Bind the mocked SDK to the container
    $this->app->instance(Sdk::class, $mockedSdk);

    // Create and authenticate a user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Interact with the OnboardingWizard component
    livewire(\App\Filament\Pages\Onboarding::class)
        // Step 1: Fill AWS credentials and attempt to move to the next step
        ->fillForm([
            'aws_access_key_id' => 'invalid-access-key',
            'aws_secret_access_key' => 'invalid-secret-key',
        ])
        ->goToWizardStep(step: 2)
        ->assertHasErrors([
            'data.aws_access_key_id' => 'Invalid AWS credentials.',
            'data.aws_secret_access_key' => 'Invalid AWS credentials.',
        ]); // Ensure error messages are added

    // Assert that the user's credentials were cleared
    $user->refresh();
    expect($user->aws_access_key_id)->toBe('')
        ->and($user->aws_secret_access_key)->toBe('');
});

it('handles createBucket exception and adds an error message', function () {
    // Mock the AWS SDK and S3 client
    $mockedS3Client = Mockery::mock(S3Client::class);
    $mockedS3Client->shouldReceive('listBuckets')
        ->once()
        ->andReturn(['Buckets' => []]); // Mock successful listBuckets for earlier step
    $mockedS3Client->shouldReceive('createBucket')
        ->once()
        ->andThrow(new Exception('AWS error: Unable to create bucket')); // Simulate an exception

    $mockedSdk = Mockery::mock(Sdk::class);
    $mockedSdk->shouldReceive('createS3')
        ->times(limit: 2)
        ->andReturn($mockedS3Client);

    // Bind the mocked SDK to the container
    $this->app->instance(Sdk::class, $mockedSdk);

    // Mock the logger
    Log::shouldReceive('error')
        ->once()
        ->with('AWS error: Unable to create bucket'); // Ensure the error message is logged

    // Create and authenticate a user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Interact with the OnboardingWizard component
    livewire(\App\Filament\Pages\Onboarding::class)
        // Step 1: Fill AWS credentials and move to the next step
        ->fillForm([
            'aws_access_key_id' => 'valid-access-key',
            'aws_secret_access_key' => 'valid-secret-key',
        ])
        ->goToWizardStep(step: 2)
        ->fillForm([
            'aws_region' => 'us-east-1',
        ])
        ->goToWizardStep(step: 3)
        ->fillForm([
            'aws_s3_bucket' => 'invalid-bucket-name',
        ])
        ->call('submit') // Attempt to submit and trigger createBucket
        ->assertHasErrors([
            'data.aws_s3_bucket' => 'Invalid AWS S3 Bucket.', // Ensure error message is added
        ])
        ->assertSee('AWS S3 Bucket'); // Ensure no redirection to the dashboard
});
