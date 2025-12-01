import axios from "axios";

export function change_csrf_token(new_csrf_token: string) {
  const meta_csrf_token = document.querySelector('meta[name="csrf-token"]')!;
  meta_csrf_token.setAttribute("content", new_csrf_token);
  axios.defaults.headers.common["X-CSRF-TOKEN"] = new_csrf_token;
}