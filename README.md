# elearning

University SaaS e-learning system built with Laravel. Includes enrollment, billing, PayMongo (GCash/Maya) payments, grades, and LMS features.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. This project uses Laravel for:

- [Simple, fast routing engine](https://laravel.com/docs/routing)
- [Database ORM (Eloquent)](https://laravel.com/docs/eloquent) and [migrations](https://laravel.com/docs/migrations)
- [Session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage

## Setup

1. Clone the repo and `cd` into the project.
2. Copy `.env.example` to `.env` and set your database and API keys (e.g. `PAYMONGO_SECRET_KEY`, `PAYMONGO_PUBLIC_KEY`).
3. Run `composer install`, `php artisan key:generate`, and `php artisan migrate`.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
