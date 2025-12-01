// src/router/index.js
import { createRouter, createWebHistory, RouteLocationRaw } from 'vue-router';

// import komponen halaman
import Biodata from './main/profile/biodata.vue';
import OAuthClient from './main/OAuthClient/index.vue';
import OAuthToken from './main/OAuthToken/index.vue';
import CObject from './main/CObject/index.vue';
import CObject_Create from './main/CObject/Create.vue';
import Trash from './main/CObject/trash/index.vue';

const routes = [
  // biodata
  { path: '/dashboard/biodata', name: 'Biodata', component: Biodata },
  // oauth
  { path: '/dashboard/oauth-client/:id?', name: 'OAuth Client', component: OAuthClient },
  { path: '/dashboard/oauth-token/:id?', name: 'OAuth Token', component: OAuthToken },
  // csdb
  { path: '/dashboard/csdb-object', name: 'CSDB Object Index', component: CObject },
  { path: '/dashboard/csdb-object/trash', name: 'Trash CSDB Object Index', component: Trash },
  { path: '/dashboard/csdb-object/trash/:filename?', name: 'Trash CSDB Object Index', component: Trash },
  { path: '/dashboard/csdb-object/:filename?', name: 'CSDB Object Index', component: CObject },
  { path: '/dashboard/csdb-object/create', name: 'CSDB Object Create', component: CObject_Create },

]

const dashboard_router = createRouter({
  history: createWebHistory(), // pakai mode history
  routes
})

export function goto(name: string, params?: Record<string,any>, query?:Record<string,any>, hash?:string){
  const config:RouteLocationRaw = {
    name:name
  };
  if(params){
    config.params = params;
  }
  if(query){
    config.query = query;
  }
  if(hash){
    config.hash = hash;
  }
  dashboard_router.push(config)
}

export default dashboard_router
