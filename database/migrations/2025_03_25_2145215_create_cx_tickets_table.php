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
        Schema::create('cx_tickets', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['service', 'repair', 'installation']);
            $table->string('product');
            $table->string('model');
            $table->string('work_order_no')->unique();
            $table->string('service_center');
            $table->string('warranty_status');
            $table->date('sold_date');
            
            $table->string('customer_name');
            $table->text('customer_address');
            $table->string('customer_contact_01');
            $table->string('customer_contact_02')->nullable();
            
            $table->string('technician_name');
            $table->string('technician_contact');
            $table->string('supervisor_name');
            $table->string('supervisor_contact');

            $table->string('status');
            $table->string('creator');

            $table->string('satisfaction_rate')->nullable();
            $table->string('satisfaction_reasons')->nullable();
            $table->string('dis_satisfaction_reasons')->nullable();
            $table->string('cancelling_reasons')->nullable();

            $table->string('closed_by')->nullable();
            $table->string('surveyed_by')->nullable();
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
        Schema::dropIfExists('cx_tickets');
    }
};
