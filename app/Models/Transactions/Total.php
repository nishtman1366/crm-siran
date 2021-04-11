<?php

namespace App\Models\Transactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Total extends Model
{
    use HasFactory;

    protected $table = 'transactions_totals';

    protected $fillable = [
        'user_id', 'date_id', 'profiles_count', 'success', 'error',
        'total_amount', 'total_count', 'total_wage',
        'charge_amount', 'charge_count', 'charge_wage',
        'bills_amount', 'bills_count', 'bills_wage',
        'buys_amount', 'buys_count', 'buys_wage',
        'balance_amount', 'balance_count', 'balance_wage',
        'under_50_amount', 'under_50_count', 'under_50_wage',
        'under_100_amount', 'under_100_count', 'under_100_wage',
        'under_150_amount', 'under_150_count', 'under_150_wage',
        'under_200_amount', 'under_200_count', 'under_200_wage',
        'under_250_amount', 'under_250_count', 'under_250_wage',
        'over_250_amount', 'over_250_count', 'over_250_wage',
        'superuser_wage', 'admin_wage', 'agent_wage', 'marketer_wage'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function date()
    {
        return $this->belongsTo(Date::class, 'date_id', 'id');
    }
}
