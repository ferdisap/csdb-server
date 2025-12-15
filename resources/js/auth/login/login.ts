import { createApp } from 'vue'
import App from './login.vue';
// import axios from 'axios';
// import { api, set_default_setting_axios } from '../../utils/fetch';
// import { CSRF_URI } from 'resources/js/config/app.config';
// import { getCookie } from 'resources/js/utils/helper';
// import router from './router'


// set_default_setting_axios();

// await api.get(CSRF_URI);
// const token = getCookie('XSRF-TOKEN'); // helper baca cookie
// api.defaults.headers["X-XSRF-TOKEN"] = decodeURIComponent(token)

createApp(App).mount('#body');

// createApp(App).use(router).mount('#app');

// async function refreshToken() {
//     const refreshToken = localStorage.getItem("refresh_token");
//     if (!refreshToken) return;

//     const { data } = await axios.post("/oauth/token", {
//       grant_type: "refresh_token",
//       refresh_token: refreshToken,
//       client_id: import.meta.env.VITE_PASSPORT_CLIENT_ID,
//       client_secret: import.meta.env.VITE_PASSPORT_CLIENT_SECRET,
//     })

//     // set to storage

//     accessToken.value = data.access_token
//     refreshToken.value = data.refresh_token
//     expiresAt.value = Date.now() + data.expires_in * 1000

//     localStorage.setItem("access_token", accessToken.value)
//     localStorage.setItem("refresh_token", refreshToken.value)
//     localStorage.setItem("expires_at", expiresAt.value.toString())
//   }