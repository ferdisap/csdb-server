import { createApp } from 'vue'
import App from './dashboard.vue';
import { isAuth, userData } from '../../utils/autentication';
import { api, set_default_setting_axios } from '../../utils/fetch';
import dashboard_router from './router';
import { AUTH_CHECK_URI } from 'resources/js/config/app.config';


// set_default_setting_axios();

// if(!is_token_reusable()){
//   window.location.href = "/login"
// }

api.get(AUTH_CHECK_URI).then((response) => {
  isAuth.value = true;
  userData.value = {
    name: response.data.name,
    email: response.data.email
  }
})

const app = createApp(App);
app.use(dashboard_router);
app.mount('#body');




