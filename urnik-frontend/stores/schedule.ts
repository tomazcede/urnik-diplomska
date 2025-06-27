import { defineStore } from 'pinia'
import { useRuntimeConfig } from "#app";

export const useScheduleStore = defineStore('schedule', {
    state: () => ({
        schedule: [],
        name: '',
        minHour: 7,
        maxHour: 15,
        currentId: null,
        colors: {
            primary_color: null,
            secondary_color: null,
            background_color: null
        }
    }),
    actions: {
        async getSchedule(id: any, from: string, to:string) {
            let localData = localStorage.getItem('schedule')
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/show`

            try{
                this.currentId = id

                const data = await $fetch(url, {
                    method: 'POST',
                    body: {from, to, id, json: localData}
                })

                this.setSchedule(data)
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

                this.setSchedule(data)
            } catch (error) {
                console.log(error)
            }
        },

        setSchedule(data: object){
            localStorage.setItem('schedule', JSON.stringify(data))

            this.name = data.name ?? ''
            this.colors.primary_color = data.primary_color
            this.colors.secondary_color = data.secondary_color
            this.colors.background_color = data.background_color
            this.schedule = data.schedule ?? []
            this.minHour = data.min_hour ?? 7
            this.maxHour = data.max_hour ?? 15
        }
    },
})