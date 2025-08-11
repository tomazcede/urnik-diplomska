import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";

export const useEventStore = defineStore('event', {
    state: () => ({
        events: []
    }),
    actions: {
        async parseFromFile(schedule_import: object) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/event/parse`

            console.log(schedule_import)
            const formData = new FormData()
            formData.append('file', schedule_import.file)
            // formData.append('faculty_id', schedule_import.faculty_id || '')

            try {
                const data = await $fetch(url, {
                    method: 'POST',
                    body: formData
                })

                return data.events;
            } catch (error) {
                console.error(error)

                return []
            }
        },
    }
})