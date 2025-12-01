<script setup lang="ts">
// import { Button } from '@@/shadcn/components/ui/button'
import { Button } from "@shadcn/components/ui/button/index";
import { useTheme } from "../../theme/dark_light";
import axios from "axios";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@shadcn/components/ui/tooltip";

const { applyLightTheme, applyDarkTheme, isDark } = useTheme();

</script>
<template>
    <section>
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">Example
                App</a>
            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-neutral-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Verification Status
                    </h1>
                    <form class="space-y-4 md:space-y-6" action="/email/verification-notification" method="get" @submit.prevent="Resend">
                        <p class="dark:text-white">
                            You have been verified!
                        </p>
                        <p class="dark:text-white">
                            Return to your dashboard by clicking
                            <a class="text-[revert] underline" href="/dashboard">here.</a>
                        </p>
                    </form>
                    <div class="float-right mb-3">
                        <TooltipProvider v-if="isDark">
                            <Tooltip>
                                <TooltipTrigger>
                                    <Button type="button" @click="applyLightTheme" variant="outline" size="icon"
                                        class="border border-gray-300 rounded-md p-2 text-gray-300 hover:bg-neutral-100 hover:text-black h-9 w-9 bg-background shadow-sm text-sm font-medium mr-1">
                                        <i class="icon-light"></i>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent class="border bg-red-400">
                                    <p>Switch to light theme</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider v-else>
                            <Tooltip>
                                <TooltipTrigger>
                                    <Button type="button" @click="applyDarkTheme" variant="outline" size="icon"
                                        class="border border-gray-300 rounded-md p-2 text-gray-300 hover:bg-neutral-100 hover:text-black h-9 w-9 bg-background shadow-sm text-sm font-medium mr-1">
                                        <i class="icon-dark"></i>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Switch to dark theme</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
