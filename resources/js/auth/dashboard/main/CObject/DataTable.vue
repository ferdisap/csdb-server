<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef, PaginationState, RowData, RowModel, RowSelectionRow } from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { Button } from '@shadcn/components/ui/button';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@shadcn/components/ui/table'
import { cobject, columns_cobject, useDataCObjects } from './cobject_column';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { onMounted, reactive, ref, computed } from 'vue';

import Skeleton from '@shadcn/components/ui/skeleton/Skeleton.vue';
import { next, prev, set_per_page } from 'resources/js/utils/pagination';

const emit = defineEmits(['selectCobject']) // ✅ wajib dideklarasikan

const props = defineProps<{
  fetchUrl?: string | null
}>();

// list oauth
const columns: ColumnDef<cobject>[] = columns_cobject();
const { data, pagination, fetchData, addData, updateData } = useDataCObjects();
const progressingFetchData = ref(true);
onMounted(async () => {
  fetchData(props.fetchUrl ?? null)
  .finally(() =>  progressingFetchData.value = false);
})


let table = useVueTable({
  get data() { return data ? data : [] },
  columns: columns,
  getCoreRowModel: getCoreRowModel(),
  enableRowSelection: true, // <-- penting untuk aktifkan select
})

function onClickRow(row:RowSelectionRow){
  table.resetRowSelection(); 
  row.toggleSelected();
  emit('selectCobject', (row as any).original);
}

</script>

<template>
  <div v-if="!progressingFetchData && data.length">
    <div class="border rounded-md">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id"
              :style="{ width: header.getSize() ? (header.getSize() + 'px') : '100%' }">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <TableRow v-for="row in table.getRowModel().rows" :key="row.id" @click="onClickRow(row)"
              :data-state="row.getIsSelected() ? 'selected' : undefined">
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" /> 
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow>
              <TableCell :colspan="columns.length" class="h-24 text-center">
                No results.
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
    <div class="mt-1">
      <div class="float-left">
        show qty.:
        <select class="ml-2 border rounded px-2 py-1" :value="pagination.data?.per_page" id="fufufafa"
        @change="set_per_page(Number(($event.target! as HTMLSelectElement).value), pagination.data!, fetchData)">
          <option :value="3">3</option>
          <option :value="5">5</option>
          <option :value="10">10</option>
        </select>
      </div>
      <div class="float-right">
        <Button variant="outline" :disabled="!pagination.data?.prev_page_url" @click="prev(pagination.data!, fetchData)">Prev</Button>
        <span class="mx-3">Page {{ pagination.data?.current_page }} of {{ Math.ceil(pagination.data?.total! / pagination.data?.per_page!) }}</span>
        <Button variant="outline" :disabled="!pagination.data?.next_page_url" @click="next(pagination.data!, fetchData)">Next</Button>
      </div>
    </div>
  </div>
  <Skeleton v-if="progressingFetchData" class="w-full container p-10 mx-auto min-h-[200px]" />
  <!-- <div v-else class="text-center">
    No csdb object available.
  </div> -->
</template>