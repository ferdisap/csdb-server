<!-- 
 Jika mau login SSO
 1. GET '/login?client=1&redirect={url_to_authorize}' 
-->

<script setup lang="ts">
import { Button } from "@shadcn/components/ui/button/index";
import axios, { AxiosError, AxiosResponse } from "axios";
import { onMounted, ref } from "vue";
import { check_in, login } from "../../utils/autentication";
import Skeleton from "@shadcn/components/ui/skeleton/Skeleton.vue";
import Spinner from "@shadcn/components/ui/spinner/Spinner.vue";
import DarkLight from "resources/js/theme/DarkLight.vue";
import { FormField } from "@shadcn/components/ui/form";
import FormItem from "@shadcn/components/ui/form/FormItem.vue";
import FormLabel from "@shadcn/components/ui/form/FormLabel.vue";
import FormControl from "@shadcn/components/ui/form/FormControl.vue";
import FormDescription from "@shadcn/components/ui/form/FormDescription.vue";
import FormMessage from "@shadcn/components/ui/form/FormMessage.vue";
import Input from "@shadcn/components/ui/input/Input.vue";
import Checkbox from "@shadcn/components/ui/checkbox/Checkbox.vue";
import { useForm } from "vee-validate";
import { api } from "resources/js/utils/fetch";
import { LOGIN_URI } from "resources/js/config/app.config";

const search_param = new URLSearchParams(window.location.search);
const is_SSO = search_param.get("client") === "1";
const submitProcess = ref(false);

const formUI = useForm({
    initialValues: {
        remember: false,
        email: "",
        password: "",
    },
});

const SignIn = formUI.handleSubmit((formData: any) => {
    submitProcess.value = true;
    const search_param = new URLSearchParams(window.location.search);
    const redirect_to = search_param.get("redirect_to");
    if (redirect_to) {
        formData["redirect_to"] = redirect_to;
    }

    api.post(LOGIN_URI, formData)
        .then((response) => {
            const data = response.data;
            const search_param = new URLSearchParams(window.location.search);
            const redirect_to = search_param.get("redirect_to");

            if (redirect_to) {
                window.location.href = redirect_to;
            } else if (data.redirect) {
                window.location.href = data.redirect;
            }

            const errors: undefined | Record<string, string[]> = (
                response.data as Record<string, {}>
            ).errors;
            if (errors) {
                formUI.setErrors(errors);
            }
        })
        .catch((e) => {
            const data = e.response?.data as Record<string, {}>;
            if (data && data.errors) {
                formUI.setErrors(data.errors);
            } else if (data && data.message) {
                formUI.setFieldError("email", data.message as string);
                formUI.setFieldError("password", data.message as string);
            }
        })
        .finally(() => {
            submitProcess.value = false;
        });
    return;
    login(
        "/login",
        formData,
        async (response: AxiosResponse) => {
            if (is_SSO) {
                await check_in();
            }

            const data = response.data;

            const search_param = new URLSearchParams(window.location.search);
            const redirect_to = search_param.get("redirect_to");
            if (redirect_to) {
                window.location.href = redirect_to;
            } else if (data.redirect) {
                window.location.href = data.redirect;
            }

            const errors: undefined | Record<string, string[]> = (
                response.data as Record<string, {}>
            ).errors;
            if (errors) {
                formUI.setErrors(errors);
            }

            submitProcess.value = false;
        },
        (e: AxiosError) => {
            submitProcess.value = false;
            const data = e.response?.data as Record<string, {}>;
            if (data && data.errors) {
                formUI.setErrors(data.errors);
            } else if (data && data.message) {
                formUI.setFieldError("email", data.message as string);
                formUI.setFieldError("password", data.message as string);
            }
        }
    );
});

const showSkeleton = ref(1);

onMounted(async () => {
    if (is_SSO) {
        await check_in();
    }

    // karena route ini (GET '/login') bisa tidak ada middleware maka pakai ini
    axios
        .get("/is-auth", {
            headers: {
                "Cache-Control": "no-cache, no-store, must-revalidate",
                Pragma: "no-cache",
                Expires: "0",
            },
        })
        .then((response) => {
            if (response.statusText === "OK") {
                const search_param = new URLSearchParams(
                    window.location.search
                );
                const redirect_to = search_param.get("redirect_to");
                if (redirect_to) {
                    // window.location.href = redirect_to;
                } else {
                    // window.location.href = "/";
                }
            }
        })
        .catch((e) => {
            console.log(e);
            showSkeleton.value = 0;
        });
});
</script>
<template>
    <section>
        <div
            class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0"
        >
            <a
                href="#"
                class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white"
                >CSDB Server</a
            >
            <Skeleton
                v-if="showSkeleton"
                class="w-full rounded-lg md:mt-0 sm:max-w-md xl:p-0 h-80"
            />
            <div
                v-else
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-neutral-800 dark:border-gray-700"
            >
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white"
                    >
                        Sign in to your account
                    </h1>
                    <form
                        class="space-y-4 md:space-y-6"
                        action="/login"
                        method="post"
                        @submit.prevent="SignIn"
                    >
                        <FormField
                            v-slot="{ componentField }"
                            name="email"
                            as="div"
                        >
                            <FormItem>
                                <FormLabel
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Your email</FormLabel
                                >
                            </FormItem>
                            <FormControl>
                                <Input
                                    type="text"
                                    placeholder="name@company.com"
                                    v-bind="componentField"
                                    class="h-12 bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormField>
                        <FormField
                            v-slot="{ componentField }"
                            name="password"
                            as="div"
                        >
                            <FormItem>
                                <FormLabel
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    >Password
                                </FormLabel>
                            </FormItem>
                            <FormControl>
                                <Input
                                    type="text"
                                    placeholder="••••••••"
                                    v-bind="componentField"
                                    class="h-12 bg-neutral-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-neutral-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormField>
                        <FormField
                            v-slot="{ value, handleChange, componentField }"
                            name="remember"
                            as="div"
                            class="flex items-center justify-between"
                        >
                            <FormItem>
                                <FormControl class="flex">
                                    <div class="flex items-center">
                                        <Checkbox
                                            class="mr-2"
                                            id="remember"
                                            :model-value="value"
                                            @update:model-value="handleChange"
                                        />
                                        <FormLabel for="remember"
                                            >Remember me</FormLabel
                                        >
                                    </div>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                            <a
                                href="#"
                                class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500"
                                >Forgot password?</a
                            >
                        </FormField>
                        <Button
                            type="submit"
                            class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        >
                            Sign in
                            <Spinner v-if="submitProcess" />
                        </Button>
                        <p
                            class="text-sm font-light text-gray-500 dark:text-gray-400"
                        >
                            Don’t have an account yet?
                            <a
                                href="/register?redirect_to=/email/verify"
                                class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                >Sign up</a
                            >
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
