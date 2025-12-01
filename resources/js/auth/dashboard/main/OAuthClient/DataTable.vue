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
import { castToClient, client, columns_client } from './oauth_client_column';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import FormNewClient from './FormNewClient.vue';

import { useDataClients } from './oauth_client_column';
import Skeleton from '@shadcn/components/ui/skeleton/Skeleton.vue';

const emit = defineEmits(['selectClient']) // ✅ wajib dideklarasikan

// const props = defineProps<{
//   columns: ColumnDef<TData, TValue>[]
//   data: TData[]
//   pagination: PaginationState
// }>();

// list oauth
const columns: ColumnDef<client>[] = columns_client((e:PointerEvent, row:RowModel<RowData>) => {
  e.preventDefault();
  emit('selectClient', (row as any).original)
});
const { data, fetchData, addData, updateData } = useDataClients();
const loading = ref(true);
const contentNoResult = ref('No results.');
onMounted(async () => {
  fetchData()
  .catch((e) => {
    contentNoResult.value = (e.response.data.message || e || 'No results.');
  })
  .finally(() => {
    loading.value = false;
  })
})


let table = useVueTable({
  get data() { return data },
  columns: columns,
  initialState: {
    pagination: {
      pageIndex: 0,
      pageSize: 5
    }
  },
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(), // WAJIB
  enableRowSelection: true, // <-- penting untuk aktifkan select
})

function onClickRow(row:RowSelectionRow){
  table.resetRowSelection(); 
  row.toggleSelected();
  emit('selectClient', (row as any).original);
}

</script>

<template>
  <div v-if="!loading">
    <div class="mb-1 ">
      <FormNewClient @new-client-registered="addData" />
    </div>
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
          <template v-if="table.getPaginationRowModel().rows?.length">
            <TableRow v-for="row in table.getPaginationRowModel().rows" :key="row.id" @click="onClickRow(row)"
              :data-state="row.getIsSelected() ? 'selected' : undefined">
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                <!-- <FlexRender v-if="cell.column.id === 'id'"
                  @click="$emit('selectClient', row.original)" :render="cell.column.columnDef.cell"
                  :props="cell.getContext()" />
                <FlexRender v-else :render="cell.column.columnDef.cell" :props="cell.getContext()" /> -->
                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" /> 
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow>
              <TableCell :colspan="columns.length" class="h-24 text-center">
                {{ contentNoResult }}
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
    <div class="mt-1">
      <div class="float-left">
        show qty.:
        <select class="ml-2 border rounded px-2 py-1" v-model.number="table.getState().pagination.pageSize"
          @change="table.setPageSize(table.getState().pagination.pageSize)">
          <option :value="3">3</option>
          <option :value="5">5</option>
          <option :value="10">10</option>
        </select>
      </div>
      <div class="float-right">
        <Button variant="outline" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
          Prev
        </Button>
        <span class="mx-3">
          Page {{ table.getState().pagination.pageIndex + 1 }}
          of {{ table.getPageCount() }}
        </span>
        <Button variant="outline" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
          Next
        </Button>
      </div>
    </div>
  </div>
  <Skeleton v-else class="w-full container p-10 mx-auto min-h-[200px]" />
</template>