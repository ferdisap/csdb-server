import axios, { AxiosError, AxiosResponse } from "axios";
import { change_csrf_token } from "./csrfToken";
import { set_default_setting_axios } from "./fetch";
import { ref } from "vue";

export const isAuth = ref(false);
export const userData = ref<{email?:string, name?:string}>({});

/** @deprecated saat ini token disimpan di cookie */
export function is_token_reusable() {
  const access_token = localStorage.getItem("access_token");
  const expires_in = Number(localStorage.getItem("expires_in"));

  return (access_token && !(expires_in - (Date.now())));
}

/**
 * @deprecated
 * karena seharusnya tidak perlu lagi nembak /is-auth
 * to genereate session if access token exist
 * redirect if response data "redirect_to" existed
 */
export async function check_in(): Promise<boolean> {
  if (is_token_reusable()) {
    // const redirect_to = "http://localhost:3001/oauth/redirect";
    const search_param = new URLSearchParams(window.location.search);
    const redirect_to = search_param.get("redirect_to");
    const isAuth = await axios.get("/is-auth", {
      headers: {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0'
      }
    });
    if (isAuth.status === 401) {
      const response = await axios.post("/session-regenerate");
      if (response.statusText === 'OK') {
        if (redirect_to) {
          window.location.href = redirect_to;
        } else {
          window.location.href = "/dashboard";
        }
        return true
      }
    }
    else if (isAuth.status === 200) {
      return true;
    }
    return false;
  }
  return false;
}

/** @deprecated saat ini token disimpan di cookie */
export function generateSession() :Promise<AxiosResponse|AxiosError>{
  return new Promise((resolve, reject) => {
    axios.post("/session-regenerate")
    .then((rsp:AxiosResponse) => {
      const new_csrf_token = rsp.data.csrf_token;
      change_csrf_token(new_csrf_token);

      set_default_setting_axios();
      
      return resolve(rsp)
    })
    .catch((e:AxiosError) => {
      return reject(e)
    })
  })
}

export function logout(onSuccess: Function, onFailure: Function | null = null) {
  if (!isAuthTokenExpire()) {
    axios.get("/logout")
      .then((response) => {
        setAuthToken('');
        setAuthTokenExpireDate('');
        onSuccess(response);
      })
      .catch((e) => {
        if (onFailure) onFailure(e);
      })
  } else {
    if (onFailure) onFailure();
  }
}

/** @deprecated saat ini token disimpan di cookie */
export function login(url: string, data: FormData, onSuccess: Function, onFailure: Function | null = null) {
  axios.post(url, data)
    .then(response => {
      const data = response.data;

      const new_csrf_token = data.csrf_token;
      change_csrf_token(new_csrf_token);

      const token = data.token;

      setAuthToken(token.access_token);
      setAuthTokenExpireDate(token.expires_in);

      set_default_setting_axios();

      onSuccess(response);
    })
    .catch(e => {
      if (onFailure) onFailure(e);
    })
}

/** @deprecated saat ini token disimpan di cookie */
export function register(url: string, data: FormData, onSuccess: Function, onFailure: Function | null = null) {
  axios.post(url, data)
    .then(response => {
      const data = response.data;

      const new_csrf_token = data.csrf_token;
      change_csrf_token(new_csrf_token);

      const token = data.token;

      setAuthToken(token.access_token);
      setAuthTokenExpireDate(token.expires_in);

      set_default_setting_axios();

      onSuccess(response);
    })
    .catch(e => {
      if (onFailure) onFailure(e);
    })
}

/** @deprecated saat ini token disimpan di cookie */
export function getAuthToken() {
  return localStorage.getItem('access_token');
}

/** @deprecated saat ini token disimpan di cookie */
export function getAuthTokenExpireDate() {
  return localStorage.getItem('expires_in');
}

/** @deprecated saat ini token disimpan di cookie */
export function isAuthTokenExpire(): boolean {
  const expDateString = getAuthTokenExpireDate();
  if (expDateString) {
    const expirationDate = new Date(expDateString); // Example expiration date
    expirationDate.setMinutes(expirationDate.getMinutes() - 1);
    const now = new Date();
    return expirationDate.getTime() < now.getTime(); // true if expired
  }
  return true
}

/** @deprecated saat ini token disimpan di cookie */
export function setAuthToken(accessToken: string) {
  localStorage.setItem("access_token", accessToken);
}

/** @deprecated saat ini token disimpan di cookie */
export function setAuthTokenExpireDate(date: string) {
  localStorage.setItem("expires_in", date);
}