<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // document_types: template of required docs (was 'documents' in Firestore)
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['restaurant', 'driver'])->default('restaurant');
            $table->boolean('is_required')->default(true);
            $table->boolean('is_enabled')->default(true);
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();
        });

        // document_verifications: per-vendor submission + status per document type
        // (was 'documents_verify' in Firestore)
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->string('file_path', 500)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('firebase_doc_id', 191)->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'document_type_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
        Schema::dropIfExists('document_types');
    }
};
