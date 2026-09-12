<?php

namespace App\Services;

use App\Exceptions\LoanException;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public const MAX_ACTIVE_LOANS = 3;

    public function borrow(Book $book, Member $member, Carbon $dueAt): Loan
    {
        return DB::transaction(function () use ($book, $member, $dueAt): Loan {
            $lockedBook = Book::query()->lockForUpdate()->findOrFail($book->id);
            $lockedMember = Member::query()->lockForUpdate()->findOrFail($member->id);
            $activeLoans = Loan::query()
                ->where('member_id', $lockedMember->id)
                ->whereNull('returned_at')
                ->count();

            if ($lockedBook->loans()->whereNull('returned_at')->exists()) {
                throw new LoanException('Ce livre est déjà emprunté.');
            }

            if ($activeLoans >= self::MAX_ACTIVE_LOANS) {
                throw new LoanException('Le membre a atteint la limite de 3 emprunts actifs.');
            }

            return Loan::create([
                'book_id' => $lockedBook->id,
                'member_id' => $lockedMember->id,
                'borrowed_at' => now()->toDateString(),
                'due_at' => $dueAt->toDateString(),
            ])->load(['book.authors', 'book.activeLoan', 'member']);
        });
    }

    public function complete(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan): Loan {
            $lockedLoan = Loan::query()->lockForUpdate()->findOrFail($loan->id);

            if ($lockedLoan->returned_at !== null) {
                throw new LoanException('Cet emprunt est déjà clôturé.');
            }

            $lockedLoan->update(['returned_at' => now()->toDateString()]);

            return $lockedLoan->fresh(['book.authors', 'book.activeLoan', 'member']);
        });
    }
}