<script setup lang="ts">
import type { ColumnDef } from '@tanstack/vue-table';
import { h, onMounted, ref } from 'vue';
import type { Ref } from 'vue';
import DataTable from './DataTable.vue';
import DataDetail from './DataDetail.vue';
import { Skeleton } from '@shadcn/components/ui/skeleton';

import { useRoute, useRouter } from 'vue-router';
import { castToClient, type client, columns_client, data_dummy } from './oauth_client_column';
import axios from 'axios';



// detail oauth
const router = useRouter();
const route = useRoute();
const selectedClient = ref<client | null>(null);

function setSelectedClientId(data:client){
  selectedClient.value = data;
  router.push({
    name: 'OAuth Client',
    params: {
      id: data.id
    }
  })
}
function fetchClientById(id:string, onSuccess:Function){
  axios.get(`/oauth-client/${id}`)
  .then(response => {    
    onSuccess(castToClient(response.data.client));
  })
}

onMounted(async () => {
  const client_id = route.params.id as string;
  if(client_id) fetchClientById(client_id, setSelectedClientId);  

  // console.log(client_id);
})

</script>

<template>
    <div class="container p-10 mx-auto">
      <h1 class="w-full text-center mb-3 font-bold text-xl">OAuth Client Index</h1>
      <!-- <DataTable v-if="data.length" :columns="columns" :pagination="{pageIndex:0, pageSize:5}" :data="data" @select-client="(data:client) => setSelectedClientId(data)"/> -->
      <DataTable @select-client="(data:client) => setSelectedClientId(data)"/>
      
    </div>

    <div v-if="route.params.id" class="w-full container p-10 mx-auto">
      <DataDetail v-if="selectedClient" v-model:client="selectedClient"/>
      <Skeleton v-else class="w-full container p-10 mx-auto min-h-[200px]"/>
    </div>

</template>