import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";
import {useModalStore} from "~/stores/modal";

export const useEventStore = defineStore('event', {
    state: () => ({
        events: [],
        event: {},
        search_events: [],
        last_page: 0
    }),
    actions: {
        async parseFromFile(schedule_import: object) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/event/parse`

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

        async paginate(filter: object){
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/event/paginate?page=${filter.current_page}`

            try {
                const data = await $fetch(url, {
                    method: 'POST',
                    body: {
                        filter: filter
                    }
                })

                this.search_events = data.data
                this.last_page = data.last_page
            } catch (error) {
                console.error(error)
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