# Sober Now We Live

Companion site for _What Was Never Yours_ and future titles — plus Reflections and book reviews.

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
- `/read`, `/read/{chapter}` — Free public sample: the opening and Chapters 1–3
  (`config('book.public_chapters')`); later chapters send readers to the subscriber library
- `/reflections`, `/reflections/{post}` — Public reflections (old `/blog` links redirect here)
- `/reviews`, `/reviews/{post}` — Public book reviews, with cover image and affiliate links
- `/dashboard` — Authenticated dashboard
- `/settings/*` — Authenticated account settings (profile, avatar, security)
- `/library/*` — Authenticated full-book reader
- `/journal`, `/journal/{post}` — Private journal, visible to admins only
- `/admin/posts/*` — Admin-only Writing editor for all three (rich text with image upload; paste from
  Facebook works, and pasted remote images are copied to local storage on save)

### Models

- `Book` — title, retailer links, excerpts, author bio, `price` (cents) and `purchase_type`
  (`link` today; the schema is ready for a `stripe` checkout later without a migration rework)
- `Post` — reflection, book review, or private journal entry
  (`category`: `reflection` | `book-review` | `journal`). Body is HTML from the
  editor, sanitized on save by `App\Support\PostHtml`. Reviews add the reviewed book's title,
  author, an optional 1–5 rating, and `affiliate_links`.
- `User` — standard Fortify user, extended with `display_name`, `bio`, and `avatar_path`
  (stored on the `public` disk) for profile pages. No teams/roles yet — kept deliberately simple.

### Book text

The reader content lives in `resources/book/chapters/*.md` and is generated from the KDP
manuscript PDF. When the manuscript changes, regenerate it (needs Python 3 and PyMuPDF):

```bash
python3 scripts/book-from-pdf.py path/to/What_Was_Never_Yours_6x9_KDP_FINAL.pdf resources/book/chapters
```

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
