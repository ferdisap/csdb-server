<script setup lang="ts" generic="TData, TValue">
import { Textarea } from '@shadcn/components/ui/textarea';
import { Button } from '@shadcn/components/ui/button';
import { useForm } from 'vee-validate';
import { fail, loading, success, warning } from 'resources/js/utils/toast';
import { FormField } from '@shadcn/components/ui/form';
import FormItem from '@shadcn/components/ui/form/FormItem.vue';
import FormLabel from '@shadcn/components/ui/form/FormLabel.vue';
import FormControl from '@shadcn/components/ui/form/FormControl.vue';
import axios, { AxiosError, AxiosResponse } from 'axios';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@shadcn/components/ui/dialog"
import { ref } from 'vue';
import Checkbox from '@shadcn/components/ui/checkbox/Checkbox.vue';
import DialogClose from '@shadcn/components/ui/dialog/DialogClose.vue';

const emit = defineEmits(['csdbCreated']) // ✅ wajib dideklarasikan

const forceDialogOpen = ref(false);
const forceDialog = {
  onSubmit: (onCheck:Function) => {
    if(onCheck) onCheck();
  },
  onCancel:() => {
    forceDialogOpen.value = false;
  },
}

const form = useForm({
  initialValues: {
    file: ''
  }
});

const submitting = (formData:any) => {
  return new Promise<any>((resolve, reject) => {
    axios.post("/csdb-object/xml/upload", formData)
    .then((response:AxiosResponse) => {
      if(response.status === 309){
        forceDialogOpen.value = true;
        forceDialog.onSubmit = () => resolve(submitting(Object.assign({force:true}, formData)));
        forceDialog.onCancel = () => {
          forceDialogOpen.value = false;
          resolve(response);
        }
        return;
      }
      forceDialogOpen.value = false;
      resolve(response.data)
    })
    .catch((e:AxiosError) => {
      console.log(e.response?.status);
      if(e.response?.status === 409){
        forceDialogOpen.value = true;
        forceDialog.onSubmit = () => resolve(submitting(Object.assign({force:true}, formData)));
        forceDialog.onCancel = () => {
          forceDialogOpen.value = false;
          reject(e);
        }
        return;
      }
      forceDialogOpen.value = false;
      reject(e);
    })
  })
}

const onSubmited = form.handleSubmit((values: any) => {
  const toastId = loading("Uploading csdb object ...");
  submitting(values)
  .then((data: any) => {
      console.log(data);
      emit('csdbCreated', data.csdb);
      success(data.message ? data.message : 'Create Csdb obejct Success.', null, toastId);
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
})
</script>
<template>
  <div class="w-full flex justify-center mt-6">
    <form id="create_csdb_form" @submit.prevent="onSubmited" class="w-[80%]">
      <FormField v-slot="{ componentField }" name="file">
        <FormItem>
          <FormLabel>XML Text File</FormLabel>
          <FormControl>
            <Textarea id="csdb_create_form" placeholder="Paste your xml file here" v-bind="componentField" class="min-h-60"/>
          </FormControl>
        </FormItem>
      </FormField>
  
      <Button type="submit" form="create_csdb_form" class="mt-2">
        Submit
      </Button>
    </form>
  </div>
  <Dialog :open="forceDialogOpen" @update:open="forceDialog.onCancel">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Are you sure force upload</DialogTitle>
        <DialogDescription>
          Force upload may overwrite the previous file and cannot be undo.
        </DialogDescription>
      </DialogHeader>

      <DialogFooter>
        <Button @click="forceDialog.onSubmit">Submit</Button>
        <Button @click="forceDialog.onCancel">Close</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>