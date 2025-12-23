<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code')->unique();
			$table->string('name');
			$table->string('person')->nullable();
			$table->string('email')->nullable();
			$table->string('phone')->nullable();
			$table->string('address')->nullable();
			$table->bigInteger('money')->default(0);
			$table->boolean('bulk_customer')->default(1);
			$table->softDeletes();
			$table->timestamps();
			$table->boolean('user_id')->nullable();
			$table->decimal('monthly_discount', 10, 1)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customers');
	}

}
