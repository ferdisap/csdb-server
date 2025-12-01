import type { ColumnDef, Row, RowData, RowModel } from '@tanstack/vue-table';
import { h, Reactive, reactive, ref } from 'vue';
import { DateTime } from '../../../../utils/datetime';
import axios from 'axios';
import { goto } from '../../router';

interface owner {
  name: string,
  email: string
}

export interface token {
  id: string,
  client_id: string,
  user: owner,
  name: string,
  scopes: Array<string>,
  revoked: boolean | number,
  expired: DateTime,
  updated: DateTime,
}

export function castToToken(data: any): token {
  return {
    id: data.id,
    client_id: data.client_id,
    user: data.user,
    name: data.name,
    scopes: data.scopes,
    revoked: data.revoked,
    expired: DateTime.cast(data.expires_at, 'MMMM Do YYYY, h:mm'),
    updated: DateTime.cast(data.updated_at),
  }
}

export const columns_token = (onClickColumnId:(e:PointerEvent, row:RowModel<RowData>) => void) :ColumnDef<token>[] => {
  return [
  {
    accessorKey: 'id',
    header: () => h('div', { class: 'text-left' }, 'ID'),
    cell: ({ row }: { row: Row<token> }) => {
      const value = row.getValue('id') as string;
      return h('a', { 
        class: 'text-left font-medium underline text-blue-600',
        href: value,
        onClick: (e:PointerEvent) => {
          onClickColumnId(e, row as unknown as RowModel<RowData>);
        }
      }, value)
    },
    size: 100,
  },
  {
    accessorKey: 'user',
    header: () => h('div', { class: 'text-left' }, 'User'),
    cell: ({ row }: { row: Row<token> }) => {
      const value = row.getValue('user') as owner;
      return h('div', { class: 'text-left font-medium' }, value.name)
    },
    size: 100,
  },
  {
    accessorKey: 'client_id',
    header: () => h('div', { class: 'text-left' }, 'Client ID'),
    cell: ({ row }: { row: Row<token> }) => {
      const value = row.getValue('client_id') as string;
      return h('a', {
        class: 'text-left font-medium underline text-blue-600', 
        href: '../oauth-client/' + value,
        onClick: (e:PointerEvent) => {
          e.preventDefault();
          goto('OAuth Client', {
            id: value
          })
        }
      }, value);
    },
    size: 100,
  },
  {
    accessorKey: 'scopes',
    header: () => h('div', { class: 'text-right' }, 'Scopes'),
    cell: ({ row }: { row: Row<token> }) => {
      const value = row.getValue('scopes') as Array<string>;
      return h('div', { class: 'text-right font-medium' }, value.join(", "))
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
  {
    accessorKey: 'updated',
    header: () => h('div', { class: 'text-right' }, 'Last Update'),
    cell: ({ row }: { row: Row<token> }) => {
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
};

const owner = {
  "name": "Nami",
  "email": "nami@example.com"
}
export const data_dummy: token[] = [
  {
    "id": "01995fd3-bcd5-7222-905e-6333ce85de42",
    "client_id": "0199bde7-7aa7-72e1-8ab4-5876e7094a7b",
    "user": owner,
    "name": "Example App",
    "scopes": ["user:read"],
    "revoked": 0,
    "expired": DateTime.cast("2025-09-19 02:35:36"),
    "updated": DateTime.cast("2025-09-19 02:35:36")
  },
  {
    "id": "01995fd3-bcd5-7222-905e-6333ce85de42",
    "client_id": "0199bde7-7aa7-72e1-8ab4-5876e7094a7b",
    "user": owner,
    "name": "Fufufafa App",
    "scopes": ["user:read"],
    "revoked": 0,
    "expired": DateTime.cast("2025-09-19 02:35:36"),
    "updated": DateTime.cast("2025-09-19 02:35:36")
  },
]

const data = ref<Array<token>>([]);

export function useDataTokens() {

  async function fetchData() {
    // Fetch data from your API here.
    // return data_dummy;
    const response = await axios.get("/oauth-token");
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
