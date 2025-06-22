import { defineStore } from 'pinia'

export const useModalStore = defineStore('modal', {
    state: () => ({
        isVisible: false,
        modalType: null
    }),
    actions: {
        closeModal(){
            this.isVisible = false
            this.modalType = null
        }
    },
})