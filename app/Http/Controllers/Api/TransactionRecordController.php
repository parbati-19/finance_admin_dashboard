<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\StoreTransactionRequest;
use App\Http\Requests\Transactions\UpdateTransactionRequest;
use App\Http\Resources\TransactionRecordCollection;
use App\Http\Resources\TransactionRecordResource;
use App\Models\Enums\TransactionType;
use App\Models\TransactionRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionRecordController extends Controller
{
    public function index(Request $request): TransactionRecordCollection
    {
        $query = TransactionRecord::where('user_id', Auth::id());

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

        return new TransactionRecordCollection(
            $query->paginate($request->integer('per_page', 10))
        );
    }

    public function show(TransactionRecord $transactionRecord): JsonResponse
    {
        if ($transactionRecord->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'transaction' => new TransactionRecordResource($transactionRecord),
        ]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $transaction = TransactionRecord::create($data);

        return response()->json([
            'message' => 'Transaction created successfully.',
            'transaction' => new TransactionRecordResource($transaction),
        ], 201);
    }

    public function update(UpdateTransactionRequest $request, TransactionRecord $transactionRecord): JsonResponse
    {
        $transactionRecord->update($request->validated());

        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => new TransactionRecordResource($transactionRecord->fresh()),
        ]);
    }

    public function destroy(TransactionRecord $transactionRecord): JsonResponse
    {
        if ($transactionRecord->user_id !== Auth::id()) {
            abort(403);
        }

        $transactionRecord->delete();

        return response()->json(['message' => 'Transaction deleted successfully.']);
    }

    public function categories(): JsonResponse
    {
        $categories = TransactionRecord::where('user_id', Auth::id())
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return response()->json([
            'categories' => $categories,
            'types' => collect(TransactionType::cases())->map(fn($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
        ]);
    }
}
