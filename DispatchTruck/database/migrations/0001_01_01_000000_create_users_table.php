<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ✅ Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'role_name' => 'admin',
                'description' => 'Full system access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'dispatcher',
                'description' => 'Create assignments and run dispatch algorithm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'driver',
                'description' => 'Truck driver - view assignments and update status',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'client',
                'description' => 'External client - create and track delivery requests',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ✅ Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Client specific fields
            $table->string('company_name')->nullable(); // For business clients
            $table->string('address')->nullable();

            // 🔥 Role relationship
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['email', 'email_verified_at']);
            $table->index(['created_at', 'updated_at']);
            $table->index('company_name');
            $table->index('city');
            $table->index('status');
        });

        // ✅ Password Reset Tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->index('token');
            $table->index('created_at');
        });

        // ✅ Sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->index('ip_address');
            $table->index('user_agent');
            $table->index(['last_activity', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};