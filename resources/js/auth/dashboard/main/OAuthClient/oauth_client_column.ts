import type { ColumnDef, Row, RowData, RowModel } from '@tanstack/vue-table';
import { h, Reactive, reactive, ref } from 'vue';
import { DateTime } from '../../../../utils/datetime';
import axios from 'axios';

interface owner {
  name: string,
  email: string
}

export interface client {
  id: string,
  name: string,
  owner: owner | null,
  is_public: boolean,
  provider: string | null,
  redirect_uris: Array<string>,
  grant_type: Array<string>,
  revoked: boolean | number,
  updated: DateTime,
}

export function castToClient(data: any): client {
  return {
    id: data.id,
    name: data.name,
    owner: data.owner,
    is_public: data.is_public,
    provider: data.provider,
    redirect_uris: data.redirect_uris,
    grant_type: data.grant_type,
    revoked: data.revoked,
    updated: DateTime.cast(data.updated_at),
  }
}

export function columns_client(onClickColumnId:(e:PointerEvent, row:RowModel<RowData>) => void): ColumnDef<client>[] {
  return [
    {
      accessorKey: 'id',
      header: () => h('div', { class: 'text-left' }, 'ID'),
      cell: ({ row }: { row: Row<client> }) => {
        const value = row.getValue('id') as string;
        return h('a', {
          class: 'text-left font-medium underline text-blue-600',
          href: value,
          onClick: (e: PointerEvent) => {
            onClickColumnId(e, row as unknown as RowModel<RowData>);
          }
        }, value)
      },
      size: 100,
    },
    {
      accessorKey: 'name',
      header: () => h('div', { class: 'text-right' }, 'App Name'),
      cell: ({ row }: { row: Row<client> }) => {
        const value = row.getValue('name') as string;
        return h('div', { class: 'text-right font-medium' }, value)
      },
      size: 100,
    },
    {
      accessorKey: 'owner',
      header: () => h('div', { class: 'text-right' }, 'Owner'),
      cell: ({ row }: { row: Row<client> }) => {
        const value = row.getValue('owner') as owner;
        return h('div', { class: 'text-right font-medium' }, value ? value.email : '-');
      },
      size: 100,
      maxSize: 200,
      minSize: 50
    },
    {
      accessorKey: 'is_public',
      header: () => h('div', { class: 'text-center' }, 'Public'),
      cell: ({ row }: { row: Row<client> }) => {
        const value = row.getValue('is_public') as boolean;
        return h('div', { class: 'text-center font-medium' }, value ? 'true' : 'false')
      },
      size: 50,
    },
    {
      accessorKey: 'revoked',
      header: () => h('div', { class: 'text-center' }, 'Revoked'),
      cell: ({ row }: { row: Row<client> }) => {
        const value = row.getValue('revoked') as boolean;
        return h('div', { class: 'text-center font-medium' }, value ? 'true' : 'false')
      },
      size: 50,
    },
    {
      accessorKey: 'updated',
      header: () => h('div', { class: 'text-right' }, 'Last Update'),
      cell: ({ row }: { row: Row<client> }) => {
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

const owner = {
  "name": "Nami",
  "email": "nami@example.com"
}
export const data_dummy: client[] = [
  {
    "id": "01995fd3-bcd5-7222-905e-6333ce85de42",
    "name": "Example App",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-19 02:35:36")
  },
  {
    "id": "01997ead-fc1f-72e6-ae7f-9bfbd23a6bec",
    "name": "Example App Oauth Server",
    "owner": owner,
    "is_public": false,
    "provider": "users",
    "redirect_uris": [],
    "grant_type": ["password", "refresh_token"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-25 02:22:35")
  },
  {
    "id": "019983fa-2d2c-701d-854f-65571fb3683c",
    "name": "Example App Oauth Server",
    "owner": owner,
    "is_public": false,
    "provider": "users",
    "redirect_uris": [],
    "grant_type": ["personal_access"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:03:54")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
  {
    "id": "01998420-52c3-73ec-81e6-76cff7b3f962",
    "name": "Client SSO",
    "owner": owner,
    "is_public": false,
    "provider": null,
    "redirect_uris": ["http:\/\/localhost:3001\/oauth\/callback"],
    "grant_type": ["authorization_code", "refresh_token", "urn:ietf:params:oauth:grant-type:device_code"],
    "revoked": 0,
    "updated": DateTime.cast("2025-09-26 03:45:34")
  },
]

const data = ref<Array<client>>([]);

export function useDataClients() {
  // const data = reactive<client[]>([]);
  // const data = reactive<{value:Reactive<client[]>}>({value:[]});

  async function fetchData() {
    // Fetch data from your API here.
    // return data_dummy;
    const response = await axios.get("/oauth-client");
    const clients: client[] = (response.data.clients as Array<{}>).map(o => castToClient(o));
    // clients.forEach(v => data.push(v))
    data.value = clients;
    return clients as Array<client>;
  }

  function addData(newClient: client) {
    const newData = Object.assign([], data.value);
    newData.push(newClient);
    assignData(newData);
  }

  function updateData(newClient: client) {
    // update data client
    const id = newClient.id;
    const index = data.value.findIndex((ct: client) => ct.id === id);
    if (index >= 0) {
      data.value[index] = newClient;
      // replace data with new assigned
      assignData();
    }
  }

  function deleteData(deletedClient: client) {
    const id = deletedClient.id;
    const index = data.value.findIndex((ct: client) => ct.id === id);
    if (index >= 0) {
      data.value.splice(index, 1);
      assignData();
    }
  }

  function assignData(clients: Array<client> | null = null) {
    // replace data with new assigned
    let newData = clients ? clients : Object.assign([], data.value);
    data.value = newData;
  }

  return { data, fetchData, addData, updateData, deleteData }
}
