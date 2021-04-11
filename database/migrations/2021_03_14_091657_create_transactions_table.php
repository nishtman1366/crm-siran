<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Profiles\Profile::class)->references('id')->on('profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(\App\Models\Transactions\Date::class)->references('id')->on('transactions_dates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('total_count')->default(0);
            $table->unsignedInteger('total_amount')->default(0);
            $table->unsignedInteger('total_wage')->default(0);

            $table->unsignedInteger('charge_count')->default(0);
            $table->unsignedBigInteger('charge_amount')->default(0);
            $table->unsignedBigInteger('charge_wage')->default(0);

            $table->unsignedInteger('bills_count')->default(0);
            $table->unsignedBigInteger('bills_amount')->default(0);
            $table->unsignedBigInteger('bills_wage')->default(0);

            $table->unsignedInteger('buys_wage')->default(0);
            $table->unsignedBigInteger('buys_amount')->default(0);
            $table->unsignedInteger('buys_count')->default(0);

            $table->unsignedInteger('balance_wage')->default(0);
            $table->unsignedBigInteger('balance_amount')->default(0);
            $table->unsignedInteger('balance_count')->default(0);

            $table->unsignedInteger('under_50_count')->default(0);
            $table->unsignedInteger('under_50_amount')->default(0);
            $table->unsignedInteger('under_50_wage')->default(0);

            $table->unsignedInteger('under_100_count')->default(0);
            $table->unsignedInteger('under_100_amount')->default(0);
            $table->unsignedInteger('under_100_wage')->default(0);

            $table->unsignedInteger('under_150_count')->default(0);
            $table->unsignedInteger('under_150_amount')->default(0);
            $table->unsignedInteger('under_150_wage')->default(0);

            $table->unsignedInteger('under_200_count')->default(0);
            $table->unsignedInteger('under_200_amount')->default(0);
            $table->unsignedInteger('under_200_wage')->default(0);

            $table->unsignedInteger('under_250_count')->default(0);
            $table->unsignedInteger('under_250_amount')->default(0);
            $table->unsignedInteger('under_250_wage')->default(0);

            $table->unsignedInteger('over_250_count')->default(0);
            $table->unsignedInteger('over_250_amount')->default(0);
            $table->unsignedInteger('over_250_wage')->default(0);

            $table->foreignIdFor(\App\Models\Transactions\Status::class)->nullable()->references('id')->on('transactions_statuses')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('transactions');
    }
}
