<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('imports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('customer_id');
			$table->bigInteger('money');
			$table->boolean('isVAT')->default(0);
			$table->bigInteger('money_with_vat');
			$table->bigInteger('debt');
			$table->bigInteger('total');
			$table->boolean('cash')->default(1);
			$table->timestamps();
			$table->boolean('isProductError')->nullable()->default(0);
			$table->text('content', 65535)->nullable();
			$table->decimal('discount', 10)->nullable();
			$table->string('note')->nullable();
			$table->boolean('lock')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('imports');
	}

}
