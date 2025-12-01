import type { ColumnDef, Row, RowData, RowModel } from '@tanstack/vue-table';
import { h, Reactive, reactive, ref } from 'vue';
import { DateTime } from '../../../../utils/datetime';
import axios from 'axios';
import { latest_url, pagination_interface } from 'resources/js/utils/pagination';

interface owner {
  name: string,
  email: string
}

export interface cobject {
  filename: string,
  path: string,
  remarks: Array<string>,
  updated: DateTime,
  owner:owner
}

export function castToCObject(data: any): cobject {
  return {
    filename: data.filename,
    path: data.path,
    remarks: data.remarks,
    updated: DateTime.cast(data.updated_at),
    owner: data.owner
  }
}

export function columns_cobject(): ColumnDef<cobject>[] {
  return [
    {
      accessorKey: 'filename',
      header: () => h('div', { class: 'text-left' }, 'Filename'),
      cell: ({ row }: { row: Row<cobject> }) => {
        const value = row.getValue('filename') as string;
        return h('div', { class: 'text-left font-medium' }, value)
      },
      size: 100,
    },
    {
      accessorKey: 'path',
      header: () => h('div', { class: 'text-right' }, 'Path'),
      cell: ({ row }: { row: Row<cobject> }) => {
        const value = row.getValue('path') as string;
        return h('div', { class: 'text-right font-medium' }, value)
      },
      size: 100,
    },
    {
      accessorKey: 'owner',
      header: () => h('div', { class: 'text-right' }, 'Owner'),
      cell: ({ row }: { row: Row<cobject> }) => {
        const value = row.getValue('owner') as owner;
        return h('div', { class: 'text-right font-medium' }, value ? value.email : '-');
      },
      size: 100,
      maxSize: 200,
      minSize: 50
    },
    {
      accessorKey: 'updated',
      header: () => h('div', { class: 'text-right' }, 'Last Update'),
      cell: ({ row }: { row: Row<cobject> }) => {
        let value = row.getValue('updated');
        if (value instanceof DateTime) {
          value = value.toString();
        }
        else if (typeof value === 'string') {
          value = (new DateTime(value)).toString();
        }
        else if (value instanceof Object) {
          value = (DateTime.instance(value as any)).toString();
        }
        return h('div', { class: 'text-right font-medium' }, value as string)
      },
      size: 100,
    },
  ]
}

// const owner = {
//   "name": "Nami",
//   "email": "nami@example.com"
// }
// export const data_dummy: cobject[] = [
//   {
//     "filename": "DMC-xxx",
//     "path": "/fufu/fafa",
//     "remarks" : ["fufu", "fafa"],
//     "updated": DateTime.cast("2025-09-19 02:35:36")
//   },
//   {
//     "filename": "DMC-xxx",
//     "path": "/fufu/fafa",
//     "remarks" : ["fufu", "fafa"],
//     "updated": DateTime.cast("2025-09-19 02:35:36")
//   },
//   {
//     "filename": "DMC-xxx",
//     "path": "/fufu/fafa",
//     "remarks" : ["fufu", "fafa"],
//     "updated": DateTime.cast("2025-09-19 02:35:36")
//   },
// ]

const data = ref<Array<cobject>>([]);
const pagination : {data: pagination_interface | null} = reactive({data:null});

export function useDataCObjects() {

  async function fetchData(url:string|null = null) {
    // Fetch data from your API here.
    // const url = (pagination.data && pagination.data.next_url) ? pagination.data.next_url : "/csdb-object";
    const response = await axios.get(latest_url(pagination.data, url ?? "/csdb-object"));

    // add data value
    let cobjects: cobject[] = [];
    if((response.data.csdb.objects as Array<{}>)){
      cobjects = (response.data.csdb.objects as Array<{}>).map(o => castToCObject(o));
      data.value = cobjects;
    }
    
    // add pagination value
    if((response.data.csdb.pagination as pagination_interface)){
      pagination.data = response.data.csdb.pagination;
    }
    // return cobjects as Array<cobject>;
  }

  function addData(newCObject: cobject) {
    const newData = Object.assign([], data.value);
    newData.push(newCObject);
    assignData(newData);
  }

  function updateData(newCObject: cobject) {
    // update data cobject
    const id = newCObject.filename;
    const index = data.value.findIndex((ct: cobject) => ct.filename === id);
    if (index >= 0) {
      data.value[index] = newCObject;
      // replace data with new assigned
      assignData();
    }
  }

  function deleteData(deletedCObject: cobject) {
    const id = deletedCObject.filename;
    const index = data.value.findIndex((ct: cobject) => ct.filename === id);
    if (index >= 0) {
      data.value.splice(index, 1);
      assignData();
    }
  }

  function assignData(cobjects: Array<cobject> | null = null) {
    // replace data with new assigned
    let newData = cobjects ? cobjects : Object.assign([], data.value);
    data.value = newData;
  }

  return { data, pagination, fetchData, addData, updateData, deleteData }
}
