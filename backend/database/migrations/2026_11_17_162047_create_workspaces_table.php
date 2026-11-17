<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->char('currency', 3);
            $table->string('timezone')->default('UTC');

            $table->unsignedTinyInteger('reminder_hour')
                ->default(9);

            $table->string('logo_path')->nullable();
            $table->string('brand_color', 7)->nullable();
            $table->string('reply_to_email')->nullable();
            $table->string('stripe_account_id')->nullable();

            $table->string('plan')->default('free');

            $table->timestamps();
        });

        Schema::create('workspace_user', function (Blueprint $table) {
            $table->foreignId('workspace_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('role', 30)->default('staff');

            $table->timestamps();

            $table->primary(['workspace_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_user');
        Schema::dropIfExists('workspaces');
    }
};
