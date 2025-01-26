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
            $table->string('archive_id')->unique();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('size');
            $table->string('sha256_tree_hash')->nullable();
            $table->timestamp('creation_date')->nullable();
            $table->timestamps();
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
