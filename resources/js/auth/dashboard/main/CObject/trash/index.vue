<script setup lang="ts">
import type { ColumnDef } from "@tanstack/vue-table";
import { h, onMounted, ref } from "vue";
import type { Ref } from "vue";
import DataTable from "../DataTable.vue";
// import DataDetail from './DataDetail.vue';
import { Skeleton } from "@shadcn/components/ui/skeleton";

import { useRoute, useRouter } from "vue-router";
import {
    castToCObject,
    type cobject,
    columns_cobject,
} from "../cobject_column";
import axios from "axios";
import { CodeEditor } from "monaco-editor-vue3";
import { prettifyXml } from "resources/js/utils/cobject";
import Checkbox from "@shadcn/components/ui/checkbox/Checkbox.vue";
import DataDetail from "../DataDetail.vue";
import FormDelete from "./FormDelete.vue";

const route = useRoute();
const router = useRouter();
const selectedCobject = ref<cobject | null>(null);

function setSelectedCObjectFilename(data: cobject) {
    selectedCobject.value = data;
    router.push({
        name: "Trash CSDB Object Index",
        params: {
            filename: data.filename,
        },
    });
}

function onDeletedTrash(){
    
}
</script>

<template>
    <div class="container p-4 mx-auto">
        <h1 class="w-full text-center mb-3 font-bold text-xl">
            Trash of CSDB Object Index
        </h1>
        <DataTable @select-cobject="(data: cobject) => setSelectedCObjectFilename(data)" fetch-url="/csdb-object/trash"/>
    </div>
    <DataDetail :data="selectedCobject" fetch-url="/csdb-object/trash/:filename">
        <template #form_delete>
            <FormDelete :filename="route.params.filename as string" @deleted-trash="onDeletedTrash"/>
        </template>
    </DataDetail>
</template>
