<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <Head title="Privacy Policy" />

    <PublicLayout>
        <article class="mx-auto max-w-3xl px-6 pt-10 pb-24 lg:px-10 lg:pt-16">
            <h1
                class="font-serif-display text-4xl font-medium tracking-tight sm:text-5xl"
            >
                Privacy Policy
            </h1>
            <p class="mt-4 text-sm text-current/50">
                Last updated: September 14, 2026
            </p>

            <div class="mt-12 space-y-6 leading-relaxed text-current/80">
                <p>
                    This Privacy Policy explains how this website collects,
                    uses, and protects information provided by visitors and
                    registered users.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Information We Collect
                </h2>
                <p>
                    When you create an account, we may collect information
                    including your name, email address, account credentials,
                    and viewing preferences. Passwords are stored in hashed
                    form and are not stored as readable passwords.
                </p>
                <p>
                    We may also automatically receive limited technical
                    information when you visit the website, such as your IP
                    address, browser information, device information,
                    requested pages, and other information normally recorded
                    in web server logs.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    How We Use Your Information
                </h2>
                <p>
                    Information collected through this website may be used
                    to:
                </p>
                <ul class="list-disc space-y-2 pl-6">
                    <li>Create and maintain your account.</li>
                    <li>Provide access to subscriber or registered-user content.</li>
                    <li>Remember your viewing preferences.</li>
                    <li>Operate, maintain, secure, and improve the website.</li>
                    <li>Respond to questions or requests.</li>
                    <li>Send emails or newsletters that you have chosen to receive.</li>
                </ul>
                <p>We do not sell your personal information.</p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Cookies and Sessions
                </h2>
                <p>
                    This website uses cookies or similar technologies
                    necessary for functions such as user authentication,
                    sessions, security, and remembering preferences.
                </p>
                <p>
                    Additional cookies or technologies may be used in the
                    future for website analytics or other features. This
                    Privacy Policy will be updated as appropriate when those
                    services are added.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Email Communications
                </h2>
                <p>
                    The website may offer optional email updates or
                    newsletters.
                </p>
                <p>
                    If you choose to subscribe to these communications, your
                    contact information may be used to send the material you
                    requested. Marketing or newsletter communications will
                    provide a way to unsubscribe.
                </p>
                <p>
                    Account-related or administrative communications may
                    still be sent when necessary to operate your account.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Affiliate Links and External Websites
                </h2>
                <p>
                    This website may contain affiliate links, including
                    links to Amazon and other third-party websites. If you
                    follow an affiliate link and make a qualifying purchase,
                    this website may receive a commission at no additional
                    cost to you.
                </p>
                <p>
                    Third-party websites have their own privacy practices
                    and policies. Once you leave this website, information
                    you provide to or that is collected by another website
                    is governed by that website's policies.
                </p>
                <p>
                    For additional information about affiliate relationships
                    on this website, please see the
                    <Link
                        href="/affiliate-disclosure"
                        class="underline hover:text-current"
                        >Affiliate Disclosure</Link
                    >.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Embedded and Third-Party Content
                </h2>
                <p>
                    The website may eventually include content or services
                    provided by third parties, such as YouTube videos,
                    social-media content, or other embedded resources. These
                    services may collect information or use cookies
                    according to their own privacy policies.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Purchases
                </h2>
                <p>
                    At this time, this website does not directly process
                    payments for books or other products. Links may direct
                    visitors to third-party retailers, such as Amazon, where
                    purchases are processed according to the retailer's own
                    terms and privacy policies.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Account and Data Deletion
                </h2>
                <p>
                    Registered users may delete their accounts using the
                    account-management features provided by the website.
                </p>
                <p>
                    You may also contact us with questions or requests
                    concerning personal information associated with your
                    account.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Data Security
                </h2>
                <p>
                    Reasonable technical and administrative measures are
                    used to protect information maintained by this website.
                    However, no method of storing or transmitting
                    information over the Internet can be guaranteed to be
                    completely secure.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Children's Privacy
                </h2>
                <p>
                    This website is intended for a general audience and is
                    not directed specifically toward children under 13. We
                    do not knowingly seek to collect personal information
                    from children under 13.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Changes to This Policy
                </h2>
                <p>
                    This Privacy Policy may be updated as the website, its
                    features, or applicable requirements change. The date at
                    the top of this page will indicate when the policy was
                    most recently updated.
                </p>

                <h2
                    class="font-serif-display pt-4 text-xl font-medium text-current/90"
                >
                    Contact
                </h2>
                <p>
                    Questions about this Privacy Policy or information
                    associated with your account may be sent to:
                </p>
                <p>
                    <a
                        href="mailto:contact@quinnix.com"
                        class="underline hover:text-current"
                        >contact@quinnix.com</a
                    >
                </p>
            </div>
        </article>
    </PublicLayout>
</template>
