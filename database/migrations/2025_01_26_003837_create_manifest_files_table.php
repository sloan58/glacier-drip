<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manifest_files', function (Blueprint $table) {
            $table->id();
            $table->string('archive_id')->default('-');
            $table->string('description')->default('-');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('sha256_tree_hash')->default('-');
            $table->timestamp('creation_date')->nullable();
            $table->foreignIdFor(\App\Models\User::class);
            $table->string('status')->default('processing');
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->unique(['archive_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifest_files');
    }
};
