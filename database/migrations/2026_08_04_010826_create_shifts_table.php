<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('kas_awal', 12, 2);
            $table->decimal('kas_akhir_sistem', 12, 2)->nullable();
            $table->decimal('kas_fisik', 12, 2)->nullable();
            $table->decimal('selisih', 12, 2)->nullable();
            $table->timestamp('mulai')->useCurrent();
            $table->timestamp('selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
