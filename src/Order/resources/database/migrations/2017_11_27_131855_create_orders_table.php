<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $userModel = config('auth.providers.users.model');
        $usersTable = $userModel ? (new $userModel())->getTable() : 'users';

        $hasUsersTable = Schema::hasTable($usersTable);

        Schema::create('orders', function (Blueprint $table) use ($usersTable, $hasUsersTable) {
            $table->increments('id');
            $table->string('number', 32);
            $table->string('status', 32);
            if ($hasUsersTable) {
                $table->intOrBigIntBasedOnRelated('user_id', Schema::connection(null), $usersTable . '.id')->nullable();
            } else {
                // ponytail: users table is created later by the host app, assumes `increments` id; FK is added by 2026_10_12_000000_add_user_foreign_key_to_the_orders_table
                $table->unsignedInteger('user_id')->nullable();
            }
            $table->integer('billpayer_id')->unsigned()->nullable();
            $table->integer('shipping_address_id')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            if ($hasUsersTable) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on($usersTable);
            }

            $table->foreign('billpayer_id')
                  ->references('id')
                  ->on('billpayers');

            $table->foreign('shipping_address_id')
                  ->references('id')
                  ->on('addresses');
        });
    }

    public function down()
    {
        Schema::drop('orders');
    }
}
