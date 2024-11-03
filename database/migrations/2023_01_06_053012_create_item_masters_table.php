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
        Schema::create('item_masters', function (Blueprint $table) {
            $table->id();
            $table->string('barcode',20)->unique();
            $table->string('descr',20);
            $table->string('pack',8);
            $table->decimal('units',12,3);
            $table->decimal('retail1',12,3);
            $table->decimal('retail2',12,3)->nullable();
            $table->decimal('retail3',12,3)->nullable();
            $table->decimal('disc_per',12,3)->nullable();
            $table->string('dept_code',2)->nullable();
            $table->string('class_id',6)->nullable();
            $table->tinyInteger('item_scale')->nullable();;
            $table->string('item_ref',20)->nullable();
            $table->string('nfplu',1)->nullable();
            $table->string('batch_no',10)->nullable();
            $table->decimal('qty',12,3)->nullable();;
            $table->decimal('cost',12,3)->nullable();;
            $table->decimal('unitqty1',12,3)->nullable();;
            $table->decimal('unitqty2',12,3)->nullable();;
            $table->decimal('unitqty3',12,3)->nullable();;
            $table->decimal('minqty',12,3)->nullable();;
            $table->tinyInteger('item_stock')->nullable();;
            $table->string('sup_code',10)->nullable();
            $table->decimal('sp_qty',12,3)->nullable();;
            $table->decimal('item_tax',12,3)->nullable();;
            $table->string('long_desc',50)->nullable();
            $table->string('tax_code',2)->nullable();
            $table->string('prod_id',20)->nullable();
            $table->boolean('multi_price')->nullable();
            $table->decimal('um_qty',12,3)->nullable();;
            $table->decimal('mast_unit_code',12,3)->nullable();;
            $table->char('item_type',1)->nullable();
            $table->boolean('open_item')->nullable();
            $table->string('more_inf1',30)->nullable();
            $table->string('more_inf2',30)->nullable();
            $table->string('more_inf3',30)->nullable();
            $table->char('group_code',2)->nullable();
            $table->char('arb_descr',40)->nullable();
            $table->char('stock_type',1)->nullable();
            $table->char('char',1)->nullable();
            $table->smallInteger('kprint_slevel')->nullable();
            $table->boolean('setmenu')->nullable();
            $table->char('loc_id',3)->nullable();
            $table->char('dis_code',3)->nullable();
            $table->string('serv_duration',10)->nullable();
            $table->string('item_image',10)->nullable();
            $table->decimal('sideitem_qty',12,3)->nullable();;
            
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
        Schema::dropIfExists('item_masters');
    }
};
