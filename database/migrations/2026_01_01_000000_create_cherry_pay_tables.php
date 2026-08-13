<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('external_reference')->unique();
            $table->string('wallet_address')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('customer_name');
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->string('status')->default('sent');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_intents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('reference')->index();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3);
            $table->string('status')->default('created')->index();
            $table->text('qr_payload_url');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_intent_id')->constrained()->cascadeOnDelete();
            $table->string('network');
            $table->string('asset');
            $table->string('transaction_hash')->unique();
            $table->string('payer_address')->nullable();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3);
            $table->string('status')->index();
            $table->unsignedBigInteger('block_number')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('reconciliations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_intent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('settlement_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('confidence_score', 5, 4);
            $table->string('match_reason');
            $table->string('status')->index();
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->index();
            $table->string('subject_type');
            $table->string('subject_id');
            $table->json('payload');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('reconciliations');
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('payment_intents');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('merchants');
    }
};
