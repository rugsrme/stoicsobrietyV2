<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Moon, Sun } from '@lucide/vue';
import { dashboard, login } from '@/routes';
import { index as blogIndex } from '@/routes/blog';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance, updateAppearance } = useAppearance();

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
                <Link href="/" class="text-[17px] font-semibold tracking-tight"
                    >Stoic Recovery</Link
                >

                <nav class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="text-sm text-[var(--site-ink-soft)] transition-colors hover:text-[var(--site-ink)]"
                    >
                        Dashboard
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

        <footer class="py-11">
            <div
                class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-[18px] px-7"
            >
                <ul
                    class="flex list-none gap-[22px] p-0 text-sm text-[var(--site-ink-soft)]"
                >
                    <li>
                        <Link href="/" class="hover:text-[var(--site-ink)]"
                            >Home</Link
                        >
                    </li>
                    <li>
                        <Link
                            href="/#about"
                            class="hover:text-[var(--site-ink)]"
                            >About</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="blogIndex()"
                            class="hover:text-[var(--site-ink)]"
                            >Journal</Link
                        >
                    </li>
                    <li>
                        <a
                            href="mailto:hello@stoicrecovery.com"
                            class="hover:text-[var(--site-ink)]"
                            >Contact</a
                        >
                    </li>
                </ul>
                <p class="text-[13px] text-[var(--site-ink-faint)]">
                    &copy; {{ new Date().getFullYear() }} Stoic Recovery. All
                    rights reserved.
                </p>
            </div>
        </footer>
    </div>
</template>
