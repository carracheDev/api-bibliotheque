<?php

namespace Tests\Feature;

use App\Exceptions\LoanException;
use App\Models\Author;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LoanBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    private function makeBook(string $isbn = '9780000000001'): Book
    {
        $author = Author::create(['name' => 'Auteur de test']);
        $book = Book::create([
            'title' => 'Livre de test',
            'isbn' => $isbn,
            'year' => 2025,
        ]);
        $book->authors()->attach($author);

        return $book;
    }

    private function makeMember(string $email = 'member@example.com'): Member
    {
        return Member::create([
            'name' => 'Membre de test',
            'email' => $email,
            'registered_at' => Carbon::today(),
        ]);
    }

    public function test_it_refuses_a_second_active_loan_for_the_same_book(): void
    {
        $service = app(LoanService::class);
        $book = $this->makeBook();
        $firstMember = $this->makeMember();
        $secondMember = $this->makeMember('second@example.com');

        $service->borrow($book, $firstMember, Carbon::tomorrow());

        $this->expectException(LoanException::class);
        $this->expectExceptionMessage('déjà emprunté');
        $service->borrow($book, $secondMember, Carbon::tomorrow());
    }

    public function test_it_limits_a_member_to_three_active_loans(): void
    {
        $service = app(LoanService::class);
        $member = $this->makeMember();

        foreach (range(1, 3) as $number) {
            $service->borrow($this->makeBook('978000000000'.$number), $member, Carbon::tomorrow());
        }

        $this->expectException(LoanException::class);
        $this->expectExceptionMessage('limite de 3');
        $service->borrow($this->makeBook('9780000000004'), $member, Carbon::tomorrow());
    }

    public function test_it_calculates_whether_an_unreturned_loan_is_late(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-12'));
        $loan = Loan::make(['due_at' => '2026-09-11', 'returned_at' => null]);

        self::assertTrue($loan->isLate());

        $loan->returned_at = Carbon::today()->toDateString();
        self::assertFalse($loan->isLate());

        Carbon::setTestNow();
    }
}