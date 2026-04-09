<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proovit_token_reservations', function (Blueprint $table): void {
            $table->id();
            $table->string('fingerprint')->unique();
            $table->string('reservation_id')->nullable()->index();
            $table->string('status')->nullable()->index();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proovit_token_reservations');
    }
};
