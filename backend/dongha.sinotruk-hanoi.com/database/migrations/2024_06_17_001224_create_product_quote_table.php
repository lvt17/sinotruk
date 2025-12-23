<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductQuoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_quote', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('quote_id');
			$table->integer('product_id');
			$table->bigInteger('price');
			$table->integer('count')->nullable()->default(1);
			$table->unique(['quote_id','product_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_quote');
	}

}
