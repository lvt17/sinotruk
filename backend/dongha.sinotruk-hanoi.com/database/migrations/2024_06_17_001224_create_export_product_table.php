<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExportProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('export_product', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('export_id');
			$table->integer('product_id');
			$table->bigInteger('price');
			$table->integer('count')->default(1);
			$table->unique(['export_id','product_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('export_product');
	}

}
