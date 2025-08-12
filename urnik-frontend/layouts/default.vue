<template>
  <div>
    <modal class="z-30">
      <add-event-modal v-if="modalStore.modalType == 'addEvent'" />
      <edit-event-modal v-if="modalStore.modalType == 'editEvent'" />
      <login-modal v-if="modalStore.modalType == 'login'" />
      <register-modal v-if="modalStore.modalType == 'register'" />
    </modal>
    <div class="absolute top-0 w-full h-full bg-black opacity-50 z-20" v-if="modalStore.isVisible">

    </div>
    <div class="p-2 w-100 flex">
      <div class="ml-auto flex gap-4">
        <button @click="locale = locale == 'sl' ? 'en' : 'sl'">
          {{ $t('switch_locale') }}:
          <span v-if="locale == 'sl'"> Angleščina</span>
          <span v-if="locale == 'en'"> Slovenian</span>
        </button>
        <button v-if="!user" @click="loginModalOpen">
          {{ $t('login') }}
        </button>
        <span v-else @click="userStore.logout()">
          {{ user.email }}
        </span>
      </div>
    </div>
    <slot />
  </div>
</template>

<script setup lang="ts">
import Modal from '../components/modal.vue'
import AddEventModal from "~/components/modals/addEventModal.vue";
import {useModalStore} from "~/stores/modal";
import {useFacultyStore} from "~/stores/faculty";
import {useUserStore} from "~/stores/user";
import LoginModal from "~/components/modals/loginModal.vue";
import RegisterModal from "~/components/modals/registerModal.vue";
import {useI18n} from "vue-i18n";
import EditEventModal from "~/components/modals/editEventModal.vue";

const modalStore = useModalStore()
const facultyStore = useFacultyStore()
const userStore = useUserStore()
const { locale } = useI18n()

const user = computed(() => userStore.user)

async function loginModalOpen(){
  modalStore.isVisible = true
  modalStore.modalType = 'login'
}

watch(() => userStore.user, (newVal) => {
  console.log('User changed:', newVal)
})
</script>

<style scoped>

</style>