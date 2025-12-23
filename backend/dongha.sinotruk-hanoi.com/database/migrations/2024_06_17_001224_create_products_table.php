<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->unique();
			$table->integer('price')->default(0);
			$table->integer('price_bulk')->default(0);
			$table->integer('total')->default(0);
			$table->string('type');
			$table->timestamps();
			$table->softDeletes();
			$table->string('code')->nullable();
			$table->string('image')->nullable();
			$table->string('mansx')->nullable();
			$table->integer('category_id')->nullable();
			$table->float('weight', 10, 0)->nullable();
			$table->integer('order_pending')->unsigned()->default(0);
			$table->string('note')->nullable();
			$table->integer('min')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}
