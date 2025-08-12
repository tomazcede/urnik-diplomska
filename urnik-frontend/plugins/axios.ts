import axios from 'axios'

export default defineNuxtPlugin(() => {
    const api = axios.create({
        baseURL: 'http://127.0.0.1:8000',
        withCredentials: true,
    })

    return {
        provide: {
            axios: api
        }
    }
})