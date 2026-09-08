<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, ChevronRight, House, Library, Newspaper } from '@lucide/vue';
import { ref } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { computed } from 'vue';
import { dashboard, home } from '@/routes';
import { index as adminPostsIndex } from '@/routes/admin/posts';
import { chapter as chapterRoute, full as fullRoute } from '@/routes/library';
import type { NavItem } from '@/types';

const page = usePage();
const isAdmin = computed(() => Boolean(page.props.auth.user?.is_admin));

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Subscribers Area',
        href: dashboard(),
        icon: Library,
    },
    {
        title: 'Read the Book',
        href: fullRoute(),
        icon: BookOpen,
    },
    ...(isAdmin.value
        ? [
              {
                  title: 'Posts',
                  href: adminPostsIndex(),
                  icon: Newspaper,
              },
          ]
        : []),
]);

const chapterNavItems = [
    { slug: 'front-matter', number: null as number | null, title: 'About This Book' },
    { slug: 'chapter-1', number: 1, title: "You're Not Fighting Anything" },
    { slug: 'chapter-2', number: 2, title: 'Self-Will Run Riot' },
    { slug: 'chapter-3', number: 3, title: 'The Illusion of Control' },
    { slug: 'chapter-4', number: 4, title: 'The Dichotomy of Control' },
    { slug: 'chapter-5', number: 5, title: 'Putting Down the Gavel' },
    { slug: 'chapter-6', number: 6, title: 'The Debt You Keep Paying' },
    { slug: 'chapter-7', number: 7, title: 'The Honest Inventory' },
    { slug: 'chapter-8', number: 8, title: "Carried by Something You Didn't Create" },
    { slug: 'chapter-9', number: 9, title: 'Where Two or Three Are Gathered' },
    { slug: 'chapter-10', number: 10, title: 'The Daily Architecture' },
    { slug: 'chapter-11', number: 11, title: "When It Doesn't Hold" },
    { slug: 'chapter-12', number: 12, title: "Life on Life's Terms" },
];

const chaptersOpen = ref(page.url.startsWith('/library/chapters'));
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <Collapsible v-model:open="chaptersOpen">
                <SidebarGroup class="px-2 py-0">
                    <CollapsibleTrigger as-child>
                        <SidebarGroupLabel
                            as="button"
                            class="w-full cursor-pointer justify-between"
                        >
                            <span>Chapters</span>
                            <ChevronRight
                                class="size-4 shrink-0 transition-transform duration-200"
                                :class="chaptersOpen ? 'rotate-90' : ''"
                            />
                        </SidebarGroupLabel>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenu>
                            <SidebarMenuItem
                                v-for="item in chapterNavItems"
                                :key="item.slug"
                            >
                                <SidebarMenuButton as-child :tooltip="item.title">
                                    <Link :href="chapterRoute(item.slug)">
                                        <span class="truncate">{{
                                            item.number
                                                ? `${item.number}. ${item.title}`
                                                : item.title
                                        }}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </CollapsibleContent>
                </SidebarGroup>
            </Collapsible>
        </SidebarContent>

        <SidebarFooter>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child tooltip="Back to site">
                        <Link :href="home()">
                            <House />
                            <span>Back to Site</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
