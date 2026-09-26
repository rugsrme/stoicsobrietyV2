<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Moon, Sun } from '@lucide/vue';
import { dashboard, login } from '@/routes';
import { show as bookShow } from '@/routes/books';
import { index as journalIndex } from '@/routes/journal';
import { index as reflectionsIndex } from '@/routes/reflections';
import { index as reviewsIndex } from '@/routes/reviews';
import { index as sampleIndex } from '@/routes/sample';
import { useAppearance } from '@/composables/useAppearance';
import { computed } from 'vue';

const { resolvedAppearance, updateAppearance } = useAppearance();

const page = usePage();

const bookUrl = computed(() =>
    page.props.bookSlug ? bookShow(page.props.bookSlug).url : null,
);

function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <Head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin=""
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="public-site min-h-screen bg-[var(--site-bg)] text-[var(--site-ink)] antialiased"
    >
        <header
            class="sticky top-0 z-40 border-b border-[var(--site-line)] bg-[var(--site-bg)]/90 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-4xl items-center justify-between px-7 py-[18px]"
            >
                <Link
                    href="/"
                    class="flex items-center gap-2.5 text-[17px] font-semibold tracking-tight"
                >
                    <img
                        src="/brand/mark.svg"
                        alt=""
                        class="size-8 shrink-0 rounded-lg"
                    />
                    <span
                        >Sober
                        <span class="text-[var(--site-accent)]"
                            >Now We Live</span
                        ></span
                    >
                </Link>

                <nav class="flex items-center gap-4">
                    <div class="hidden items-center gap-5 sm:flex">
                        <Link
                            v-if="bookUrl"
                            :href="bookUrl"
                            class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                        >
                            The Book
                        </Link>
                        <Link
                            :href="sampleIndex()"
                            class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                        >
                            Read Free
                        </Link>
                        <Link
                            :href="reflectionsIndex()"
                            class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                        >
                            Reflections
                        </Link>
                        <Link
                            :href="reviewsIndex()"
                            class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                        >
                            Book Reviews
                        </Link>
                        <Link
                            v-if="$page.props.auth.user?.is_admin"
                            :href="journalIndex()"
                            class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                            title="Private — admins only"
                        >
                            Journal
                        </Link>
                    </div>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                    >
                        Subscribers Area
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                    >
                        Log in
                    </Link>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-[var(--site-line)] px-3.5 py-[7px] text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                        :aria-pressed="resolvedAppearance === 'dark'"
                        @click="toggleTheme"
                    >
                        <Moon
                            v-if="resolvedAppearance !== 'dark'"
                            class="size-[15px]"
                        />
                        <Sun v-else class="size-[15px]" />
                        <span>{{
                            resolvedAppearance === 'dark'
                                ? 'Light mode'
                                : 'Dark mode'
                        }}</span>
                    </button>
                </nav>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer class="border-t border-[var(--site-line)] py-11">
            <div class="mx-auto mb-9 flex max-w-4xl items-center gap-5 px-7">
                <img
                    src="/brand/badge.webp"
                    alt="Sober Now We Live"
                    class="size-24 shrink-0 rounded-full"
                    loading="lazy"
                />
                <div>
                    <p class="text-[15px] font-semibold">
                        Sober
                        <span class="text-[var(--site-accent)]"
                            >Now We Live</span
                        >
                    </p>
                    <p
                        class="mt-1 text-xs tracking-[0.2em] text-[var(--site-ink-soft)] uppercase"
                    >
                        Steps · Stoicism · Scripture
                    </p>
                    <p class="mt-1 text-sm text-[var(--site-ink-faint)]">
                        A more honest way forward.
                    </p>
                </div>
            </div>
            <div
                class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-[18px] px-7"
            >
                <ul
                    class="flex list-none flex-wrap gap-x-[22px] gap-y-2 p-0 text-sm text-[var(--site-ink-soft)]"
                >
                    <li>
                        <Link href="/" class="hover:text-[var(--site-ink)]"
                            >Home</Link
                        >
                    </li>
                    <li v-if="bookUrl">
                        <Link
                            :href="bookUrl"
                            class="hover:text-[var(--site-ink)]"
                            >The Book</Link
                        >
                    </li>
                    <li v-if="bookUrl">
                        <Link
                            :href="`${bookUrl}#about`"
                            class="hover:text-[var(--site-ink)]"
                            >About</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="sampleIndex()"
                            class="hover:text-[var(--site-ink)]"
                            >Read Free</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="reflectionsIndex()"
                            class="hover:text-[var(--site-ink)]"
                            >Reflections</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="reviewsIndex()"
                            class="hover:text-[var(--site-ink)]"
                            >Book Reviews</Link
                        >
                    </li>
                    <li>
                        <a
                            href="mailto:contact@quinnix.com"
                            class="hover:text-[var(--site-ink)]"
                            >Contact</a
                        >
                    </li>
                    <li>
                        <Link
                            href="/affiliate-disclosure"
                            class="hover:text-[var(--site-ink)]"
                            >Affiliate Disclosure</Link
                        >
                    </li>
                    <li>
                        <Link
                            href="/privacy-policy"
                            class="hover:text-[var(--site-ink)]"
                            >Privacy Policy</Link
                        >
                    </li>
                </ul>
                <p class="text-[13px] text-[var(--site-ink-faint)]">
                    &copy; {{ new Date().getFullYear() }} Sober Now We Live. All
                    rights reserved.
                </p>
            </div>
        </footer>
    </div>
</template>
