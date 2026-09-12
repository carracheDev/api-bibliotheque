 # API REST de gestion de bibliothèque

 Projet Laravel de démonstration pour une API de bibliothèque avec PostgreSQL et Laravel Sanctum.

 ## Installation

 Prérequis : PHP 8.3+, Composer, PostgreSQL et Node.js.

 ```bash
 composer install
 cp .env.example .env
 php artisan key:generate
 ```

 Créer la base PostgreSQL puis renseigner `.env` avec `DB_CONNECTION=pgsql`, `DB_HOST=127.0.0.1`, `DB_PORT=5432`, `DB_DATABASE=bibliotheque`, `DB_USERNAME=postgres` et `DB_PASSWORD=motdepasse123`.

 Initialiser le schéma et les données :

 ```bash
 php artisan migrate --seed
 php artisan test
 php artisan serve
 ```

 ## Architecture

 - `app/Http/Requests` : validation des entrées HTTP.
 - `app/Http/Resources` : format des réponses JSON.
 - `app/Services/LoanService.php` : règles métier d’emprunt et de retour.
 - `app/Models` : relations Eloquent.
 - `database/migrations` : schéma PostgreSQL versionné.
 - `database/seeders` : données de démonstration.
 - `tests/Feature/LoanBusinessRulesTest.php` : tests des règles critiques.

 ## Authentification

 Créer un compte :

 ```bash
 curl -X POST http://localhost:8000/api/auth/register \
   -H 'Content-Type: application/json' \
   -d '{"name":"Admin","email":"admin@example.com","password":"password123","password_confirmation":"password123"}'
 ```

 Le champ `token` retourné doit être envoyé avec l’en-tête `Authorization: Bearer VOTRE_TOKEN`.

 ## Endpoints principaux

 | Méthode | URL | Authentification | Description |
 | --- | --- | --- | --- |
 | `POST` | `/api/auth/register` | Non | Créer un utilisateur et un token |
 | `POST` | `/api/auth/login` | Non | Obtenir un token |
 | `POST` | `/api/auth/logout` | Oui | Révoquer le token courant |
 | `GET` | `/api/books` | Non | Livres paginés |
 | `GET` | `/api/books?search=Afrique` | Non | Filtrer par titre ou auteur |
 | `GET` | `/api/books?availability=available` | Non | Livres disponibles |
 | `POST` | `/api/books` | Oui | Créer un livre et ses auteurs |
 | `POST` | `/api/loans` | Oui | Emprunter un livre |
 | `POST` | `/api/loans/{id}/return` | Oui | Enregistrer le retour |

 Créer un emprunt :

 ```bash
 curl -X POST http://localhost:8000/api/loans \
   -H "Authorization: Bearer VOTRE_TOKEN" \
   -H 'Content-Type: application/json' \
   -d '{"book_id":1,"member_id":1,"due_at":"2026-09-30"}'
 ```

 Une transaction et des verrous de ligne empêchent deux emprunts concurrents du même livre. Le service refuse aussi un quatrième emprunt actif pour un membre. Les réponses d’emprunt exposent `is_late`, calculé lorsque `due_at` est dépassée et que `returned_at` est vide.

## Documentation OpenAPI

La spécification complète est disponible dans `openapi.yaml`. Elle peut être importée dans Swagger UI, Postman ou Insomnia.

## Docker

Pour lancer l’application et PostgreSQL :

```bash
docker compose up --build
```

L’API est alors disponible sur `http://localhost:8000`.

## CI/CD

Le workflow `.github/workflows/ci.yml` installe PHP 8.3, démarre PostgreSQL et exécute toutes les migrations et tous les tests à chaque push et pull request.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# api-bibliotheque
