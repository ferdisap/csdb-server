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
import { castToToken } from './dochub_token_column';
import { fail, loading, success, warning } from 'resources/js/utils/toast';
import RadioGroup from '@shadcn/components/ui/radio-group/RadioGroup.vue';
import RadioGroupItem from '@shadcn/components/ui/radio-group/RadioGroupItem.vue';
import Label from '@shadcn/components/ui/label/Label.vue';

// const formSchema = toTypedSchema(z.object({
//   username: z.string().min(2).max(50),
// }))

const emit = defineEmits(['newTokenCreated']) // ✅ wajib dideklarasikan
const open = ref(false);

const form = useForm({});

const onSubmited = form.handleSubmit(async (values: any) => {
  const toastId = loading("Creating ...");
  axios.post("/dochub/token/create", values)
    .then((response: AxiosResponse) => {
      const data = response.data
      open.value = false;
      const token = castToToken(data.token);
      emit('newTokenCreated', token);
      success(data.message ? data.message : 'Creating token success.', null, toastId);
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
        <DialogTitle>Register New Token</DialogTitle>
        <DialogDescription>
          Fill the form then click register when you're done.
        </DialogDescription>
      </DialogHeader>
      <form id="dialogForm" @submit.prevent="onSubmited">
        <Separator class="mb-3" />
        <FormField v-slot="{ componentField }" name="provider">
          <FormItem class="mb-4">
            <FormLabel>Provider</FormLabel>
            <FormControl>
              <Input type="text" placeholder="http://fufufafa.com" v-bind="componentField" />
            </FormControl>
            <FormDescription>
              This provider indicates where will you use your token.
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>
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