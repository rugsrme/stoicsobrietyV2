# Stoic Recovery

Companion site for *Architecture of Surrender* and future Stoic Recovery titles.

## Stack

- Laravel 13
- PHP 8.3+
- Vue 3 with Inertia.js, TypeScript, shadcn-vue components
- Vite and Tailwind CSS
- Laravel Fortify authentication (registration, login, password reset, email verification, 2FA, passkeys)
- MySQL
- Pest, PHPStan/Larastan, and Laravel Pint
- Deployer, for shared-hosting deploys

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- MySQL (or SQLite for local development)

## Getting started

```bash
composer run setup
```

The setup script installs PHP and JavaScript dependencies, creates the local environment file,
generates an application key, runs migrations, and builds the frontend.

Then seed the featured book and a test user:

```bash
php artisan db:seed
```

Start the development server with:

```bash
composer run dev
```

## Useful commands

```bash
# Build frontend assets
npm run build

# Run frontend checks
npm run check
npm run types:check

# Run formatting, static analysis, and tests
composer run lint
composer run types:check
php artisan test

# Run the complete project test suite
composer run test
```

## Application structure

- `/` — Landing page featuring the current book
- `/books`, `/books/{book}` — Book catalog (built to hold more than one title)
- `/blog`, `/blog/{post}` — Public journal
- `/dashboard` — Authenticated dashboard
- `/settings/*` — Authenticated account settings (profile, avatar, security)
- `/admin/posts/*` — Authenticated journal admin (create, edit, publish)

### Models

- `Book` — title, retailer links, excerpts, author bio, `price` (cents) and `purchase_type`
  (`link` today; the schema is ready for a `stripe` checkout later without a migration rework)
- `Post` — simple database-backed blog post (title, slug, body, `published_at`, author)
- `User` — standard Fortify user, extended with `display_name`, `bio`, and `avatar_path`
  (stored on the `public` disk) for profile pages. No teams/roles yet — kept deliberately simple.

## Deployment

Deploys to shared hosting (Web Hosting Hub, quinnix.com) via [Deployer](https://deployer.org),
over SSH on port 2222. See `deploy.php`.

Before your **first** deploy:

1. Run `npm run build` locally and commit the resulting `public/build` directory — the shared
   host has no Node.js, so assets are never built on the server.
2. Make sure `composer.json`'s `config.platform.php` matches the server's PHP version.
3. Copy `.env.example` to `.env` on the server, fill in real database credentials, and run
   `php artisan key:generate`.
4. Point the `stoic.quinnix.com` subdomain's document root at `~/stoicrecovery/current/public`
   (this repurposes the subdomain previously used by the stoicsobriety practice app).

Then deploy with:

```bash
vendor/bin/dep deploy
```
