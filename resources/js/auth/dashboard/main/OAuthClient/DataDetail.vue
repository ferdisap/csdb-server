<script setup lang="ts">
import { ref, watch } from 'vue';
import FormRename from './FormRename.vue';
import FormRevoke from './FormRevoke.vue';
import { client } from './oauth_client_column';
import { Button } from '@shadcn/components/ui/button';
import { useDataClients } from './oauth_client_column';
import FormDelete from './FormDelete.vue';

const props = defineProps<{
  // class?: HTMLAttributes["class"]
  // data: client
  // modelValue: client
  client: client
}>();

// const emit = defineEmits(['update:modelValue'])
const emit = defineEmits(['update:client'])

const { updateData, deleteData } = useDataClients();
// const dataDetail = ref<client|null>(props.data);

function updateClient(updatedClient:client){
  // await fetchData()
  // dataDetail.value = updatedClient;
  const newData = updatedClient;
  // emit('update:modelValue', newData)
  emit('update:client', newData)
  updateData(updatedClient);
}


function deleteClient(deletedClient:client){
  deleteData(deletedClient);
}


</script>


<style scoped>
  td:nth-child(2) {
    width: 20px;
    padding-right: 5px;
    text-align: center;
  }
</style>

<template>
  <table v-if="props.client">
    <tr>
      <td>ID</td>
      <td>:</td>
      <td>{{ props.client.id }}</td>
    </tr>
    <tr>
      <td>Name</td>
      <td>:</td>
      <td>{{ props.client.name }}</td>
    </tr>
    <tr>
      <td>Owner Name</td>
      <td>:</td>
      <td>{{ props.client.owner ? props.client.owner.name : '-' }}</td>
    </tr>
    <tr>
      <td>Owner Email</td>
      <td>:</td>
      <td>{{ props.client.owner ? props.client.owner.email : '-' }}</td>
    </tr>
    <tr>
      <td>Provider</td>
      <td>:</td>
      <td>{{ props.client.provider }}</td>
    </tr>
    <tr>
      <td>Redirect URI</td>
      <td>:</td>
      <!-- <td>{{ props.client.redirect_uris }}</td> -->
      <td>
        <ul>
          <li v-for="uri in props.client.redirect_uris">
            {{ uri }}
          </li>
        </ul>
      </td>
    </tr>
    <tr>
      <td>Grant Type</td>
      <td>:</td>
      <td>
        <ul>
          <li v-for="type in props.client.grant_type">
            {{ type }}
          </li>
        </ul>
      </td>
    </tr>
    <tr>
      <td>Public</td>
      <td>:</td>
      <td>{{ props.client.is_public ? 'true' : 'false' }}</td>
    </tr>
    <tr>
      <td>Revoked</td>
      <td>:</td>
      <td>{{ props.client.revoked ? 'true' : 'false' }}</td>
    </tr>
    <tr>
      <td>Last Update</td>
      <td>:</td>
      <td>{{ props.client.updated.toString() }}</td>
    </tr>
  </table>

  <div class="container mt-2">
    <!-- <Button class="ml-2" variant="destructive">Delete</Button> -->
    <FormDelete :data="{ client: props.client }" :onDeletedClient="deleteClient"/>
    <FormRevoke :data="{ client: props.client }" :onClientUpdated="updateClient"/>
    <FormRename :data="{ client: props.client }" :onClientUpdated="updateClient"/>
  </div>
</template>