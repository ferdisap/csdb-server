<script setup lang="ts">
import 'vue-sonner/style.css'
import { toast } from 'vue-sonner';
import { toTypedSchema } from "@vee-validate/zod"
import { h, onMounted, reactive, ref, watch } from "vue"
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
  FORM_ITEM_INJECTION_KEY
} from "@shadcn/components/ui/form"
import { Input } from "@shadcn/components/ui/input"
import { injectDialogRootContext } from 'reka-ui';
import DialogClose from '@shadcn/components/ui/dialog/DialogClose.vue';
import Checkbox from '@shadcn/components/ui/checkbox/Checkbox.vue';
import Separator from '@shadcn/components/ui/separator/Separator.vue';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useForm } from 'vee-validate';
import { castToClient, client } from './oauth_client_column';
import { fail, loading, success, warning } from 'resources/js/utils/toast';
import RadioGroup from '@shadcn/components/ui/radio-group/RadioGroup.vue';
import RadioGroupItem from '@shadcn/components/ui/radio-group/RadioGroupItem.vue';
import Label from '@shadcn/components/ui/label/Label.vue';

// const formSchema = toTypedSchema(z.object({
//   username: z.string().min(2).max(50),
// }))

const emit = defineEmits(['newClientRegistered']) // ✅ wajib dideklarasikan
const open = ref(false);
const submitting = (formData: any) => {
  return new Promise<any>((resolve, reject) => {
    // return resolve({
    //   client: {
    //     "id": "axaxaid",
    //     "name": "tes Register App",
    //     "provider": null,
    //     "redirect_uris": [
    //       "https:\/\/third-party-app.com\/callback"
    //     ],
    //     "grant_types": [
    //       "authorization_code",
    //       "refresh_token",
    //       "urn:ietf:params:oauth:grant-type:device_code"
    //     ],
    //     "revoked": false,
    //     "updated_at": "2025-10-10T23:34:21.000000Z",
    //     "owner": {
    //       "name": "Nami",
    //       "email": "nami@example.com"
    //     }
    //   },
    // })
    axios.post("/oauth-client/register", formData)
      .then(response => {
        resolve(response.data);
      })
      .catch((e: AxiosError) => {
        reject(e);
      })
  })
}

const initialValueForm = {
  confidential: true,
  device_flow: false,
}
const form = useForm({
  initialValues: initialValueForm
});

// const responseDataDummy = {
//   client: {
//     "id": "axaxaid",
//     "name": "tes Register App",
//     "provider": null,
//     "redirect_uris": [
//       "https:\/\/third-party-app.com\/callback"
//     ],
//     "grant_types": [
//       "authorization_code",
//       "refresh_token",
//       "urn:ietf:params:oauth:grant-type:device_code"
//     ],
//     "revoked": false,
//     "updated_at": "2025-10-10T23:34:21.000000Z",
//     "owner": {
//       "name": "Nami",
//       "email": "nami@example.com"
//     }
//   },
// }


const onSubmited = form.handleSubmit(async (values: any) => {
  const toastId = loading("Registering ...");
  axios.post("/oauth-client/register", values)
    .then((response: AxiosResponse) => {
      const data = response.data
      open.value = false;
      const client = castToClient(data.client);
      emit('newClientRegistered', client);
      success(data.message ? data.message : 'Registration success.', null, toastId);
      // return data.message ? data.message : 'Registration success.'; // untuk toast
    })
    .catch((e: AxiosError) => {

      // Safely access errors if present
      const responseData = e.response?.data as { errors?: {}; message?: string } || {};
      const errors = responseData.errors;
      if (errors) {
        form.setErrors(errors);
      }
      const responseMessage = responseData.message;

      if (e.response && e.response.status === 422) {
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

const client_type = ref('');

</script>

<template>
  <Dialog :open="open" @update:open="open = $event">
    <DialogTrigger as-child>
      <Button class="ml-2" variant="default">New</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Register New OAuth Client</DialogTitle>
        <DialogDescription>
          Fill the form then click register when you're done.
        </DialogDescription>
      </DialogHeader>
      <form id="dialogForm" @submit.prevent="onSubmited">
        <Separator class="mb-3" />
        <FormField v-slot="{ componentField }" name="app_name">
          <FormItem class="mb-4">
            <FormLabel>App Name</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Example App" v-bind="componentField" />
            </FormControl>
            <FormDescription>
              This name is an third party application's name which can access some feature of this application.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
        <Separator class="mb-3" />
        <!-- switch client type -->
        <FormField v-slot="{ value, componentField }" name="client_type">
          <FormItem class="mb-4">
            <FormLabel>Client Type</FormLabel>
            <FormControl>
              <RadioGroup class="flex items-center h-8" v-bind="componentField">
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r1" value="client_credential" @click="client_type = 'client_credential'" />
                  <Label for="r1">Client Credential</Label>
                </div>
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r2" value="password_grant" @click="client_type = 'password_grant'"/>
                  <Label for="r2">Password Grant</Label>
                </div>
              </RadioGroup>
            </FormControl>
            <FormDescription>
              This type is used to determine wheter you want to generate client credential or password grant.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
        <Separator class="mb-3" />
        <!-- redirect uris -->
        <FormField v-if="client_type === 'password_grant'" v-slot="{ componentField }" name="redirect_uris">
          <FormItem class="mb-4">
            <FormLabel>Redirect Uris</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Example App" v-bind="componentField" />
            </FormControl>
            <FormDescription>
              This uri is used for Authorization Server to send 'authorization code' or 'access token' after user
              succeed login. Use ',' if you have more than one uri.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
        <Separator v-if="client_type === 'password_grant'" class="mb-3" />
        <FormField v-if="client_type === 'password_grant'" v-slot="{ value, handleChange }" name="confidential">
          <FormItem class="mb-4">
            <FormControl class="flex">
              <div class="flex items-center">
                <Checkbox class="mr-2" id="register_newclient_confidential" :model-value="value"
                  @update:model-value="handleChange" :default-value="true" />
                <FormLabel for="register_newclient_confidential">Confidential</FormLabel>
              </div>
            </FormControl>
            <FormDescription>
              Confidential is to mark wether the client is public or not.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
        <Separator v-if="client_type === 'password_grant'" class="mb-3" />
        <FormField v-if="client_type === 'password_grant'" v-slot="{ value, handleChange }" name="device_flow">
          <FormItem>
            <FormControl class="flex">
              <div class="flex items-center">
                <Checkbox class="mr-2" id="register_newclient_deviceflow" :model-value="value" :default-value="false"
                  @update:model-value="handleChange" />
                <FormLabel for="register_newclient_deviceflow">Device Flow</FormLabel>
              </div>
            </FormControl>
            <FormDescription>
              Device flow is to enable device authorization.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
        <Separator v-if="client_type === 'password_grant'" class="mb-3" />
      </form>

      <DialogFooter>
        <Button type="submit" form="dialogForm">
          Register
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