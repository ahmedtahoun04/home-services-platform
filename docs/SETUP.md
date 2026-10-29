# Local setup and after-pull checklist

Requirements: PHP 8.3 or newer, Composer, Git.

## First time

```powershell
git clone https://github.com/ahmedtahoun04/home-services-platform
cd home-services-platform
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item database\database.sqlite -ItemType File
php artisan migrate
php artisan db:seed
php artisan serve
```

Open http://127.0.0.1:8000

## After every `git pull`

```powershell
composer install
php artisan migrate
php artisan db:seed
php artisan optimize:clear
php artisan test
```

The database file (`database/database.sqlite`) is NOT shared through Git, so each
developer must run `migrate` after pulling new code. A 500 error on the home page
right after a pull usually means a missing table: run `migrate` and `db:seed`.

To see the real error: `Get-Content storage\logs\laravel.log -Tail 30`

## Demo accounts (from the seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@homely.test | Admin12345 |
| Customer | customer@homely.test | Customer12345 |
