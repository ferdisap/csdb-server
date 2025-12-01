<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef, CoreRow, PaginationState, RowData, RowModel, RowSelectionRow } from '@tanstack/vue-table'
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
import { token, columns_token, useDataTokens } from './oauth_token_column';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import Skeleton from '@shadcn/components/ui/skeleton/Skeleton.vue';
import { goto } from '../../router';

const emit = defineEmits(['selectToken']) // ✅ wajib dideklarasikan



// list oauth
const columns: ColumnDef<token>[] = columns_token((e:PointerEvent, row:RowModel<RowData>) => {
  e.preventDefault();
  emit('selectToken', (row as any).original)
});
const { data, fetchData, addData, updateData } = useDataTokens();
const loading = ref(true);

onMounted(async () => {
  await fetchData()
  loading.value = false;
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
  emit('selectToken', (row as any).original);
}

</script>

<template>
  <div v-if="!loading">
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

  <!-- <div class="mt-12 border">
    <p class="font-semibold">Selected rows:</p>
    <pre>{{ table.getSelectedRowModel().rows.map(r => r.original) }}</pre>
  </div> -->
</template>