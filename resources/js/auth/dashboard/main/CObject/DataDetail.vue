<script setup lang="ts">
import { prettifyXml } from 'resources/js/utils/cobject';
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import {
  useDataCObjects,
  type cobject,
} from "./cobject_column";
import axios from 'axios';
import { CodeEditor } from 'monaco-editor-vue3';
import Checkbox from '@shadcn/components/ui/checkbox/Checkbox.vue';
import { CircleX } from 'lucide-vue-next';
import Button from '@shadcn/components/ui/button/Button.vue';
import FormDelete from './FormDelete.vue';

const props = defineProps<{
  data: cobject | null
  fetchUrl?: string | null
}>();
const { data, deleteData } = useDataCObjects();
const route = useRoute();
const router = useRouter();
const code = ref("");
const prettied = ref(false);
const editorOptions = {
  fontSize: 14,
  minimap: { enabled: false },
  automaticLayout: true,
};
function preetyXml(evt: PointerEvent) {
  if (!prettied.value) {
    (evt.target! as HTMLElement).dataset.unprettied = code.value;
    prettied.value = true;
    code.value = prettifyXml(code.value);
  } else {
    if ((evt.target! as HTMLElement).dataset.unprettied) {
      code.value = (evt.target! as HTMLElement).dataset.unprettied!;
      (evt.target! as HTMLElement).dataset.unprettied = '';
      prettied.value = false;
    }
  }
}
function readCObjectById(filename: string) {
  let url:string;
  if(props.fetchUrl){
    url = props.fetchUrl.replace(":filename", filename);
  } else {
    url = `/csdb-object/${filename}`;
  }
  axios.get(url).then((response) => {
    code.value = response.data.csdb.file;
  });
}
onMounted(async () => {
  if(props.data){
    readCObjectById(props.data.filename);
  } else {
    const cobject_filename = route.params.filename as string;
    if (cobject_filename) readCObjectById(cobject_filename);
  }
});
watch(
  () => props.data as cobject,
  (newData:cobject) => readCObjectById(newData.filename)
)
function onDeletedCObject(data:cobject){
  deleteData(data);
  router.back();
}
</script>
<template>
  <div v-if="route.params.filename" class="w-full container p-4 mx-auto mt-6" style="height: 400px">
    <div class="flex items-center mb-2">
      <div>
        <Checkbox id="pretty_xml" class="mr-2" @click="preetyXml" />
        <label for="pretty_xml">Pretty Xml</label>
      </div>
      <div>
        <slot name="form_delete">
          <FormDelete :filename="route.params.filename as string" @deleted-c-object="onDeletedCObject"/>
        </slot>
      </div>
    </div>
    <CodeEditor class="border p-2 rounded-lg bg bg-gray-900" v-model:value="code" language="xml" theme="vs-dark"
      :options="editorOptions" />
  </div>
</template>
