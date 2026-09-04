<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('umkm_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('address', 255);
            $table->string('landmark', 255)->nullable();

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->time('start_time');
            $table->time('end_time');

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_locations');
    }
};