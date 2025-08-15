<template>
  <div class="w-full h-full flex justify-center items-center">
    <div
        class="bg-white w-50 opacity-100 text-black p-2 md:p-4"
        style="height: fit-content"
    >
      <div class="w-full flex mb-2">
        <div class="text-lg font-bold">{{ $t('register') }}</div>
        <div class="ml-auto">
          <span @click="modalStore.closeModal" class="cursor-pointer">x</span>
        </div>
      </div>

      <div class="flex flex-col gap-2">
        <label>{{ $t('name') }}</label>
        <input type="text" class="border" v-model="form.name" required />
        <div v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</div>

        <label>{{ $t('email') }}</label>
        <input type="email" class="border" v-model="form.email" required />
        <div v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</div>

        <label>{{ $t('password') }}</label>
        <input type="password" class="border" v-model="form.password" required />
        <div v-if="errors.password" class="text-red-500 text-sm">{{ errors.password }}</div>

        <label>{{ $t('confirm-password') }}</label>
        <input type="password" class="border" v-model="form.confirm_password" required />
        <div v-if="errors.confirm_password" class="text-red-500 text-sm">{{ errors.confirm_password }}</div>

        <label>{{ $t('faculty') }}</label>
        <select class="border" v-model="form.faculty_id" required>
          <option value="">{{ $t('select_faculty') }}</option>
          <option v-for="fac in faculties" :key="fac.id" :value="fac.id">
            {{ fac.name }}
          </option>
        </select>
        <div v-if="errors.faculty_id" class="text-red-500 text-sm">{{ errors.faculty_id }}</div>

        <button
            class="btn-primary mt-4"
            :disabled="loading"
            :class="loading ? 'btn-disabled' : ''"
            @click="doRegister"
        >
          {{ loading ? $t('registering') : $t('register') }}
        </button>

        <div v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</div>

        <div class="flex justify-end">
          <a class="cursor-pointer" @click="openLoginModal">{{ $t('login') }}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useModalStore } from '~/stores/modal'
import { useUserStore } from '~/stores/user'
import { useFacultyStore } from '~/stores/faculty'
import {useScheduleStore} from "~/stores/schedule";

const modalStore = useModalStore()
const userStore = useUserStore()
const facultyStore = useFacultyStore()
const scheduleStore = useScheduleStore()

const form = ref({
  name: '',
  email: '',
  password: '',
  confirm_password: '',
  faculty_id: ''
})
const loading = ref(false)
const error = ref<string | null>(null)
const errors = ref<Record<string, string>>({})
const faculties = computed(() => facultyStore.faculties)

// onMounted(async () => {
//   // await facultyStore.loadFaculties()
//   faculties.value = facultyStore.faculties
// })

async function doRegister() {
  loading.value = true
  error.value = null
  errors.value = {}

  try {
    await userStore.register(form.value)

    if(userStore.user && userStore.user.default_schedule){
      await scheduleStore.getSchedule(userStore.user.default_schedule.id, scheduleStore.from_date, scheduleStore.to_date)
      modalStore.closeModal()
    } else {
      error.value = 'Registration failed'
    }
  } catch (err) {
    console.log(err)
    error.value = err?.message || 'Registration failed'
    errors.value = err?.errors || {}
  } finally {
    loading.value = false
  }
}

function openLoginModal() {
  modalStore.modalType = 'login'
}
</script>

<style scoped>
.btn-primary {
  background: sandybrown;
  padding: 5px;
  border-radius: 10px;
  color: white;
}

.btn-disabled {
  background: gray !important;
}
</style>
