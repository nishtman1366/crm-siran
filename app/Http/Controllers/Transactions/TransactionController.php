<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Imports\Transactions\MonthlyImport;
use App\Models\Profiles\Profile;
use App\Models\Transactions\Date;
use App\Models\Transactions\Total;
use App\Models\Transactions\Transaction;
use App\Models\User;
use App\Models\Variables\Psp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::User();
        $dates = Date::orderBy('id', 'DESC')->get();

        $date = $request->query('dateId', $dates->first()->id);
        $adminId = (int)$request->query('adminId', 0);
        $agentId = (int)$request->query('agentId', 0);
        $marketerId = (int)$request->query('marketerId', 0);

        $transactions = Total::with('user')
            ->with('user.parent')
            ->with('user.parent.parent')
            ->with('date')
            ->where(function ($query) use ($user, $adminId, $agentId, $marketerId) {
                if ($adminId != 0 && ($agentId == 0 && $marketerId == 0)) {
                    $query->where('user_id', $adminId);
                    $query->orWhereHas('user', function ($userQuery) use ($adminId) {
                        $userQuery->where('parent_id', $adminId);
                        $userQuery->orWhereHas('parent', function ($parentQuery) use ($adminId) {
                            $parentQuery->where('parent_id', $adminId);
                        });
                    });
                } elseif ($agentId != 0 && $marketerId == 0) {
                    $query->where('user_id', $agentId);
                    $query->orWhereHas('user', function ($userQuery) use ($agentId) {
                        $userQuery->where('parent_id', $agentId);
                    });
                } elseif ($marketerId != 0) {
                    $query->where('user_id', $marketerId);
                } else {
                    if ($user->isAdmin()) {
                        $query->where('user_id', $user->id);
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                            $userQuery->orWhereHas('parent', function ($parentQuery) use ($user) {
                                $parentQuery->where('parent_id', $user->id);
                            });
                        });
                    }
                }
            })->where('date_id', $date)->orderBy('id', 'ASC')->get()->each(function (Model $transaction) {
                $items = $transaction->getAttributes();
                foreach ($items as $key => $value) {
                    if (is_integer($value)) {
                        $transaction->setAttribute($key, number_format($value));
                    }
                }
            });

        $agents = User::with('parent')->where('level', 'AGENT')->get();
        $marketers = User::with('parent')->with('parent.parent')->where('level', 'MARKETER')->get();
        $admins = User::where('level', 'ADMIN')->get();

        return Inertia::render('Dashboard/Transactions/List', [
            'transactions' => $transactions,
            'dates' => $dates,
            'admins' => $admins,
            'agents' => $agents,
            'marketers' => $marketers,

            'date' => $date,
            'adminId' => $adminId,
            'agentId' => $agentId,
            'marketerId' => $marketerId,
        ]);
    }

    public function create(Request $request)
    {
        $dates = Date::orderBy('id', 'DESC')->get();
        $psps = Psp::where('status', 1)->orderBy('name', 'ASC')->get();

        $dateId = (int)$request->query('date', null);
        $transactionsInfo = null;
        $date = null;
        if ($dateId !== 0) {
            $date = Date::where('id', $dateId)->get()->first();
            if (!is_null($date)) {
                $transactionsInfo = [
                    'count' => Transaction::where('date_id', $date->id)->count(),
                    'total_count' => Transaction::where('date_id', $date->id)->sum('total_count'),
                    'total_amount' => Transaction::where('date_id', $date->id)->sum('total_amount'),
                    'total_wage' => Transaction::where('date_id', $date->id)->sum('total_wage'),
                    'success' => Transaction::where('date_id', $date->id)->where('status_id', 1)->count(),
                    'error' => Transaction::where('date_id', $date->id)->where('status_id', 0)->count(),
                ];
                foreach ($transactionsInfo as $key => $value) {
                    $transactionsInfo[$key] = number_format((int)$value);
                }
            }
        }

        return Inertia::render('Dashboard/Transactions/CreateTransactions', compact('dates', 'psps', 'transactionsInfo', 'date', 'dateId'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('transactionsForm', [
            'date_id' => 'required',
            'psp_id' => 'required',
            'file' => 'required'
        ]);

        $date = $request->get('date_id');
        $file = $request->file('file')->store('temp/excel/transactions');
        $transactionsDate = Date::find($date);
        if (!is_null($transactionsDate)) {
            Excel::import(new MonthlyImport($date), $file);

            $transactionsDate->status = 1;
            $transactionsDate->save();
            return redirect()->route('dashboard.transactions.create', ['date' => $date, 'step' => 2]);
        }

        return redirect()->route('dashboard.transactions.create')->with('message', 'تاریخ وارد شده اشتباه است');
    }

    public function store2(Request $request)
    {
        $date = (int)$request->query('date', null);
        if ($date === 0) return redirect()->route('dashboard.transactions.create')->with('message', 'تاریخ وارد شده اشتباه است');

        $users = User::with('parent')
            ->with('parent.parent')
            ->orderBy('id', 'ASC')
            ->get();
        $list = [];
        foreach ($users as $user) {
            $profiles = Profile::where('user_id', $user->id)->pluck('id');
            $transactions = Transaction::where('date_id', $date)->whereIn('profile_id', $profiles)->get();
            $totalWage = $transactions->sum('total_wage');
            $userWages = $this->calculateUserWages($user, $totalWage);
            $list[] = [
                'user_id' => $user->id,
                'date_id' => $date,
                'profiles_count' => count($transactions),
                'success' => $transactions->where('status', 1)->count(),
                'error' => $transactions->where('status', 0)->count(),

                'total_amount' => $transactions->sum('total_amount'),
                'total_count' => $transactions->sum('total_count'),
                'total_wage' => $totalWage,

                'charge_amount' => $transactions->sum('charge_amount'),
                'charge_count' => $transactions->sum('charge_count'),
                'charge_wage' => $transactions->sum('charge_wage'),

                'bills_amount' => $transactions->sum('bills_amount'),
                'bills_count' => $transactions->sum('bills_count'),
                'bills_wage' => $transactions->sum('bills_wage'),

                'buys_amount' => $transactions->sum('buys_amount'),
                'buys_count' => $transactions->sum('buys_count'),
                'buys_wage' => $transactions->sum('buys_wage'),

                'balance_amount' => $transactions->sum('balance_amount'),
                'balance_count' => $transactions->sum('balance_count'),
                'balance_wage' => $transactions->sum('balance_wage'),

                'under_50_amount' => $transactions->sum('under_50_amount'),
                'under_50_count' => $transactions->sum('under_50_count'),
                'under_50_wage' => $transactions->sum('under_50_wage'),

                'under_100_amount' => $transactions->sum('under_100_amount'),
                'under_100_count' => $transactions->sum('under_100_count'),
                'under_100_wage' => $transactions->sum('under_100_wage'),

                'under_150_amount' => $transactions->sum('under_150_amount'),
                'under_150_count' => $transactions->sum('under_150_count'),
                'under_150_wage' => $transactions->sum('under_150_wage'),

                'under_200_amount' => $transactions->sum('under_200_amount'),
                'under_200_count' => $transactions->sum('under_200_count'),
                'under_200_wage' => $transactions->sum('under_200_wage'),

                'under_250_amount' => $transactions->sum('under_250_amount'),
                'under_250_count' => $transactions->sum('under_250_count'),
                'under_250_wage' => $transactions->sum('under_250_wage'),

                'over_250_amount' => $transactions->sum('over_250_amount'),
                'over_250_count' => $transactions->sum('over_250_count'),
                'over_250_wage' => $transactions->sum('over_250_wage'),

                'superuser_wage' => $userWages['superuser'],
                'admin_wage' => $userWages['admin'],
                'agent_wage' => $userWages['agent'],
                'marketer_wage' => $userWages['marketer'],
            ];
        }

        Total::insert($list);
    }

    public function calculateUserWages($user, $totalWage)
    {
        $percent = $user->percent;

        $superuserWage = (($totalWage * 91) / 100) / 2;
        $adminWage = 0;
        $agentWage = 0;
        $marketerWage = 0;

        if ($user->isAdmin()) {
            $adminWage = ($superuserWage * $percent) / 100;
        } elseif ($user->isAgent()) {
            $parent = $user->parent;
            if ($parent->isSuperuser()) {
                $adminWage = $superuserWage;
            } elseif ($parent->isAdmin()) {
                $adminPercent = $parent->percent;
                $adminWage = ($superuserWage * $adminPercent) / 100;
            }
            $agentWage = ($adminWage * $percent) / 100;
        } elseif ($user->isMarketer()) {
            $parent = $user->parent;
            if ($parent->isSuperuser()) {
                $marketerWage = ($superuserWage * $percent) / 100;
            } elseif ($parent->isAdmin()) {
                $adminPercent = $parent->percent;
                $adminWage = ($superuserWage * $adminPercent) / 100;
                $marketerWage = ($adminWage * $percent) / 100;
            } elseif ($parent->isAgent()) {
                $adminPercent = $parent->parent->percent;
                $adminWage = ($superuserWage * $adminPercent) / 100;
                $agentPercent = $parent->percent;
                $agentWage = ($adminWage * $agentPercent) / 100;
                $marketerWage = ($adminWage * $percent) / 100;
            }
        }

        return [
            'superuser' => $superuserWage,
            'admin' => $adminWage,
            'agent' => $agentWage,
            'marketer' => $marketerWage,
        ];
    }

    public function createDate(Request $request)
    {
        $request->validateWithBag('createDateForm', [
            'name' => 'required|unique:transactions_dates'
        ]);
        Date::create(['name' => $request->get('name')]);

        return redirect()->route('dashboard.transactions.create');
    }
}
