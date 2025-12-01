<script setup lang="ts">
import { useTheme } from './dark_light';
import TooltipProvider from '@shadcn/components/ui/tooltip/TooltipProvider.vue';
import Tooltip from '@shadcn/components/ui/tooltip/Tooltip.vue';
import TooltipTrigger from '@shadcn/components/ui/tooltip/TooltipTrigger.vue';
import TooltipContent from '@shadcn/components/ui/tooltip/TooltipContent.vue';
import Switch from '@shadcn/components/ui/switch/Switch.vue';
import { Primitive } from 'reka-ui';
import { cn } from '@shadcn/lib/utils';
import { HTMLAttributes } from 'vue';

const { applyLightTheme, applyDarkTheme, isDark, toggleTheme } = useTheme();

const props = withDefaults(
  defineProps<{
    as?: string
    class?: HTMLAttributes["class"]
    asChild?: boolean
  }>(),
  {
    as: "div", // default jadi <div>
  }
)
</script>

<template>
  <Primitive data-slot="div" :as="as" :as-child="asChild"
    :class="cn('', props.class)">
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger class="flex items-center" as="div">
          <Switch id="theme" class="mr-1" @click="toggleTheme" />
          <i class="icon-dark" v-if="isDark"></i>
          <i class="icon-light" v-else></i>
        </TooltipTrigger>
        <TooltipContent class="border border-light dark:border-dark bg-light dark:bg-dark text-xs">
          <p>Switch to light theme</p>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </Primitive>
</template>