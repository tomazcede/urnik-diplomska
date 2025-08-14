<template>
  <div class="w-full h-full flex justify-center items-center">
    <div class="bg-white w-50 opacity-100 text-black p-2 md:p-4" style="height: fit-content">
      <div class="w-full flex mb-2">
        <div class="text-lg font-bold">{{ $t('login') }}</div>
        <div class="ml-auto">
          <span @click="modalStore.closeModal" class="cursor-pointer">x</span>
        </div>
      </div>

      <div class="flex flex-col gap-2">
        <label>{{ $t('email') }}</label>
        <input type="email" class="border" v-model="credentials.email" />
        <div v-if="errors && errors.email" class="text-red-500 text-sm mt-2">{{ errors.email }}</div>

        <label>{{ $t('password') }}</label>
        <input type="password" class="border" v-model="credentials.password" />
        <div v-if="errors && errors.password" class="text-red-500 text-sm mt-2">{{ errors.password }}</div>

        <button
            class="btn-primary mt-4"
            :disabled="loading"
            :class="loading ? 'btn-disabled' : ''"
            @click="doLogin"
        >
          {{ loading ? $t('logging_in') : $t('login') }}
        </button>

        <div v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</div>

        <div class="flex justify-content-end">
          <a class="cursor-pointer" @click="openRegisterModal">{{ $t('register') }}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useModalStore } from '~/stores/modal'
import { useUserStore } from '~/stores/user'
import {useScheduleStore} from "~/stores/schedule";

const modalStore = useModalStore()
const userStore = useUserStore()
const scheduleStore = useScheduleStore()

const credentials = ref({
  email: '',
  password: ''
})
const loading = ref(false)
const error = ref<string|null>(null)
const errors = ref({})

async function doLogin() {
  loading.value = true
  error.value = null

  try {
    await userStore.login(credentials.value)

    if(userStore.user && userStore.user.default_schedule){
      await scheduleStore.getSchedule(userStore.user.default_schedule.id, scheduleStore.from_date, scheduleStore.to_date)
      modalStore.closeModal()
    } else {
      error.value = 'Login failed'
    }
  } catch (err) {
    console.log(err)
    error.value = err?.message || 'Login failed'
    errors.value = err?.errors || {}
  } finally {
    loading.value = false
  }
}

function openRegisterModal(){
  modalStore.modalType = 'register'
}
</script>

<style scoped>
.btn-primary {
  background: sandybrown;
  padding: 5px;
  border-radius: 10px;
  color: white
}

.btn-disabled {
  background: gray !important;
}
</style>
