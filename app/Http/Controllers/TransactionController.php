<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::orderByDesc('date')->get();

        return Inertia::render('Transaction/Index', [
            'transactions' => $transactions,
        ]);
    }

    public function create()
    {
        return Inertia::render('Transaction/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'remark' => 'nullable|string',
        ]);

        Transaction::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        return Inertia::render('Transaction/Edit', [
            'transaction' => $transaction,
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'remark' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function summary(Request $request)
    {
        $range = $request->get('range', 'week');
    
        $baseQuery = Transaction::where('user_id', Auth::id());
    
        $transactions = match ($range) {
            'today' => (clone $baseQuery)->whereDate('date', today())->get(),
            'month' => (clone $baseQuery)->whereMonth('date', now()->month)->get(),
            default => (clone $baseQuery)->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->get()
        };
    
        $previousTransactions = match ($range) {
            'today' => (clone $baseQuery)->whereDate('date', today()->subDay())->get(),
            'month' => (clone $baseQuery)->whereMonth('date', now()->subMonth()->month)->get(),
            default => (clone $baseQuery)->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->get()
        };
    
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;
    
        $previousBalance = $previousTransactions->where('type', 'income')->sum('amount') -
            $previousTransactions->where('type', 'expense')->sum('amount');
    
        // Hitung persentase perubahan dengan aman
        $percentageChange = null;

        if ($previousBalance === 0) {
            $percentageChange = null; // undefined growth
        } else {
            $percentageChange = (($balance - $previousBalance) / abs($previousBalance)) * 100;
        }
    
        $transactions = $transactions->sortBy('date');
    
        $runningTotal = 0;
        $dailySummary = $transactions
            ->groupBy(fn($item) => \Carbon\Carbon::parse($item->date)->format('Y-m-d'))
            ->map(function ($group) use (&$runningTotal) {
                $dailyIncome = $group->where('type', 'income')->sum('amount');
                $dailyExpense = $group->where('type', 'expense')->sum('amount');
    
                $runningTotal += ($dailyIncome - $dailyExpense);
                return $runningTotal;
            })
            ->all(); // pakai all() agar bentuknya tetap Record<string, number>
    
        return Inertia::render('Dashboard', [
            'summary' => [
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
                'previous_balance' => $previousBalance,
                'percent_change' => $percentageChange,
                'chart' => $dailySummary,
            ]
        ]);
    }
    
}
