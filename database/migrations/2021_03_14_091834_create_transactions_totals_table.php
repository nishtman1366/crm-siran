<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Transactions\Date::class)->references('id')->on('transactions_dates')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('profiles_count')->default(0);
            $table->unsignedBigInteger('success')->default(0);
            $table->unsignedBigInteger('error')->default(0);

            $table->unsignedBigInteger('total_amount')->default(0);
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('total_wage')->default(0);

            $table->unsignedBigInteger('charge_amount')->default(0);
            $table->unsignedInteger('charge_count')->default(0);
            $table->unsignedInteger('charge_wage')->default(0);

            $table->unsignedBigInteger('bills_amount')->default(0);
            $table->unsignedInteger('bills_count')->default(0);
            $table->unsignedInteger('bills_wage')->default(0);

            $table->unsignedBigInteger('buys_amount')->default(0);
            $table->unsignedInteger('buys_count')->default(0);
            $table->unsignedInteger('buys_wage')->default(0);

            $table->unsignedBigInteger('balance_amount')->default(0);
            $table->unsignedInteger('balance_count')->default(0);
            $table->unsignedInteger('balance_wage')->default(0);

            $table->unsignedBigInteger('under_50_amount')->default(0);
            $table->unsignedInteger('under_50_count')->default(0);
            $table->unsignedInteger('under_50_wage')->default(0);

            $table->unsignedBigInteger('under_100_amount')->default(0);
            $table->unsignedInteger('under_100_count')->default(0);
            $table->unsignedInteger('under_100_wage')->default(0);

            $table->unsignedBigInteger('under_150_amount')->default(0);
            $table->unsignedInteger('under_150_count')->default(0);
            $table->unsignedInteger('under_150_wage')->default(0);

            $table->unsignedBigInteger('under_200_amount')->default(0);
            $table->unsignedInteger('under_200_count')->default(0);
            $table->unsignedInteger('under_200_wage')->default(0);

            $table->unsignedBigInteger('under_250_amount')->default(0);
            $table->unsignedInteger('under_250_count')->default(0);
            $table->unsignedInteger('under_250_wage')->default(0);

            $table->unsignedBigInteger('over_250_amount')->default(0);
            $table->unsignedInteger('over_250_count')->default(0);
            $table->unsignedInteger('over_250_wage')->default(0);

            $table->unsignedBigInteger('superuser_wage')->default(0);
            $table->unsignedBigInteger('admin_wage')->default(0);
            $table->unsignedBigInteger('agent_wage')->default(0);
            $table->unsignedBigInteger('marketer_wage')->default(0);

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
        Schema::dropIfExists('totals');
    }
}
