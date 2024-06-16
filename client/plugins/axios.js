import axios from 'axios';

export default defineNuxtPlugin(() => {
  const instance = axios.create({
    baseURL: 'http://localhost:8000/api' // Laravel API URL
  });

  return {
    provide: {
      axios: instance
    }
  };
});
