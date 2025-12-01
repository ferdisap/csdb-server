import axios from "axios";
import { getAuthToken } from "./autentication";
import { BACKEND_ORIGIN } from "../config/app.config";

/** @deprecated saat ini token disimpan di cookie */
export function set_default_setting_axios(){
  axios.defaults.withCredentials = true;
  axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
  
  const meta_csrf_token = document.querySelector('meta[name="csrf-token"]')!;
  const csrf_token = meta_csrf_token.getAttribute("content");
  axios.defaults.headers.common["X-CSRF-TOKEN"] = csrf_token;
  
  const access_token = getAuthToken();
  if (access_token) {
    axios.defaults.headers.common["Authorization"] = "Bearer " + access_token;
  }
}


export const api = axios.create({
  baseURL: BACKEND_ORIGIN,
  withCredentials:true
})