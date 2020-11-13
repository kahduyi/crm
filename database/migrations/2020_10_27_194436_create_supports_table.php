<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('lastname')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 13)->unique()->nullable();
            $table->string('PersonnelCode', 10)->unique();
            $table->enum('type_position', \App\Models\Support::TYPES);
            $table->string('avatar')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('dateBirth')->nullable();
            $table->string('user_code',6)->nullable();
            $table->unsignedBigInteger('avidi_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supports');
    }
}
