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
        Schema::create('arisan_history_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arisan_history_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->date('date_paid')->nullable();
            $table->string('status_paid')->comment('skip, cancel, paid, unpaid')->nullable();
            $table->string('status_active')->comment('active, inactive')->nullable();
            $table->unsignedBigInteger('nominal_paid')->nullable();
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
        Schema::dropIfExists('arisan_history_details');
    }
};
