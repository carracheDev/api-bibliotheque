<?php

namespace App\Http\Controllers;

use App\Exceptions\LoanException;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Resources\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService)
    {
    }

    public function store(StoreLoanRequest $request): LoanResource|JsonResponse
    {
        try {
            $loan = $this->loanService->borrow(
                Book::findOrFail($request->integer('book_id')),
                Member::findOrFail($request->integer('member_id')),
                Carbon::parse($request->validated('due_at')),
            );
        } catch (LoanException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return new LoanResource($loan);
    }

    public function complete(Loan $loan): LoanResource|JsonResponse
    {
        try {
            return new LoanResource($this->loanService->complete($loan));
        } catch (LoanException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }
}