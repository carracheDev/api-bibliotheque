<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $authors = [
            Author::updateOrCreate(['name' => 'Olympe Bhêly-Quenum'], ['bio' => 'Écrivain béninois de langue française.']),
            Author::updateOrCreate(['name' => 'Ahmadou Kourouma'], ['bio' => 'Romancier ivoirien majeur de la littérature africaine.']),
            Author::updateOrCreate(['name' => 'Chinua Achebe'], ['bio' => 'Écrivain nigérian et figure de la littérature africaine moderne.']),
        ];

        $books = [
            ['title' => 'Un piège sans fin', 'isbn' => '9782070360426', 'year' => 1968, 'authors' => [$authors[0]]],
            ['title' => 'Les soleils des indépendances', 'isbn' => '9782708701234', 'year' => 1968, 'authors' => [$authors[1]]],
            ['title' => 'Things Fall Apart', 'isbn' => '9780385474542', 'year' => 1958, 'authors' => [$authors[2]]],
        ];

        foreach ($books as $data) {
            $book = Book::updateOrCreate(['isbn' => $data['isbn']], [
                'title' => $data['title'],
                'year' => $data['year'],
            ]);
            $book->authors()->sync(collect($data['authors'])->pluck('id'));
        }

        Member::updateOrCreate(['email' => 'aminata.soglo@example.com'], [
            'name' => 'Aminata Soglo',
            'registered_at' => Carbon::today(),
        ]);

        Member::updateOrCreate(['email' => 'koffi.mensah@example.com'], [
            'name' => 'Koffi Mensah',
            'registered_at' => Carbon::today(),
        ]);
    }
}
