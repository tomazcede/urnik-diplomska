import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        errors: {}
    }),
    actions: {
        async login(credentials: object) {
            try {
                const config = useRuntimeConfig()

                await $fetch(`${config.public.apiUrl}/sanctum/csrf-cookie`, {
                    credentials: 'include'
                }).then(async () => {
                    const value = `; ${document.cookie}`
                    const parts = value.split(`; XSRF-TOKEN=`)
                    let token = decodeURIComponent(parts.pop().split(';').shift())

                    const data = await $fetch(`${config.public.apiUrl}/api/auth/login`, {
                        method: 'POST',
                        body: credentials,
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': token,
                        },
                    })

                    this.user = data.user
                    this.errors = {}
                })


            } catch (error) {
                console.log(error)
            }
        },

        async getCurrentUser() {
            try {
                const config = useRuntimeConfig()
                const url = `${config.public.apiUrl}/api/auth/current`

                await $fetch(`${config.public.apiUrl}/sanctum/csrf-cookie`, {
                    credentials: 'include'
                }).then(async () => {
                    const value = `; ${document.cookie}`
                    const parts = value.split(`; XSRF-TOKEN=`)
                    let token = decodeURIComponent(parts.pop().split(';').shift())

                    const data = await $fetch(url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': token,
                        },
                    })

                    this.user = data.user
                    this.errors = {}
                })
            } catch (error) {
                console.log(error)
            }
        },

        async register(payload: object) {
            try {
                const config = useRuntimeConfig()
                const url = `${config.public.apiUrl}/api/auth/register`

                await $fetch(`${config.public.apiUrl}/sanctum/csrf-cookie`, {
                    credentials: 'include'
                }).then(async () => {
                    const value = `; ${document.cookie}`
                    const parts = value.split(`; XSRF-TOKEN=`)
                    let token = decodeURIComponent(parts.pop().split(';').shift())

                    const data = await $fetch(url, {
                        method: 'POST',
                        body: payload,
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': token,
                        },
                    })

                    this.user = data.user
                    this.errors = {}
                })
            } catch (error: any) {
                this.errors = error?.data?.errors || {}
                throw error?.data || error
            }
        },

        async logout() {
            try {
                const config = useRuntimeConfig()
                const url = `${config.public.apiUrl}/api/auth/logout`

                await $fetch(`${config.public.apiUrl}/sanctum/csrf-cookie`, {
                    credentials: 'include'
                }).then(async () => {
                    const value = `; ${document.cookie}`
                    const parts = value.split(`; XSRF-TOKEN=`)
                    let token = decodeURIComponent(parts.pop().split(';').shift())

                    await $fetch(url, {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': token,
                        },
                    })

                    this.user = null
                    this.errors = {}
                })
            } catch (error: any) {
                this.errors = error?.data?.errors || {}
                throw error?.data || error
            }
        },

        async delete() {
            try {
                const config = useRuntimeConfig()
                const url = `${config.public.apiUrl}/api/user/delete/${this.user.id}`

                await $fetch(`${config.public.apiUrl}/sanctum/csrf-cookie`, {
                    credentials: 'include'
                }).then(async () => {
                    const value = `; ${document.cookie}`
                    const parts = value.split(`; XSRF-TOKEN=`)
                    let token = decodeURIComponent(parts.pop().split(';').shift())

                    await $fetch(url, {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': token,
                        },
                    })

                    this.user = null
                    this.errors = {}
                })
            } catch (error: any) {
                this.errors = error?.data?.errors || {}
                throw error?.data || error
            }
        }
    }
})