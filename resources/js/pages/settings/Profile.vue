<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useInitials } from '@/composables/useInitials';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const { getInitials } = useInitials();

const avatarPreview = ref<string | null>(null);
const avatarSrc = computed(() => avatarPreview.value ?? user.value.avatar_url);

function onAvatarChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (avatarPreview.value) {
        URL.revokeObjectURL(avatarPreview.value);
    }

    avatarPreview.value = file ? URL.createObjectURL(file) : null;
}
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profile"
            description="Update your name and email address"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="flex items-center gap-4">
                <Avatar class="size-16">
                    <AvatarImage
                        v-if="avatarSrc"
                        :src="avatarSrc"
                        :alt="user.name"
                    />
                    <AvatarFallback class="text-lg">{{
                        getInitials(user.display_name || user.name)
                    }}</AvatarFallback>
                </Avatar>

                <div class="grid gap-2">
                    <Label for="avatar">Profile picture</Label>
                    <input
                        id="avatar"
                        type="file"
                        name="avatar"
                        accept="image/*"
                        class="text-muted-foreground text-sm"
                        @change="onAvatarChange"
                    />
                    <InputError :message="errors.avatar" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    placeholder="Full name"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="display_name">Display name</Label>
                <Input
                    id="display_name"
                    class="mt-1 block w-full"
                    name="display_name"
                    :default-value="user.display_name ?? ''"
                    autocomplete="off"
                    placeholder="How your name appears publicly"
                />
                <InputError class="mt-2" :message="errors.display_name" />
            </div>

            <div class="grid gap-2">
                <Label for="bio">Bio</Label>
                <Textarea
                    id="bio"
                    class="mt-1 block w-full"
                    name="bio"
                    :default-value="user.bio ?? ''"
                    placeholder="A short line about yourself"
                />
                <InputError class="mt-2" :message="errors.bio" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="Email address"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div v-if="page.props.mustVerifyEmail && !user.email_verified_at">
                <p class="text-muted-foreground -mt-4 text-sm">
                    Your email address is unverified.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-if="page.props.status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button"
                    >Save</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
