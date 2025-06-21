import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";

export const useScheduleStore = defineStore('schedule', {
    state: () => ({
        schedule: [],
        name: '',
        minHour: 7,
        maxHour: 15,
    }),
    // getters: {
    //     events: (state) => state.events,
    // },
    actions: {
        async getSchedule(id: number) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/show/${id}`
            console.log('Calling URL:', config.public.apiUrl)

            const data = await $fetch(url, {
                method: 'POST',
            })

            console.log(data)

            this.name = data.name
            this.schedule = data.schedule
            this.minHour = data.min_hour
            this.maxHour = data.max_hour
        }
    },
})