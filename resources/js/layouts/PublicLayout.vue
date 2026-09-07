<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import { index as blogIndex } from '@/routes/blog';
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
            href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="min-h-screen bg-[#f6f1e9] text-[#1c1a16] antialiased dark:bg-[#15130f] dark:text-[#efe9dd]"
    >
        <header
            class="mx-auto flex max-w-6xl items-center justify-between px-6 py-8 lg:px-10"
        >
            <Link href="/" class="font-serif-display text-lg tracking-tight"
                >Stoic Recovery</Link
            >
            <nav class="flex items-center gap-6 text-sm">
                <Link
                    :href="blogIndex()"
                    class="text-current/70 transition-colors hover:text-current"
                    >Journal</Link
                >
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="text-current/70 transition-colors hover:text-current"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="text-current/70 transition-colors hover:text-current"
                        >Log in</Link
                    >
                    <Link
                        :href="register()"
                        class="border-b border-current/30 pb-0.5 transition-colors hover:border-current"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <slot />

        <footer
            class="mx-auto max-w-6xl border-t border-current/15 px-6 py-10 lg:px-10"
        >
            <div
                class="flex flex-col items-start justify-between gap-4 text-sm text-current/50 sm:flex-row sm:items-center"
            >
                <span
                    >&copy; {{ new Date().getFullYear() }} Stoic Recovery</span
                >
                <div class="flex gap-6">
                    <Link :href="blogIndex()" class="hover:text-current"
                        >Journal</Link
                    >
                    <Link :href="login()" class="hover:text-current"
                        >Log in</Link
                    >
                </div>
            </div>
        </footer>
    </div>
</template>
