import axios from "axios";

const isProduction = import.meta.env.PROD;

axios.defaults.baseURL = isProduction
  ? ""
  : "http://localhost:8000";

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

const token = localStorage.getItem("auth_token") || "";

if(token !== "")
{
  axios.defaults.headers.common["Authorization"] = `Bearer ${ token }`;
}

if (import.meta.env.DEV) {
  console.log("API Base URL:", axios.defaults.baseURL);
}

export default axios;