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
