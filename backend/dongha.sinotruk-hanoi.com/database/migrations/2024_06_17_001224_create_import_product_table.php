<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImportProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('import_product', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('import_id');
			$table->integer('product_id');
			$table->bigInteger('price');
			$table->integer('count')->default(1);
			$table->unique(['import_id','product_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('import_product');
	}

}
