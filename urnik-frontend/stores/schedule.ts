import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";

export const useScheduleStore = defineStore('schedule', {
    state: () => ({
        schedule: [],
        name: '',
        minHour: 7,
        maxHour: 15,
        currentId: null
    }),
    actions: {
        async getSchedule(id: number, from: string, to:string) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/show/${id}`

            try{
                this.currentId = id

                const data = await $fetch(url, {
                    method: 'POST',
                    body: {from, to}
                })

                localStorage.setItem('schedule', JSON.stringify(data))

                this.name = data.name ?? ''
                this.schedule = data.schedule ?? []
                this.minHour = data.min_hour ?? 7
                this.maxHour = data.max_hour ?? 15
            } catch (error) {
                this.currentId = null

                let localData = localStorage.getItem('schedule')
                const data = localData ? JSON.parse(localData) : {}

                this.name = data.name ?? ''
                this.schedule = data.schedule ?? []
                this.minHour = data.min_hour ?? 7
                this.maxHour = data.max_hour ?? 15
            }
        },
        async addEvents(events){
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/add-events`
            let localData = localStorage.getItem('schedule')

            try{
                const postdata = {
                    events: events,
                    ...(this.currentId ? { id: this.currentId } : { json: localData })
                };

                const data = await $fetch(url, {
                    method: 'POST',
                    body: postdata
                })

                localStorage.setItem('schedule', JSON.stringify(data))

                this.name = data.name ?? ''
                this.schedule = data.schedule ?? []
                this.minHour = data.min_hour ?? 7
                this.maxHour = data.max_hour ?? 15
            } catch (error) {
                console.log(error)
            }
        }
    },
})