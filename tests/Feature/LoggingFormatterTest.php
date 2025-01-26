<?php

use App\Logging\CustomFormatter;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;

it('applies the IntrospectionProcessor to include class and method in log entries', function () {
    // Create a Monolog logger instance with a test handler
    $handler = new TestHandler(Level::Debug);
    $logger = new Logger('test');
    $logger->pushHandler($handler);

    // Apply the custom formatter
    $customFormatter = new CustomFormatter();
    $customFormatter($logger);

    // Log a test message
    $logger->info('Test log message');

    // Retrieve the logged record
    $records = $handler->getRecords();
    expect($records)->toHaveCount(1);

    // Check the record has the introspection data
    $record = $records[0];
    expect($record['extra']['class'])->toContain('LoggingFormatterTest')
        ->and($record['extra']['function'])->toContain('closure')
        ->and($record['message'])->toBe('Test log message'); // The namespace for this test file
    // The function is an anonymous closure
    // Ensure the log message is correct
});
