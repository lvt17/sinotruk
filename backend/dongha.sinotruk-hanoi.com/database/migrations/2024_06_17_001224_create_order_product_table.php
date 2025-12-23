<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_product', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order_id');
			$table->integer('product_id');
			$table->float('price', 10, 0)->nullable();
			$table->integer('count')->default(1);
			$table->text('note', 65535);
			$table->string('nametq', 5)->default('1');
			$table->integer('hscode')->nullable();
			$table->float('weight', 10, 0)->default(0);
			$table->string('image')->nullable();
			$table->float('suggest_price', 10, 0)->default(0);
			$table->boolean('hightlight')->default(0);
			$table->integer('base_number');
			$table->unique(['order_id','product_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_product');
	}

}
