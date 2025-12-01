<script setup lang="ts">
import { SidebarTrigger } from '@shadcn/components/ui/sidebar';
import { Separator } from '@shadcn/components/ui/separator';
import {
  Breadcrumb,
  BreadcrumbEllipsis,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from "@shadcn/components/ui/breadcrumb"
import { computed, ref } from 'vue';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@shadcn/components/ui/dropdown-menu'
import { useRoute } from 'vue-router';
import Avatar from '../../login/Avatar.vue';
import DarkLight from 'resources/js/theme/DarkLight.vue';

interface CrumbData {
  url: string | null,
  name: string | null,
  elipsis: Array<CrumbData> | null
}

function urlSegment(k: number, segments: Array<string>): string {
  return window.location.origin + '/' + segments.filter((v, i) => i <= k).join("/");
}

const route = useRoute();
const qty_minimalToUseElipsis = 3;
const qty_minimalDropdownItem = 2;
const qty_crumbItemAfterElipsis = 2;
const pathSegments = computed(() => {
  const segments: Array<string> = (Object.values(route.params) ? route.path : window.location.pathname).split('/').filter((v) => v);
  const newSegments: Array<CrumbData> = [];

  if (segments.length > qty_minimalToUseElipsis) {
    // crumb item 1
    newSegments.push({
      name: segments[0],
      url: window.location.origin + '/' + segments[0],
      elipsis: null
    });

    // crumb item 2 atau elipsis
    if (segments.length - qty_crumbItemAfterElipsis === (qty_minimalDropdownItem)) {
      newSegments.push({
        name: segments[1],
        url: urlSegment(1, segments),
        elipsis: null
      });
    } else {
      newSegments[1] = { url: null, name: null, elipsis: [] };
      for (let i = 1; i < segments.length - qty_crumbItemAfterElipsis; i++) {
        newSegments[1].elipsis!.push({
          name: segments[i],
          url: urlSegment(i, segments),
          elipsis: null
        });
      }
    }

    // crumb item nomor 2 terakhir
    newSegments.push({
      name: segments[segments.length - qty_crumbItemAfterElipsis],
      url: urlSegment(segments.length - qty_crumbItemAfterElipsis, segments),
      elipsis: null
    });

    // crumb item terakhir
    newSegments.push({
      name: segments[segments.length - 1],
      url: urlSegment(segments.length - 1, segments),
      elipsis: null
    });
  }
  else {
    for (let i = 0; i < segments.length; i++) {
      newSegments.push({
        name: segments[i],
        url: urlSegment(i, segments),
        elipsis: null
      });
    }
  }

  return newSegments;
})
const isEndBreadCrumb = (k: number, qtyLength: number) => {
  return ((qtyLength - k) === 1)
}
</script>
<template>
  <div class="flex items-center h-12 justify-between bg-light dark:bg-dark p-4">
    <div class="flex items-center h-8 gap-2">
      <SidebarTrigger />
      <Separator orientation="vertical" class="ml-1 mr-2" />
      <Breadcrumb class="flex-nowrap mr-2">
        <BreadcrumbList class="flex flex-nowrap">
          <BreadcrumbItem v-for="(segment, k) in pathSegments" class="whitespace-nowrap">
            <BreadcrumbLink as-child v-if="!segment.elipsis">
              <a :href="segment.url!">{{ segment.name }}</a>
              <BreadcrumbSeparator v-if="!isEndBreadCrumb(k, pathSegments.length)" />
            </BreadcrumbLink>
            <BreadcrumbEllipsis v-else>
              <DropdownMenu>
                <DropdownMenuTrigger class="flex items-center gap-1">
                  <BreadcrumbEllipsis />
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start">
                  <DropdownMenuItem v-for="segmentEl in segment.elipsis">
                    <a :href="segmentEl.url!">{{ segmentEl.name }}</a>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
              <BreadcrumbSeparator />
            </BreadcrumbEllipsis>
          </BreadcrumbItem>
        </BreadcrumbList>
      </Breadcrumb>

      <Separator orientation="vertical" class="mr-2" />
    </div>

    <!-- Kanan: icon -->
    <div class="flex items-center gap-3 h-8">
      <DarkLight />
      <Separator orientation="vertical" class="mx-1" />
      <Avatar />
    </div>
  </div>
  <!-- <div class="flex h-12 py-2 pl-1 w-full items-center">
    <SidebarTrigger />
    <Separator orientation="vertical" class="ml-1 mr-2" />
    <Breadcrumb class="flex-nowrap mr-2">
      <BreadcrumbList class="flex flex-nowrap">
        <BreadcrumbItem v-for="(segment, k) in pathSegments" class="whitespace-nowrap">
          <BreadcrumbLink as-child v-if="!segment.elipsis">
            <a :href="segment.url!">{{ segment.name }}</a>
            <BreadcrumbSeparator v-if="!isEndBreadCrumb(k, pathSegments.length)" />
          </BreadcrumbLink>
          <BreadcrumbEllipsis v-else>
            <DropdownMenu>
              <DropdownMenuTrigger class="flex items-center gap-1">
                <BreadcrumbEllipsis />
              </DropdownMenuTrigger>
              <DropdownMenuContent align="start">
                <DropdownMenuItem v-for="segmentEl in segment.elipsis">
                  <a :href="segmentEl.url!">{{ segmentEl.name }}</a>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
            <BreadcrumbSeparator />
          </BreadcrumbEllipsis>
        </BreadcrumbItem>
      </BreadcrumbList>
    </Breadcrumb>
    <Separator orientation="vertical" class="mr-2"/>
     <div class="flex w-[100px] border">
       <DarkLight class="w-20"/>
       <Separator orientation="vertical" class="mx-1"/>
       <Avatar class="w-20"/>
     </div>
  </div> -->
</template>
