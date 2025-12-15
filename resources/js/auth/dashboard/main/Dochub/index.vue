<script setup lang="ts">
import type { ColumnDef } from '@tanstack/vue-table';
import { h, onMounted, ref } from 'vue';
import type { Ref } from 'vue';
import DataTable from './DataTable.vue';
import DataDetail from './DataDetail.vue';
import { Skeleton } from '@shadcn/components/ui/skeleton';

import { useRoute, useRouter } from 'vue-router';
import { castToToken, type token, columns_token, data_dummy } from './dochub_token_columnn';
import axios from 'axios';
import DataTableSavedToken from './DataTableSavedToken.vue';



// detail oauth
const router = useRouter();
const route = useRoute();
const selectedToken = ref<token | null>(null);

function setSelectedTokenId(data:token){
  selectedToken.value = data;
  router.push({
    name: 'Dochub Token Detail',
    params: {
      id: data.id
    }
  })
}
// function fetchClientById(id:string, onSuccess:Function){
//   axios.get(`/oauth-token/${id}`)
//   .then(response => {    
//     onSuccess(castToToken(response.data.token));
//   })
// }

onMounted(async () => {
  const token_id = route.params.id as string;
  // if(token_id) fetchClientById(token_id, setSelectedTokenId);
})

</script>

<template>
    <div class="container p-10 mx-auto">
      <h1 class="w-full text-center mb-3 font-bold text-xl">Dochub Token Index</h1>
      <DataTable @select-token="(data:token) => setSelectedTokenId(data)"/>
    </div>
    <div class="container p-10 mx-auto">
      <h1 class="w-full text-center mb-3 font-bold text-xl">Dochub Self Token Index</h1>
      <DataTableSavedToken @select-token="(data:token) => setSelectedTokenId(data)"/>
    </div>

    <div v-if="route.params.id" class="w-full container p-10 mx-auto">
      <DataDetail v-if="selectedToken" v-model:token="selectedToken"/>
      <Skeleton v-else class="w-full container p-10 mx-auto min-h-[200px]"/>
    </div>

</template>