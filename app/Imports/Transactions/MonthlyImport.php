<?php

namespace App\Imports\Transactions;

use App\Models\Profiles\Profile;
use App\Models\Transactions\Status;
use App\Models\Transactions\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MonthlyImport implements ToModel, WithStartRow
{
    private $date;

    /**
     * MonthlyImport constructor.
     * @param $date
     */
    public function __construct($date)
    {
        $this->date = $date;
    }


    public function model(array $row)
    {
        $profile = Profile::where('terminal_id', $row[0])->get()->first();
        if (!is_null($profile)) {
            $balance_wage = $row[41] ?? 0;
            $charge_wage = $row[42] ?? 0;
            $bills_wage = $row[43] ?? 0;

            $under_50_wage = $row[31] ?? 0;
            $under_100_wage = $row[32] ?? 0;
            $under_150_wage = $row[33] ?? 0;
            $under_200_wage = $row[34] ?? 0;
            $under_250_wage = $row[35] ?? 0;
            $over_250_wage = $row[40] ?? 0;

            $buys_wage = $under_50_wage + $under_100_wage + $under_150_wage + $under_200_wage + $under_250_wage + $over_250_wage;

            $totalWage = $charge_wage + $bills_wage + $balance_wage + $buys_wage;

            $transaction = [
                'profile_id' => $profile->id,
                'date_id' => $this->date,

                'total_count' => $row[9] ?? 0,
                'total_amount' => $row[10] ?? 0,
                'total_wage' => $totalWage,

                'charge_count' => $row[8] ?? 0,
                'charge_amount' => 0,
                'charge_wage' => $charge_wage,

                'bills_count' => $row[7] ?? 0,
                'bills_amount' => $row[6] ?? 0,
                'bills_wage' => $bills_wage,

                'buys_wage' => $buys_wage,
                'buys_amount' => $row[4] ?? 0,
                'buys_count' => $row[5] ?? 0,

                'balance_wage' => $balance_wage,
                'balance_amount' => 0,
                'balance_count' => $row[11] ?? 0,

                'under_50_count' => $row[21] ?? 0,
                'under_50_amount' => $row[26] ?? 0,
                'under_50_wage' => $under_50_wage,

                'under_100_count' => $row[22] ?? 0,
                'under_100_amount' => $row[27] ?? 0,
                'under_100_wage' => $under_100_wage,

                'under_150_count' => $row[23] ?? 0,
                'under_150_amount' => $row[28] ?? 0,
                'under_150_wage' => $under_150_wage,

                'under_200_count' => $row[24] ?? 0,
                'under_200_amount' => $row[29] ?? 0,
                'under_200_wage' => $under_200_wage,

                'under_250_count' => $row[25] ?? 0,
                'under_250_amount' => $row[30] ?? 0,
                'under_250_wage' => $under_250_wage,

                'over_250_count' => $row[39] ?? 0,
                'over_250_amount' => $row[38] ?? 0,
                'over_250_wage' => $over_250_wage,

                'status_id' => null
            ];
            return Transaction::create($transaction);
        }
        return null;
    }

    public function startRow(): int
    {
        return 2;
    }
}
