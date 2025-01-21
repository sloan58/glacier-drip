<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('aws_access_key_id')->nullable();
            $table->text('aws_secret_access_key')->nullable();
            $table->text('aws_region')->nullable();
            $table->text('aws_s3_bucket')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'aws_access_key_id',
                'aws_secret_access_key',
                'aws_region',
                'aws_s3_bucket'
            ]);
        });
    }
};
