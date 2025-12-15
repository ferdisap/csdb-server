<script setup lang="ts">
import 'vue-sonner/style.css'
import { toast } from 'vue-sonner';
import { toTypedSchema } from "@vee-validate/zod"
import { h, ref, watch } from "vue"
import * as z from "zod"

import { Button } from "@shadcn/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@shadcn/components/ui/dialog"
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@shadcn/components/ui/form"
import { Input } from "@shadcn/components/ui/input"
import { injectDialogRootContext } from 'reka-ui';
import { useRoute } from 'vue-router';
import axios, { AxiosError } from 'axios';
import { useForm } from 'vee-validate';
import { castToToken, token } from './dochub_token_column';
import Separator from '@shadcn/components/ui/separator/Separator.vue';
import Checkbox from '@shadcn/components/ui/checkbox/Checkbox.vue';
import DialogClose from '@shadcn/components/ui/dialog/DialogClose.vue';
import { fail, loading, success, warning } from 'resources/js/utils/toast';

const open = ref(false);
const emit = defineEmits(['deletedToken']) // ✅ wajib dideklarasikan
const route = useRoute();
const submitting = (formData: any) => {
  const id = route.params["id"];
  return new Promise<any>((resolve, reject) => {
    axios.post(`/dochub/token/${id}/delete`, formData)
      .then(response => {
        resolve(response.data);
      })
      .catch((e: AxiosError) => {
        reject(e);
      })
  })
}

const form = useForm({
  initialValues: {
    deleted: false
  }
});

const onSubmited = form.handleSubmit((values: any) => {
  const toastId = loading("Deleting ...");
  submitting(values)
    .then((data: any) => {
      open.value = false;
      const token = castToToken(data.token);
      emit('deletedToken', token);
      success(data.message ? data.message : 'Deletion success.', null, toastId);
    })
    .catch((e: AxiosError) => {
      const responseData = e.response?.data as { errors?: {}; message?: string } || {};
      const errors = responseData.errors;
      if (errors) {
        form.setErrors(errors);
      }
      const responseMessage = responseData.message;
      if(e.response && e.response.status === 422){
        warning(e.message + (responseMessage ? `.\n ${responseMessage}` : ''), null, toastId);
      }
      else {
        fail(e.message + (responseMessage ? `.\n ${responseMessage}` : ''), null, toastId);
      }
    })
});

watch(open, (val) => {
  form.resetForm(); // reset ke initialValues
});

</script>

<template>
  <Dialog :open="open" @update:open="open = $event">
    <DialogTrigger as-child>
      <Button class="ml-2" variant="destructive">Delete</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Revoke token id</DialogTitle>
        <DialogDescription>
          Make changes to your profile here. Click save when you're done.
        </DialogDescription>
      </DialogHeader>

      <form id="dialogForm" @submit.prevent="onSubmited">
        <FormField v-slot="{ value, handleChange }" name="deleted">
          <FormItem class="mb-4">
            <FormControl class="flex">
              <div class="flex items-center">
                <Checkbox class="mr-2" id="deleted_token_form" :model-value="value"
                  @update:model-value="handleChange" />
                <FormLabel for="deleted_token_form">Delete</FormLabel>
              </div>
            </FormControl>
            <FormDescription>
              The tokens will be clearly deleted from database.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
        <Separator class="mb-3" />
        <FormField v-slot="{ componentField }" name="password">
          <FormItem>
            <FormLabel>Password</FormLabel>
            <FormControl>
              <Input type="password" placeholder="your account password here.." v-bind="componentField" />
            </FormControl>
            <FormDescription>
              This password is used to ensure you put the right decision.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
      </form>

      <DialogFooter>
        <Button type="submit" form="dialogForm" variant="destructive">
          Delete
        </Button>
        <DialogClose as-child id="closebtn">
          <Button type="button" form="dialogForm">
            Close
          </Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>