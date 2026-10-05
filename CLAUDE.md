# CLAUDE.md

Guidance for Claude Code when working in this repository. The README covers setup, routes, deployment and the Meta (Facebook/Instagram) setup. Read it first; this file covers what you can't easily see from it.

## What this is

"Sober Now We Live" is the companion site for the book _What Was Never Yours_. It is a Laravel 13 (PHP 8.3) app with Inertia v3, Vue 3 and TypeScript, Tailwind 4 and shadcn-vue (reka-ui). It uses Fortify for auth, with 2FA and passkeys. Production runs MySQL; tests run on in-memory SQLite.

## Commands

```bash
composer run dev          # php artisan dev (server + vite)
composer run test         # config:clear, pint --test, phpstan, then php artisan test
php artisan test --filter=SocialSharing   # a single test file or test
composer run lint         # pint (laravel preset); fixes files
composer run types:check  # phpstan level 7 (larastan) over app/, config/, database/, routes/
npm run check             # vite-plus lint + format check (vp check); check:fix to fix
npm run types:check       # vue-tsc
composer run ci:check     # what GitHub Actions runs: npm check, vue-tsc, then composer test
```

The frontend toolchain is **vite-plus** (`vp`), not plain Vite or ESLint/Prettier. Lint and format settings live in the `lint` and `fmt` blocks of `vite.config.ts`: 4-space indent, single quotes, 80 columns, Tailwind class sorting, and warnings fail the check.

## Things to know before changing code

- **`public/build` is committed** (it is commented out in `.gitignore`). The shared host has no Node, so after a frontend change, run `npm run build` and commit the output before you deploy. `composer.json` pins `config.platform.php` to match the server's PHP.
- **Wayfinder** generates typed route helpers into `resources/js/routes`, `resources/js/actions` and `resources/js/wayfinder`. These folders are gitignored and rebuilt by the Vite plugin. In Vue, import routes from `@/routes/...` (e.g. `import { show } from '@/routes/reflections'`) rather than hard-coding URLs. Don't edit the generated files.
- **Don't edit `resources/js/components/ui/*` casually.** These are vendored shadcn-vue components and are excluded from lint and format.
- **Persistent layouts are chosen in `resources/js/app.ts`** from the page name. Public pages (`Welcome`, `posts/`, `reviews/`, `sample/`, `books/`) get `null` and wrap themselves in `PublicLayout`. Admin pages get `[AppLayout, AdminLayout]`. When you add a page directory, add it to that switch.
- **Admin access** is `users.is_admin` checked by the `admin` middleware alias (`EnsureUserIsAdmin`). There are no roles or policies.
- **The library (`/library/*`) is open to any verified user.** It does not check purchases. `Order` is a bookkeeping record managed in the admin, not an access gate.

## Domain map

- **Posts**: one `posts` table with `category` set to the `App\Enums\PostCategory` enum (`reflection`, `book-review`, `journal`). The enum is the source of truth for labels, public vs. private (`isPublic()`, false only for journal) and which named route shows a post (`showRoute()`).
  - Public sections extend `PostSectionController`, which takes care of listing, the published check, moving a post to its correct section with a 301, and Open Graph data. A journal entry must never be reachable or previewed through a public URL. Keep that rule when you touch this code.
  - Bodies come from the TipTap editor (`RichTextEditor.vue`). On save, `App\Support\PostHtml::sanitize()` runs first: it uses an allowlist, and the only inline style it keeps is `text-align`. Then `localizeImages()` copies remote `<img>` files to the `public` disk, because Facebook image URLs expire. Public pages render the body as raw HTML, so any new tag or attribute must also be allowed in the sanitizer.
  - When a slug changes, `Post::booted()` records the old slug in `post_slug_redirects`, and the route's `->missing()` closure in `routes/web.php` issues a 301 to the new address. The old `/blog/*` URLs redirect to `/reflections/*`.
- **Social sharing**: `Admin\PostController::share()` runs `SharePostToSocial` with `dispatchAfterResponse`, so no queue worker is needed. That job calls `App\Support\MetaPublisher` (Graph API through the `Http` facade) and `SocialImage` (builds the 4:5 Instagram JPEG). Each network is posted to only once, tracked by `facebook_post_id` and `instagram_media_id`. Errors are saved to `social_share_error`. All three fields are `$hidden` and shown only to the editor. Configuration is under `services.meta.*`, and a network is off when its ID is empty.
- **Book text**: the chapters are Markdown files in `resources/book/chapters/`, listed in order in `config/book.php`. `App\Support\BookContent` parses them into blocks. `config('book.public_chapters')` sets the free sample (`/read`); other chapters redirect to `/library`. The files are generated with `scripts/book-from-pdf.py`. Regenerate them rather than editing by hand when the manuscript changes.
- **Books**: `Book::current()` returns the featured book, or else the first published one. Its slug is shared with every page as the `bookSlug` Inertia prop. The book row and its images come from `BookSeeder` (images in `resources/seed-images/`), which runs on **every deploy** and must stay idempotent.
- **Global middleware**: `StripClickIds` runs first on web requests and 301-redirects away `fbclid`, `gclid`, `msclkid` and `igshid`. `HandleInertiaRequests` shares `auth.user`, `bookSlug` and `sidebarOpen`.
- **Link previews**: crawlers don't run JavaScript, so OG and Twitter tags are rendered server-side in `resources/views/app.blade.php`. Controllers pass them with `->withViewData('linkPreview', [...])`.

## Conventions

- PHP: Pint with the Laravel preset. Keep code passing PHPStan level 7; models declare `@property` docblocks, and relations and attributes carry generic return types (see `Post`).
- Validation goes in Form Requests (`app/Http/Requests/Admin|Settings`). Validation rules shared between them live in `app/Concerns/*ValidationRules.php`.
- Money is stored in integer cents (`Book.price`, `Order.amount_cents`).
- Uploaded files go on the `public` disk; models expose them as `*_url` accessors.
- Comments explain *why* in plain language, often from a reader's or editor's point of view. Match that tone.
- Tests use Pest. Feature tests use `RefreshDatabase` automatically (set in `tests/Pest.php`). For external services, fake them: use `Http::fake`, `Storage::fake('public')`, `Bus::fake` and `Sleep::fake` (see `tests/Feature/SocialSharingTest.php`).
- In production, `AppServiceProvider` blocks destructive DB commands and requires strong passwords.

## Deployment

Run `vendor/bin/dep deploy` (Deployer, `deploy.php`) to deploy to shared hosting at quinnix.com over SSH on port 2222. It runs the vendors step, `storage:link`, `optimize`, `migrate` and `db:seed --class=BookSeeder`. No build step runs on the server.

## Local-only

`tools/reel-generator` is deliberately untracked. Leave it out of commits.
