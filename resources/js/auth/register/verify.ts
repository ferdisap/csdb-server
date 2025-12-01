import { createApp } from 'vue'
import App from './verify.vue';
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const meta_csrf_token = document.querySelector('meta[name="csrf-token"]')!;
const csrf_token = meta_csrf_token.getAttribute("content");
axios.defaults.headers.common["X-CSRF-TOKEN"] = csrf_token;


createApp(App).mount('#body');