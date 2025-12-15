import type { ColumnDef, Row, RowData, RowModel } from '@tanstack/vue-table';
import { h, Reactive, reactive, ref } from 'vue';
import { DateTime } from '../../../../utils/datetime';
import axios from 'axios';
import { goto } from '../../router';
import { SavedToken } from './dochub_savedtoken_column';

export interface token {
  id: string,
  provider: string,
  access_token: string,
  revoked: boolean | number,
  expired: DateTime,
  savedToken: null | SavedToken
}

export function castToToken(data: any): token {
  return {
    id: data.id,
    provider: data.provider,
    access_token: data.access_token,
    revoked: data.revoked,
    expired: DateTime.cast(data.expires_at, 'MMMM Do YYYY, h:mm'),
    savedToken: data.saved_token || null
  }
}

export const columns_token = (onClickColumnId: (e: PointerEvent, row: RowModel<RowData>) => void): ColumnDef<token>[] => {
  return [
    {
      accessorKey: 'id',
      header: () => h('div', { class: 'text-left' }, 'ID'),
      cell: ({ row }: { row: Row<token> }) => {
        const value = row.getValue('id') as string;
        return h('a', {
          class: 'text-left font-medium underline text-blue-600',
          href: value,
          onClick: (e: PointerEvent) => {
            onClickColumnId(e, row as unknown as RowModel<RowData>);
          }
        }, value)
      },
      size: 20,
    },
    {
      accessorKey: 'provider',
      header: () => h('div', { class: 'text-center' }, 'Provider'),
      cell: ({ row }: { row: Row<token> }) => {
        const value = row.getValue('provider') as string;
        return h('div', { class: 'text-center font-medium' }, value)
      },
      size: 50,
    },
    {
      accessorKey: 'access_token',
      header: () => h('div', { class: 'text-center' }, 'Token (hashed)'),
      cell: ({ row }: { row: Row<token> }) => {
        const value = row.getValue('access_token') as string;
        return h('div', { class: 'text-center font-medium' }, value);
      },
      size: 100,
    },
    {
      accessorKey: 'revoked',
      header: () => h('div', { class: 'text-center' }, 'Revoked'),
      cell: ({ row }: { row: Row<token> }) => {
        const value = row.getValue('revoked') as boolean;
        return h('div', { class: 'text-center font-medium' }, value ? 'true' : 'false')
      },
      size: 50,
    },
    {
      accessorKey: 'expired',
      header: () => h('div', { class: 'text-right' }, 'Expired'),
      cell: ({ row }: { row: Row<token> }) => {
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
export const data_dummy: token[] = [
  {
    "id": '1',
    "provider": "http:\/\/localhost:1001",
    "access_token": "7d9bbdfffccffa0632e60c9ede522004972de228125610e0ebf70bc8a16de84c",
    "revoked": false,
    "expired": DateTime.cast("2025-12-15 03:02:58")
  },
  {
    "id": '2',
    "provider": "http:\/\/localhost:1001",
    "access_token": "7d9bbdfffccffa0632e60c9ede522004972de228125610e0ebf70bc8a16de84c",
    "revoked": false,
    "expired": DateTime.cast("2025-12-15 03:02:58")
  },
]

const data = ref<Array<token>>([]);

export function useDataTokens() {

  async function fetchData() {
    // Fetch data from your API here.
    // return data_dummy;
    const response = await axios.get("/dochub/token/list");
    const tokens: token[] = (response.data.tokens as Array<{}>).map(o => castToToken(o));
    // tokens.forEach(v => data.push(v))
    data.value = tokens;
    return tokens as Array<token>;
  }

  function addData(newClient: token) {
    const newData = Object.assign([], data.value);
    newData.push(newClient);
    assignData(newData);
  }

  function updateData(newClient: token) {
    // update data token
    const id = newClient.id;
    const index = data.value.findIndex((ct: token) => ct.id === id);
    if (index >= 0) {
      data.value[index] = newClient;
      // replace data with new assigned
      assignData();
    }
  }

  function deleteData(deletedClient: token) {
    const id = deletedClient.id;
    const index = data.value.findIndex((ct: token) => ct.id === id);
    if (index >= 0) {
      data.value.splice(index, 1);
      assignData();
    }
  }

  function assignData(tokens: Array<token> | null = null) {
    // replace data with new assigned
    let newData = tokens ? tokens : Object.assign([], data.value);
    data.value = newData;
  }

  return { data, fetchData, addData, updateData, deleteData }
}
