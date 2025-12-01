<script setup lang="ts">
import { useTheme } from "../../theme/dark_light";
import Sidebar from './components/sidebar.vue';
import { SidebarProvider } from '@shadcn/components/ui/sidebar';
import Topbar from './components/topbar.vue';
import { Separator } from '@shadcn/components/ui/separator';
import OAuthClient from './main/OAuthClient/index.vue';
import { Toaster } from "resources/js/utils/toast.ts";

const { applyLightTheme, applyDarkTheme, isDark } = useTheme();

</script>
<template>
    <Toaster position="top-center"/>
    <SidebarProvider style="--sidebar-width: 16rem; --sidebar-width-mobile: 20rem;">
        <Sidebar />
        <div class="w-full">
            <Topbar />
            <Separator />
            <router-view v-slot="{ Component }">
                <keep-alive>
                    <component :is="Component" />
                </keep-alive>
            </router-view>
        </div>
    </SidebarProvider>
</template>
