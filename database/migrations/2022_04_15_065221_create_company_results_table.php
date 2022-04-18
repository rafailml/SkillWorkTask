<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('domain');
            $table->string('name')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('job_finished')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'domain']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies_results');
    }
};
