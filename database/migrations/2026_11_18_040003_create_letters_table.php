<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number')->nullable();
            $table->date('letter_date')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('audit_reference_group_id')->nullable()->constrained('audit_reference_groups')->nullOnDelete();
            $table->json('payload_json')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('letter_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
