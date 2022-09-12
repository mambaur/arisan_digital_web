<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('name');
            $table->string('no_telp')->nullable();
            $table->string('no_whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->date('date_paid')->nullable();
            $table->string('status_paid')->comment('skip, cancel, paid, unpaid')->nullable();
            $table->unsignedBigInteger('nominal_paid')->nullable();
            $table->string('status_active');
            $table->boolean('is_owner')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
};
