<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // $file = new Filesystem;
        // $file->cleanDirectory('storage/app/private');

        Schema::create('cobjects', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner'); // resource owner
            $table->string('filename')->unique(); // Capital letter DMC-...
            $table->string('path');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobjects');
    }
};
