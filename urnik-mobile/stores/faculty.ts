import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";

export const useFacultyStore = defineStore('faculty', {
    state: () => ({
        faculties: {}
    }),
    actions: {
        async fetchFaculties() {
            try {
                const config = useRuntimeConfig()
                const url = `${config.public.apiUrl}/api/faculty/all`

                const data = await $fetch(url, {
                    method: 'POST',
                })

                console.log(data)
                this.faculties = data.faculties;
            } catch (error) {
                console.error(error)
            }
        },
    }
})