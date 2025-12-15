import type { ColumnDef, Row, RowData, RowModel } from '@tanstack/vue-table';
import { h, Reactive, reactive, ref } from 'vue';
import { DateTime } from '../../../../utils/datetime';
import axios from 'axios';
import { goto } from '../../router';

export interface SavedToken {
  id: string,
  access_token: string,
  refresh_token: string,
  expired: DateTime,
}

export function castToToken(data: any): SavedToken {
  return {
    id: data.id,
    access_token: data.access_token,
    refresh_token: data.refresh_token,
    expired: DateTime.cast(data.expires_at, 'MMMM Do YYYY, h:mm'),
  }
}

export const columns_token = (onClickColumnId: (e: PointerEvent, row: RowModel<RowData>) => void): ColumnDef<token>[] => {
  return [
    {
      accessorKey: 'id',
      header: () => h('div', { class: 'text-left' }, 'ID'),
      cell: ({ row }: { row: Row<SavedToken> }) => {
        const value = row.getValue('id') as string;
        return h('div', { class: 'text-center font-medium' }, value);
      },
      size: 20,
    },
    {
      accessorKey: 'access_token',
      header: () => h('div', { class: 'text-center' }, 'Access Token'),
      cell: ({ row }: { row: Row<SavedToken> }) => {
        const value = row.getValue('access_token') as string;
        return h('div', { class: 'text-center font-medium' }, value);
      },
      size: 100,
    },
    {
      accessorKey: 'refresh_token',
      header: () => h('div', { class: 'text-center' }, 'Refresh Token'),
      cell: ({ row }: { row: Row<SavedToken> }) => {
        const value = row.getValue('refresh_token') as string;
        return h('div', { class: 'text-center font-medium' }, value);
      },
      size: 100,
    },
    {
      accessorKey: 'expired',
      header: () => h('div', { class: 'text-right' }, 'Expired'),
      cell: ({ row }: { row: Row<SavedToken> }) => {
        let value = row.getValue('expired');
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
};

const owner = {
  "name": "Nami",
  "email": "nami@example.com"
}
export const data_dummy: SavedToken[] = [
  {
    "id": '1',
    "access_token": "7d9bbdfffccffa0632e60c9ede522004972de228125610e0ebf70bc8a16de84c",
    "refresh_token": "7d9bbdfffccffa0632e60c9ede522004972de228125610e0ebf70bc8a16de84c",
    "expired": DateTime.cast("2025-12-15 03:02:58")
  },
  {
    "id": '2',
    "access_token": "7d9bbdfffccffa0632e60c9ede522004972de228125610e0ebf70bc8a16de84c",
    "refresh_token": "7d9bbdfffccffa0632e60c9ede522004972de228125610e0ebf70bc8a16de84c",
    "expired": DateTime.cast("2025-12-15 03:02:58")
  },
]

const data = ref<Array<SavedToken>>([]);

export function useDataTokens() {

  async function fetchData() {
    // Fetch data from your API here.
    // return data_dummy;
    const response = await axios.get("/dochub/token/list-saved");
    const tokens: SavedToken[] = (response.data.tokens as Array<{}>).map(o => castToToken(o));
    // tokens.forEach(v => data.push(v))
    data.value = tokens;
    return tokens as Array<SavedToken>;
  }

  function addData(newClient: SavedToken) {
    const newData = Object.assign([], data.value);
    newData.push(newClient);
    assignData(newData);
  }

  function updateData(newClient: SavedToken) {
    // update data SavedToken
    const id = newClient.id;
    const index = data.value.findIndex((ct: SavedToken) => ct.id === id);
    if (index >= 0) {
      data.value[index] = newClient;
      // replace data with new assigned
      assignData();
    }
  }

  function deleteData(deletedClient: SavedToken) {
    const id = deletedClient.id;
    const index = data.value.findIndex((ct: SavedToken) => ct.id === id);
    if (index >= 0) {
      data.value.splice(index, 1);
      assignData();
    }
  }

  function assignData(tokens: Array<SavedToken> | null = null) {
    // replace data with new assigned
    let newData = tokens ? tokens : Object.assign([], data.value);
    data.value = newData;
  }

  return { data, fetchData, addData, updateData, deleteData }
}
