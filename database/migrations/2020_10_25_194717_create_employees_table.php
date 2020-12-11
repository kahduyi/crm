<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('lastname')->nullable();
            $table->string('password')->nullable();
            $table->string('mobile', 13)->unique()->nullable();
            $table->string('personnelCode', 9)->unique();
            $table->string('email')->nullable();
            $table->string('position')->nullable();
            $table->boolean('isAdmin')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('dateBirth')->nullable();
            $table->string('verify_code',6)->nullable();
            $table->string('ip')->nullable();
            $table->string('website')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();

            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
