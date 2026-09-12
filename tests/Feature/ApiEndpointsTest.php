<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_list_members(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/members', [
            'name' => 'Membre API',
            'email' => 'membre-api@example.com',
        ]);

        $response->assertCreated()->assertJsonPath('data.email', 'membre-api@example.com');
        $this->actingAs($admin, 'sanctum')->getJson('/api/members')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_regular_users_cannot_manage_members(): void
    {
        $user = User::factory()->create(['role' => 'member']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/members')
            ->assertForbidden()
            ->assertJsonPath('message', 'Accès réservé aux administrateurs.');
    }

    public function test_books_endpoint_supports_search_and_availability_filter(): void
    {
        $author = Author::create(['name' => 'Auteur API']);
        $book = \App\Models\Book::create([
            'title' => 'Recherche API',
            'isbn' => '9780000000099',
            'year' => 2026,
        ]);
        $book->authors()->attach($author);

        $this->getJson('/api/books?search=Recherche&availability=available')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Recherche API')
            ->assertJsonPath('data.0.is_available', true);
    }
}