<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expert & Agency Ecosystem — Database Schema
 *
 * Extends the existing appointments table with full lifecycle support
 * and creates all expert-specific tables.  All FK references use the
 * shared `users` and `experts` tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Expert Profiles (rich data beyond the base experts table) ──────
        Schema::create('expert_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->unique()->constrained('experts')->cascadeOnDelete();
            $table->string('agency_name')->nullable();
            $table->string('specialization')->nullable();   // maps to experts.specialty alias
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->text('certifications')->nullable();     // JSON / free text
            $table->text('availability_schedule')->nullable(); // JSON schedule
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('city')->nullable();
            $table->string('country', 100)->default('Pakistan');
            $table->enum('account_type', ['individual', 'agency'])->default('individual');
            $table->enum('approval_status', ['pending', 'approved', 'suspended', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('approval_status');
            $table->index('account_type');
        });

        // ── 2. Expert Specializations (tag / taxonomy) ────────────────────────
        Schema::create('expert_specializations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->constrained('experts')->cascadeOnDelete();
            $table->string('name');           // e.g. "Crop Diseases", "Soil Science"
            $table->string('level', 50)->default('intermediate'); // beginner|intermediate|expert
            $table->timestamps();

            $table->index('expert_id');
            $table->index('name');
        });

        // ── 3. Extend appointments table with full lifecycle ──────────────────
        Schema::table('appointments', function (Blueprint $table) {
            // Widen status enum to full lifecycle
            // Note: MySQL ALTER ENUM is additive-safe
            // We replace existing column (status already exists as pending/confirmed/completed/cancelled)
            // Add new values requested/accepted/rejected/rescheduled + keep existing
            $table->string('topic', 255)->nullable()->after('notes');
            $table->string('meeting_link', 500)->nullable()->after('topic');
            $table->timestamp('reschedule_requested_at')->nullable()->after('meeting_link');
            $table->timestamp('accepted_at')->nullable()->after('reschedule_requested_at');
            $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            $table->timestamp('completed_at')->nullable()->after('rejected_at');
            $table->string('reject_reason', 500)->nullable()->after('completed_at');
        });

        // ── 4. Appointment Status History ─────────────────────────────────────
        Schema::create('appointment_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('from_status', 50);
            $table->string('to_status', 50);
            $table->text('notes')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->index('appointment_id');
            $table->index('changed_by');
        });

        // ── 5. Appointment Reschedules ─────────────────────────────────────────
        Schema::create('appointment_reschedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('original_scheduled_at');
            $table->dateTime('proposed_scheduled_at');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index('appointment_id');
        });

        // ── 6. Forum Expert Responses ─────────────────────────────────────────
        Schema::create('forum_expert_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_reply_id')->constrained('forum_replies')->cascadeOnDelete();
            $table->foreignId('expert_id')->constrained('experts')->cascadeOnDelete();
            $table->boolean('is_expert_advice')->default(true);
            $table->text('recommendation')->nullable();   // optional structured advice
            $table->unsignedInteger('helpful_votes')->default(0);
            $table->timestamps();

            $table->unique('forum_reply_id');             // one expert response per reply
            $table->index('expert_id');
        });

        // ── 7. Expert Notification Logs (in addition to Laravel's notifications) ─
        Schema::create('expert_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->constrained('experts')->cascadeOnDelete();
            $table->string('type', 100);        // appointment.new | forum.mention | admin.announcement
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->json('data')->nullable();   // arbitrary payload
            $table->foreignId('related_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['expert_id', 'is_read']);
            $table->index('type');
        });

        // ── 8. Forum replies — add expert badge column ────────────────────────
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->boolean('is_expert_reply')->default(false)->after('body');
            $table->foreignId('expert_id')->nullable()->after('is_expert_reply')
                  ->constrained('experts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Remove added columns in reverse order
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropForeign(['expert_id']);
            $table->dropColumn(['is_expert_reply', 'expert_id']);
        });

        Schema::dropIfExists('expert_notification_logs');
        Schema::dropIfExists('forum_expert_responses');
        Schema::dropIfExists('appointment_reschedules');
        Schema::dropIfExists('appointment_status_history');

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'topic', 'meeting_link', 'reschedule_requested_at',
                'accepted_at', 'rejected_at', 'completed_at', 'reject_reason',
            ]);
        });

        Schema::dropIfExists('expert_specializations');
        Schema::dropIfExists('expert_profiles');
    }
};
