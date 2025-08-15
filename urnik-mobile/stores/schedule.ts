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
        },
        from_date: "",
        to_date: ""
    }),
    actions: {
        async getSchedule(id: any, from: string, to:string) {
            let localData = localStorage.getItem('schedule')
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/show`

            try{
                this.currentId = id
                this.from_date = from
                this.to_date = to

                const data = await $fetch(url, {
                    method: 'POST',
                    body: {from, to,
                        ...(this.currentId ? { id: this.currentId } : { json: localData })
                    }
                })

                this.setSchedule(data)
            } catch (error) {
                this.currentId = null

                let localData = localStorage.getItem('schedule')
                const data = localData ? JSON.parse(localData) : {}

                this.setSchedule(data)
            }
        },
        async addEvents(events) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/add-events`
            let localData = localStorage.getItem('schedule')
            const { $axios } = useNuxtApp()

            try {
                const postdata = {
                    events: events,
                    ...(this.currentId ? { id: this.currentId } : { json: localData })
                }

                const { data } = await $axios.post(url, postdata, {
                    withCredentials: true
                })

                this.setSchedule(data)
            } catch (error) {
                console.error(error)
            }
        },

        async updateEvent(event: object) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/event/update`
            let localData = localStorage.getItem('schedule')
            const { $axios } = useNuxtApp()

            try {
                const postdata = {
                    event: event,
                    ...(this.currentId ? { schedule_id: this.currentId } : { json: localData })
                }

                const { data } = await $axios.post(url, postdata, {
                    withCredentials: true
                })

                this.setSchedule(data)
            } catch (error) {
                console.error(error)
            }
        },

        async removeEvent(event_id: string) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/remove-event`
            let localData = localStorage.getItem('schedule')
            const { $axios } = useNuxtApp()

            try {
                const postdata = {
                    event_id: event_id,
                    ...(this.currentId ? { id: this.currentId } : { json: localData })
                }

                const { data } = await $axios.post(url, postdata, {
                    withCredentials: true
                })

                this.setSchedule(data)
            } catch (error) {
                console.error(error)
            }
        },

        async updateSchedule(scheduleData: object) {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/update`
            let localData = localStorage.getItem('schedule')
            const { $axios } = useNuxtApp()

            try {
                const postdata = {
                    name: scheduleData.name,
                    primary_color: scheduleData.primary_color ?? null,
                    secondary_color: scheduleData.secondary_color ?? null,
                    background_color: scheduleData.background_color ?? null,
                    ...(this.currentId ? { id: this.currentId } : { json: localData })
                }

                const { data } = await $axios.post(url, postdata, {
                    withCredentials: true
                })

                this.setSchedule(data)
            } catch (error) {
                console.error(error)
            }
        },

        async exportSchedule() {
            const config = useRuntimeConfig()
            const url = `${config.public.apiUrl}/api/schedule/export`
            let localData = localStorage.getItem('schedule')
            const { $axios } = useNuxtApp()

            try {
                const postdata = {
                    ...(this.currentId ? { id: this.currentId } : { json: localData })
                }

                await $axios.post(url, postdata, {
                    withCredentials: true,
                    responseType: 'blob'
                }).then(response => {
                    const blob = new Blob([response.data], { type: 'text/calendar' })
                    const downloadUrl = window.URL.createObjectURL(blob)
                    const link = document.createElement('a')
                    link.href = downloadUrl
                    link.download = 'schedule.ics'
                    document.body.appendChild(link)
                    link.click()
                    link.remove()
                    window.URL.revokeObjectURL(downloadUrl)
                })
            } catch (error) {
                console.error(error)
            }
        },


        setSchedule(data: object){
            localStorage.setItem('schedule', JSON.stringify(data))

            this.name = data.name ?? ''
            this.colors.primary_color = data.primary_color
            this.colors.secondary_color = data.secondary_color
            this.colors.background_color = data.background_color
            this.schedule = data.schedule ?? {
                "mon": [],
                "tue": [],
                "wed": [],
                "thu": [],
                "fri": [],
                "sat": [],
                "sun": []
            }
            this.minHour = data.min_hour ?? 7
            this.maxHour = data.max_hour ?? 15
        }
    },
})