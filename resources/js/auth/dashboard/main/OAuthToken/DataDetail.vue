<script setup lang="ts">
import { ref, watch } from 'vue';
import FormRename from './FormRename.vue';
import FormRevoke from './FormRevoke.vue';
import { token } from './oauth_token_column';
import { Button } from '@shadcn/components/ui/button';
import { useDataTokens } from './oauth_token_column';
import FormDelete from './FormDelete.vue';

const props = defineProps<{
  // class?: HTMLAttributes["class"]
  // data: token
  // modelValue: token
  token: token
}>();

// const emit = defineEmits(['update:modelValue'])
const emit = defineEmits(['update:token'])

const { updateData, deleteData } = useDataTokens();
// const dataDetail = ref<token|null>(props.data);

function updateToken(updatedToken:token){
  // await fetchData()
  // dataDetail.value = updatedToken;
  const newData = updatedToken;
  // emit('update:modelValue', newData)
  emit('update:token', newData)
  updateData(updatedToken);
}


function deleteToken(deletedToken:token){
  deleteData(deletedToken);
}


</script>


<style scoped>
  td:nth-child(1) {
    text-align: left;
    white-space: nowrap;
    padding-right: 10px;
  }
  td:nth-child(2) {
    width: 20px;
    padding-right: 5px;
    text-align: center;
  }
</style>

<template>
  <table v-if="props.token">
    <tr>
      <td>ID</td>
      <td>:</td>
      <td>{{ props.token.id }}</td>
    </tr>
    <tr>
      <td>Client ID</td>
      <td>:</td>
      <td>{{ props.token.client_id }}</td>
    </tr>
    <tr>
      <td>User</td>
      <td>:</td>
      <td>{{ props.token.user.name }}</td>
    </tr>
    <tr>
      <td>Name</td>
      <td>:</td>
      <td>{{ props.token.name }}</td>
    </tr>
    <tr>
      <td>Scopes</td>
      <td>:</td>
      <td>{{ props.token.scopes.join(", ") }}</td>
    </tr>
    <tr>
      <td>Revoked</td>
      <td>:</td>
      <td>{{ props.token.revoked ? 'true' : 'false' }}</td>
    </tr>
    <tr>
      <td>Expired</td>
      <td>:</td>
      <td>{{ props.token.expired.toString() }}</td>
    </tr>
    <tr>
      <td>Last Update</td>
      <td>:</td>
      <td>{{ props.token.updated.toString() }}</td>
    </tr>
  </table>

  <div class="container mt-2">
    <!-- <Button class="ml-2" variant="destructive">Delete</Button> -->
    <FormDelete :data="{ token: props.token }" :onDeletedToken="deleteToken"/>
    <FormRevoke :data="{ token: props.token }" :onTokenUpdated="updateToken"/>
    <FormRename :data="{ token: props.token }" :onTokenUpdated="updateToken"/>
  </div>
</template>