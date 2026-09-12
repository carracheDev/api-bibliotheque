<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $books = Book::query()
            ->with(['authors', 'activeLoan'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $term = '%'.mb_strtolower($search).'%';
                    $query->whereRaw('LOWER(title) LIKE ?', [$term])
                        ->orWhereHas('authors', fn ($author) => $author->whereRaw('LOWER(name) LIKE ?', [$term]));
                });
            })
            ->when($request->input('availability') === 'available', fn ($query) => $query->available())
            ->when($request->input('availability') === 'unavailable', fn ($query) => $query->unavailable())
            ->orderBy('title')
            ->paginate($request->integer('per_page', 15));

        return BookResource::collection($books);
    }

    public function show(Book $book): BookResource
    {
        return new BookResource($book->load(['authors', 'activeLoan']));
    }

    public function store(StoreBookRequest $request): BookResource|JsonResponse
    {
        $book = DB::transaction(function () use ($request): Book {
            $book = Book::create($request->safe()->except('author_ids'));
            $book->authors()->sync($request->validated('author_ids'));

            return $book->load(['authors', 'activeLoan']);
        });

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    public function destroy(Book $book): Response
    {
        abort_if($book->activeLoan()->exists(), 409, 'Un livre emprunté ne peut pas être supprimé.');
        $book->delete();

        return response()->noContent();
    }
}