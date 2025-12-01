<script setup
        lang="ts">
        // import { Button } from '@shadcn/components/ui/button'
        import { Button } from "@shadcn/components/ui/button/index";
        import { AxiosError, AxiosResponse } from "axios";
        import DarkLight from "resources/js/theme/DarkLight.vue";
        import { ref } from "vue";
        import {
          check_in,
          generateSession,
          register,
        } from "resources/js/utils/autentication";
        import Skeleton from "@shadcn/components/ui/skeleton/Skeleton.vue";
        import Spinner from "@shadcn/components/ui/spinner/Spinner.vue";
        import { FormField } from "@shadcn/components/ui/form";
        import FormItem from "@shadcn/components/ui/form/FormItem.vue";
        import FormLabel from "@shadcn/components/ui/form/FormLabel.vue";
        import FormControl from "@shadcn/components/ui/form/FormControl.vue";
        import Input from "@shadcn/components/ui/input/Input.vue";
        import FormMessage from "@shadcn/components/ui/form/FormMessage.vue";
        import { useForm } from "vee-validate";
        import { api } from "resources/js/utils/fetch";
        import { REGISTER_URI } from "resources/js/config/app.config";

        const submitProcess = ref(false);
        const formUI = useForm({});

        const SignUp = formUI.handleSubmit((formData: any) => {
          api.post(REGISTER_URI, formData)
            .then((response) => {
              const search_param = new URLSearchParams(window.location.search);
              const redirect_to = search_param.get("redirect_to") || response.data.redirect || "/dashboard";
              window.location.href = redirect_to;
            })
            .catch((e) => {
              const errors: undefined | Record<string, string[]> = (
                e.response?.data as Record<string, {}>
              ).errors;
              if (errors) {
                formUI.setErrors(errors);
              }
            })
            .finally(() => {
              submitProcess.value = false;
            });
        });
</script>
<template>
  <section>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">Example App</a>
      <div
        class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-neutral-800 dark:border-gray-700">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
          <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            Register your account
          </h1>
          <form class="space-y-4 md:space-y-6" action="/register" method="post" @submit.prevent="SignUp">
            <FormField v-slot="{ componentField }" name="name" as="div">
              <FormItem>
                <FormLabel class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your name</FormLabel>
              </FormItem>
              <FormControl>
                <Input type="text" placeholder="luffy" v-bind="componentField"
                  class="h-12 bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
              </FormControl>
              <FormMessage />
            </FormField>
            <FormField v-slot="{ componentField }" name="email" as="div">
              <FormItem>
                <FormLabel class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</FormLabel>
              </FormItem>
              <FormControl>
                <Input type="text" placeholder="name@company.com" v-bind="componentField"
                  class="h-12 bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
              </FormControl>
              <FormMessage />
            </FormField>
            <FormField v-slot="{ componentField }" name="password" as="div">
              <FormItem>
                <FormLabel class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password
                </FormLabel>
              </FormItem>
              <FormControl>
                <Input type="text" placeholder="••••••••" v-bind="componentField"
                  class="h-12 bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
              </FormControl>
              <FormMessage />
            </FormField>
            <FormField v-slot="{ componentField }" name="password_confirmation" as="div">
              <FormItem>
                <FormLabel class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password
                </FormLabel>
              </FormItem>
              <FormControl>
                <Input type="text" placeholder="••••••••" v-bind="componentField"
                  class="h-12 bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
              </FormControl>
              <FormMessage />
            </FormField>
            <!-- <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                                name</label>
                            <input type="name" name="name" id="name"
                                class="bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="luffy" />
                        </div> -->
            <!-- <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                                email</label>
                            <input type="email" name="email" id="email"
                                class="bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="name@company.com" />
                        </div> -->
            <!-- <div>
                            <label for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                class="bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div> -->
            <!-- <div>
                            <label for="password_confirmation"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password
                                Confirmation</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="••••••••"
                                class="bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div> -->
            <Button type="submit"
              class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
              Sign Up
              <Spinner v-if="submitProcess" />
            </Button>
            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
              Already registered?
              <a href="/login" class="font-medium text-blue-600 hover:underline dark:text-blue-500">Sign in</a>
            </p>
          </form>
          <div class="float-right mb-3">
            <DarkLight />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
