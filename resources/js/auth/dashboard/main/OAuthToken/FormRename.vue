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
import DialogClose from '@shadcn/components/ui/dialog/DialogClose.vue';
import axios, { AxiosError } from 'axios';
import { useRoute } from 'vue-router';
import { castToToken, token } from './oauth_token_column';
import { useForm } from 'vee-validate';
import Separator from '@shadcn/components/ui/separator/Separator.vue';
import { fail, loading, success, warning } from 'resources/js/utils/toast';

const props = defineProps<{
  // class?: HTMLAttributes["class"]
  data: {token:token}
}>();

const open = ref(false);
const emit = defineEmits(['tokenUpdated']) // ✅ wajib dideklarasikan
const route = useRoute();
// const submitting = () => new Promise<string>((resolve) => setTimeout(() => resolve('fufufafa'), 1000));
const submitting = (formData: any) => {
  const id = route.params["id"];
  return new Promise<any>((resolve, reject) => {
    // return resolve({
    //   "message": "bla bla bla",
    //   "token": {
    //     "id": "0199d197-d68e-7310-ba42-933a68f5dc08",
    //     "name": "Tes Reg App xxx",
    //     "provider": "users",
    //     "redirect_uris": [
    //       "http:\/\/regApp.xyz\/oauth\/callback"
    //     ],
    //     "grant_types": [
    //       "authorization_code",
    //       "refresh_token"
    //     ],
    //     "revoked": false,
    //     "updated_at": "2025-10-11T04:46:53.000000Z",
    //     "owner": {
    //       "name": "Nami",
    //       "email": "nami@example.com"
    //     }
    //   }
    // });
    axios.post(`/oauth-token/${id}/update`, formData)
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
    name: props.data.token.name
  }
});

const onSubmited = form.handleSubmit((values: any) => {
  const toastId = loading("Renaming ...");
  submitting(values)
    .then((data: any) => {
      open.value = false;
      const token = castToToken(data.token);
      emit('tokenUpdated', token);
      success(data.message ? data.message : 'Update success.', null, toastId);
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

watch(
  () => props.data,
  (newData) => {
    form.setValues({
      name: newData.token.name
    })
  },
  { deep: true, immediate: true }
)

</script>

<template>
  <Dialog :open="open" @update:open="open = $event">
    <DialogTrigger as-child>
      <Button class="ml-2" variant="secondary">Rename</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Edit Token Name</DialogTitle>
        <DialogDescription>
          Make changes to your application name. Click save when you're done.
        </DialogDescription>
      </DialogHeader>

      <form id="dialogForm" @submit.prevent="onSubmited">
        <FormField v-slot="{ componentField }" name="name">
          <FormItem class="mb-4">
            <FormLabel>Token Name</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Example App" v-bind="componentField" :default-value="props.data.token.name"/>
            </FormControl>
            <FormDescription>
              This name is a relation to other application that login using this application.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
        <Separator class="mb-3" />
        <FormField v-slot="{ componentField }" name="password">
          <FormItem>
            <FormLabel>Password</FormLabel>
            <FormControl>
              <Input type="password" placeholder="your account password here.." v-bind="componentField"/>
            </FormControl>
            <FormDescription>
              This password is used to ensure you put the right decision.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
      </form>

      <DialogFooter>
        <Button type="submit" form="dialogForm">
          Save changes
        </Button>
        <DialogClose as-child id="closebtn">
          <Button type="button" form="dialogForm">
            Close
          </Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
  <!-- <Form as="" keep-values>
  </Form> -->
</template>