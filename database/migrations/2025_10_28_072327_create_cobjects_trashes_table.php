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
    Schema::create('cobjects_trashes', function (Blueprint $table) {
      $table->id();
      $table->morphs('owner'); // resource owner
      $table->string('filename')->unique(); // Capital letter DMC-...
      $table->string('path');
      $table->text('remarks')->nullable();
      $table->date("expired_at");
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cobjects_trashes');
  }
};
