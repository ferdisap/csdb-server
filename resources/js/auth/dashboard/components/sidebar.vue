<script setup lang="ts">
import { Calendar, FolderOpenDot, HardDrive, Home, Inbox, Key, List, LockKeyhole, Search, Settings, Trash2, Type, User } from "lucide-vue-next"
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from '@shadcn/components/ui/sidebar'
import { useRouter } from "vue-router";
import Separator from "@shadcn/components/ui/separator/Separator.vue";
import TooltipProvider from "@shadcn/components/ui/tooltip/TooltipProvider.vue";
import Tooltip from "@shadcn/components/ui/tooltip/Tooltip.vue";
import TooltipTrigger from "@shadcn/components/ui/tooltip/TooltipTrigger.vue";
import TooltipContent from "@shadcn/components/ui/tooltip/TooltipContent.vue";
import SidebarMenuSub from "@shadcn/components/ui/sidebar/SidebarMenuSub.vue";
import SidebarMenuSubItem from "@shadcn/components/ui/sidebar/SidebarMenuSubItem.vue";
import SidebarMenuSubButton from "@shadcn/components/ui/sidebar/SidebarMenuSubButton.vue";
import SidebarHeader from "@shadcn/components/ui/sidebar/SidebarHeader.vue";

// Menu items.
const items = [
  {
    title: "OAuth Client",
    url: "/dashboard/oauth-client",
    icon: LockKeyhole,
    description: "display the oauth client that can be accessed by the admin only"
  },
  {
    title: "OAuth Token",
    url: "/dashboard/oauth-token",
    icon: Key,
    description: "display the personal autentication token that user used from the third application "
  },
  {
    title: "Profile",
    url: "/dashboard/biodata",
    icon: User,
  },
];

const items_csdb = [
  {
    title: "Object",
    url: "/dashboard/csdb-object",
    icon: FolderOpenDot,
    sub_items: [
      {
        title: "Create",
        url: "/dashboard/csdb-object/create",
        icon: Type,
      },
      {
        title: "Trash",
        url: "/dashboard/csdb-object/trash",
        icon: Trash2,
      }
    ]
  },
]

const items_dochub = [
  {
    title: "Dochub",
    url: "#",
    icon: Key,
    sub_items: [
      {
        title: "Token",
        url: "/dashboard/dochub/token",
        icon: List,
      },
      // {
      //   title: "Token-self",
      //   url: "/dashboard/dochub/saved-token",
      //   icon: List,
      // }
    ]
  },
]

const router = useRouter();
function goto(url: string) {
  router.push(url);
}

const { open } = useSidebar();
</script>

<template>
  <Sidebar collapsible="icon">
    <SidebarHeader class="w-full">
      <div class="w-full flex items-center justify-center">
        <span :class="['mr-2', open ? '' : 'mt-4 ml-2']"><HardDrive/></span>
        <span v-if="open" class="text-xl text-center py-4">CSDB Server App</span>
      </div>
    </SidebarHeader>
    <SidebarContent>
      <SidebarGroup>
        <SidebarGroupLabel>Account</SidebarGroupLabel>
        <Separator />
        <SidebarGroupContent class="mt-2">
          <SidebarMenu>
            <TooltipProvider>
              <SidebarMenuItem v-for="item in items" :key="item.title">
                <Tooltip>
                  <TooltipTrigger>
                    <SidebarMenuButton asChild>
                      <a :href="item.url" @click.prevent="goto(item.url)">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                      </a>
                    </SidebarMenuButton>
                  </TooltipTrigger>
                  <TooltipContent v-if="item.description"
                    class="border border-light dark:border-dark bg-light dark:bg-dark">
                    <p>{{ item.description }}</p>
                  </TooltipContent>
                </Tooltip>
              </SidebarMenuItem>
            </TooltipProvider>
          </SidebarMenu>
        </SidebarGroupContent>
        <Separator />
        <SidebarGroupLabel>CSDB</SidebarGroupLabel>
        <SidebarGroupContent>
          <SidebarMenu>
            <SidebarMenuItem v-for="item in items_csdb" :key="item.title">
              <SidebarMenuButton asChild>
                <a :href="item.url" @click.prevent="goto(item.url)">
                  <component :is="item.icon" />
                  <span>{{ item.title }}</span>
                </a>
              </SidebarMenuButton>
              <SidebarMenuSub v-for="subitem in item.sub_items" :key="subitem.title">
                <SidebarMenuSubItem>
                  <SidebarMenuSubButton asChild>
                    <a :href="subitem.url" @click.prevent="goto(subitem.url)">
                      <component :is="subitem.icon" />
                      <span>{{ subitem.title }}</span>
                    </a>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
        <Separator />
        <SidebarGroupLabel>Dochub</SidebarGroupLabel>
        <SidebarGroupContent>
          <SidebarMenu>
            <SidebarMenuItem v-for="item in items_dochub" :key="item.title">
              <SidebarMenuButton asChild>
                <a :href="item.url" @click.prevent="goto(item.url)">
                  <component :is="item.icon" />
                  <span>{{ item.title }}</span>
                </a>
              </SidebarMenuButton>
              <SidebarMenuSub v-for="subitem in item.sub_items" :key="subitem.title">
                <SidebarMenuSubItem>
                  <SidebarMenuSubButton asChild>
                    <a :href="subitem.url" @click.prevent="goto(subitem.url)">
                      <component :is="subitem.icon" />
                      <span>{{ subitem.title }}</span>
                    </a>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>
  </Sidebar>
</template>