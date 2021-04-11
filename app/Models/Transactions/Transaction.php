<?php

namespace App\Models\Transactions;

use App\Models\Profiles\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id', 'date_id',

        'total_count', 'total_amount', 'total_wage',
        'charge_count', 'charge_amount', 'charge_wage',
        'bills_count', 'bills_amount', 'bills_wage',
        'buys_wage', 'buys_amount', 'buys_count',
        'balance_wage', 'balance_amount', 'balance_count',
        'under_50_count', 'under_50_amount', 'under_50_wage',
        'under_100_count', 'under_100_amount', 'under_100_wage',
        'under_150_count', 'under_150_amount', 'under_150_wage',
        'under_200_count', 'under_200_amount', 'under_200_wage',
        'under_250_count', 'under_250_amount', 'under_250_wage',
        'over_250_count', 'over_250_amount', 'over_250_wage',

        'status_id'];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }
}
