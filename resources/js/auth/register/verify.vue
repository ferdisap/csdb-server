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
import { ref, computed, onMounted, onUnmounted } from "vue";

const { applyLightTheme, applyDarkTheme, isDark } = useTheme();


const message = ref<null|string>(null);

// ##### elapsed time #####
const count = ref<number>(0);
const startTime = ref<number | null>(null);
const timer = ref<ReturnType<typeof setInterval> | null>(null);

const limit = 60; // seconds
const remaining = ref<number>(limit);

function increment(): void {
  if (!startTime.value) {
    startTime.value = Date.now();
    startCountdown();
  }

  if (remaining.value > 0) {
    count.value++;
  } else {
    resetCounter();
    count.value = 1;
    startTime.value = Date.now();
    startCountdown();
  }
}
function startCountdown(): void {
  const time = 1000; // milisecond
  timer.value = setInterval(() => {
    if (startTime.value) {
      const diff = Math.floor((Date.now() - startTime.value) / time);
      remaining.value = Math.max(limit - diff, 0);

      if (remaining.value <= 0) {
        resetCounter();
      }
    }
  }, time);
}
function resetCounter(): void {
  if (timer.value) {
    clearInterval(timer.value);
    timer.value = null;
    message.value = null
  }
  count.value = 0;
  startTime.value = null;
  remaining.value = limit;
}
// Format remaining as MM:SS
const formattedRemaining = computed(() => {
  const minutes = Math.floor(remaining.value / 60);
  const seconds = remaining.value % 60;
  return `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(
    2,
    "0"
  )}`;
});
onUnmounted(() => {
  if (timer.value) clearInterval(timer.value);
});

// ##### end of elapsed time #####

// ##### Send verification link #####
function Resend(e: SubmitEvent) {
  const url = (e.target as HTMLFormElement).getAttribute("action")!;
  axios.post(url, {}).then((rst) => {
    const status = rst.status;
    message.value = rst.data.message;
    if (status === 202) {
      increment();
    } 
  });
}
</script>
<template>
  <section>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">Example App</a>
      <div
        class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
          <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            Verify your account
          </h1>
          <form class="space-y-4 md:space-y-6" action="/email/verification-notification" method="get"
            @submit.prevent="Resend">
            <p class="dark:text-white">
              Check the verification link in your email. If
              nothing, click resend button below.
            </p>
            <p v-if="message" class="dark:text-white">
              <span>Respond: </span>
              <br>
              {{ message }}
            </p>
            <Button type="submit" :disabled="count"
              class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
              Resend
              <strong v-if="count">{{
                formattedRemaining
              }}</strong>
            </Button>
            <!-- <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Already registered?
                            <a href="/login" class="font-medium text-blue-600 hover:underline dark:text-blue-500">Sign
                                in</a>
                        </p> -->
          </form>
          <div class="float-right mb-3">
            <TooltipProvider v-if="isDark">
              <Tooltip>
                <TooltipTrigger>
                  <Button type="button" @click="applyLightTheme" variant="outline" size="icon"
                    class="border border-gray-300 rounded-md p-2 text-gray-300 hover:bg-gray-100 hover:text-black h-9 w-9 bg-background shadow-sm text-sm font-medium mr-1">
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
                    class="border border-gray-300 rounded-md p-2 text-gray-300 hover:bg-gray-100 hover:text-black h-9 w-9 bg-background shadow-sm text-sm font-medium mr-1">
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
