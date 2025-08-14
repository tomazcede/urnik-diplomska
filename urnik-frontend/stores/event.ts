import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";
import {useModalStore} from "~/stores/modal";

export const useEventStore = defineStore('event', {
    state: () => ({
        events: [],
        event: {}
    }),
    actions: {
        async parseFromFile(schedule_import: object) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/event/parse`

            console.log(schedule_import)
            const formData = new FormData()
            formData.append('file', schedule_import.file)
            formData.append('faculty_id', schedule_import.faculty_id ?? null)

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

        async paginate(page: number, search: string){
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/event/paginate`

            try {
                const data = await $fetch(url, {
                    method: 'POST',
                    body: {
                        page,
                        search
                    }
                })

                console.log(data);
            } catch (error) {
                console.error(error)

                return []
            }
        },

        openEditModal(event: object) {
            const modalstore = useModalStore()

            this.event = event
            modalstore.modalType = "editEvent"
            modalstore.isVisible = true
        }
    }
})