<script setup lang="ts">
import Button from '@shadcn/components/ui/button/Button.vue';
import Checkbox from '@shadcn/components/ui/checkbox/Checkbox.vue';
import Dialog from '@shadcn/components/ui/dialog/Dialog.vue';
import DialogClose from '@shadcn/components/ui/dialog/DialogClose.vue';
import DialogContent from '@shadcn/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@shadcn/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@shadcn/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@shadcn/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@shadcn/components/ui/dialog/DialogTitle.vue';
import DialogTrigger from '@shadcn/components/ui/dialog/DialogTrigger.vue';
import { FormField } from '@shadcn/components/ui/form';
import FormControl from '@shadcn/components/ui/form/FormControl.vue';
import FormDescription from '@shadcn/components/ui/form/FormDescription.vue';
import FormItem from '@shadcn/components/ui/form/FormItem.vue';
import FormLabel from '@shadcn/components/ui/form/FormLabel.vue';
import FormMessage from '@shadcn/components/ui/form/FormMessage.vue';
import Separator from '@shadcn/components/ui/separator/Separator.vue';
import Tooltip from '@shadcn/components/ui/tooltip/Tooltip.vue';
import TooltipContent from '@shadcn/components/ui/tooltip/TooltipContent.vue';
import TooltipProvider from '@shadcn/components/ui/tooltip/TooltipProvider.vue';
import TooltipTrigger from '@shadcn/components/ui/tooltip/TooltipTrigger.vue';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { CircleX } from 'lucide-vue-next';
import { fail, loading, success, warning } from 'resources/js/utils/toast';
import { useForm } from 'vee-validate';
import { ref } from 'vue';
import { castToCObject } from '../cobject_column';

const props = defineProps<{
  filename: string
}>();
const emit = defineEmits(["deletedTrash"]);
const open = ref(false);
const form = useForm({
  initialValues: {
    deleted: false,
    permanent: false
  }
});
const submitting = (formData: any) => {
  const id = props.filename
  return new Promise<any>((resolve, reject) => {
    axios.post(`/csdb-object/trash/${id}/delete`, formData)
      .then((response:AxiosResponse) => {
        resolve(response.data);
      })
      .catch((e: AxiosError) => {
        reject(e);
      })
  })
}
const onSubmited = form.handleSubmit((values: any) => {
  const toastId = loading("Deleting ...");
  submitting(values)
    .then((data: any) => {
      open.value = false;
      const cobject = castToCObject(data.csdb.trashes[0]);
      emit('deletedTrash', cobject);
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
</script>
<template>
  <Dialog :open="open" @update:open="open = $event">
    <DialogTrigger as-child>
      <TooltipProvider>
        <Tooltip>
          <TooltipTrigger>
            <Button class="ml-2" variant="destructive" @click="open = !open"><CircleX/></Button>
          </TooltipTrigger>
          <TooltipContent class="border border-light dark:border-dark bg-light dark:bg-dark">Delete Trash Csdb Object</TooltipContent>
        </Tooltip>
      </TooltipProvider>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Delete Trash CSDB Object</DialogTitle>
        <DialogDescription>
          Do this action carefully.
        </DialogDescription>
      </DialogHeader>

      <form id="dialogForm" @submit.prevent="onSubmited">
        <FormField v-slot="{ value, handleChange }" name="deleted">
          <FormItem class="mb-4">
            <FormControl class="flex">
              <div class="flex items-center">
                <Checkbox class="mr-2" id="deleted_cobject_form" :model-value="value"
                  @update:model-value="handleChange" />
                <FormLabel for="deleted_cobject_form">Delete</FormLabel>
              </div>
            </FormControl>
            <FormDescription>
              The trash will be permanently deleted.
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