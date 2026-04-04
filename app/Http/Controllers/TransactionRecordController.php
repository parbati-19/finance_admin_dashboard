<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transactions\StoreTransactionRequest;
use App\Http\Requests\Transactions\UpdateTransactionRequest;
use App\Models\Enums\TransactionType;
use App\Models\TransactionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = TransactionRecord::with('user')
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        $transactions = $query->paginate(10)->through(function ($record) {
            return [
                'id' => $record->id,
                'amount' => $record->amount,
                'type' => $record->type->value,
                'type_label' => $record->type->label(),
                'category' => $record->category,
                'date' => $record->date->format('Y-m-d'),
                'notes' => $record->notes,
                'created_at' => $record->created_at->format('Y-m-d H:i'),
            ];
        });

        $categories = TransactionRecord::where('user_id', Auth::id())
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return Inertia::render('TransactionRecords/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'types' => collect(TransactionType::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
            'filters' => $request->only(['search', 'type', 'category']),
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        TransactionRecord::create($data);

        return back()->with('success', 'Transaction created successfully');
    }

    public function update(UpdateTransactionRequest $request, TransactionRecord $transaction_record)
    {
        $transaction_record->update($request->validated());

        return back()->with('success', 'Transaction updated successfully');
    }

    public function destroy(TransactionRecord $transaction_record)
    {
        if ($transaction_record->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction_record->delete();

        return back()->with('success', 'Transaction deleted successfully');
    }
}
