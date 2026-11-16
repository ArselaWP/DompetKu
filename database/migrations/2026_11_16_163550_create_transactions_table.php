<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wallet_id')
                ->constrained('wallets')
                ->restrictOnDelete();

            $table->foreignId('related_wallet_id')
                ->nullable()
                ->constrained('wallets')
                ->restrictOnDelete();

            $table->string('type', 20);

            $table->unsignedBigInteger('amount');

            $table->unsignedBigInteger('balance_before');
            $table->unsignedBigInteger('balance_after');

            $table->unsignedBigInteger('related_balance_before')
                ->nullable();

            $table->unsignedBigInteger('related_balance_after')
                ->nullable();

            $table->string('idempotency_key', 100)->unique();

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['wallet_id', 'created_at']);
            $table->index(['related_wallet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
