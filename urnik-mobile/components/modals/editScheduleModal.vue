<template>
  <div class="w-full h-full flex justify-center items-center">
    <div class="bg-white w-75 md:w-50 opacity-100 text-black p-2 md:p-4" style="height: fit-content">
      <div class="w-full flex mb-2">
        <div class="font-bold">{{ $t('edit_schedule') }}</div>
        <div class="ml-auto">
          <span @click="modalStore.closeModal" class="cursor-pointer">x</span>
        </div>
      </div>

      <div class="flex flex-col">
        <label>{{ $t('name') }}</label>
        <input type="text" class="border" v-model="editSchedule.name" />

        <label>{{ $t('primary_color') }}</label>
        <div class="flex gap-2 align-items-center">
          <input type="color" v-model="editSchedule.primary_color" />
          <span>{{editSchedule.primary_color}}</span>
          <button @click="editSchedule.primary_color = null">{{ $t('reset') }}</button>
        </div>

        <label>{{ $t('secondary_color') }}</label>
        <div class="flex gap-2 align-items-center">
          <input type="color" v-model="editSchedule.secondary_color" />
          <span>{{editSchedule.secondary_color}}</span>
          <button @click="editSchedule.secondary_color = null">{{ $t('reset') }}</button>
        </div>

        <label>{{ $t('background_color') }}</label>
        <div class="flex gap-2 align-items-center">
          <input type="color" v-model="editSchedule.background_color" />
          <span>{{editSchedule.background_color}}</span>
          <button @click="editSchedule.background_color = null">{{ $t('reset') }}</button>
        </div>
      </div>

      <div class="w-full flex justify-between gap-4 mt-4">
        <button class="btn-primary" @click="exportSchedule">
          {{ $t('export') }}
        </button>

        <button class="btn-primary" @click="saveChanges">
          {{ $t('save_changes') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useScheduleStore } from '~/stores/schedule'
import { useModalStore } from '~/stores/modal'
import {useEventStore} from "~/stores/event";

const scheduleStore = useScheduleStore()
const modalStore = useModalStore()

const editSchedule = ref({
  name: scheduleStore.name,
  primary_color: scheduleStore.colors.primary_color,
  secondary_color: scheduleStore.colors.secondary_color,
  background_color: scheduleStore.colors.background_color
})

async function saveChanges() {
  await scheduleStore.updateSchedule(editSchedule.value)
  modalStore.closeModal()
}

// async function deleteEvent() {
//   // await scheduleStore.deleteSchedule()
//   modalStore.closeModal()
// }

function exportSchedule(){
  scheduleStore.exportSchedule()
}
</script>

<style scoped>
.btn-primary {
  background: sandybrown;
  padding: 5px;
  border-radius: 10px;
  color: white
}

.btn-danger {
  background: crimson;
  padding: 5px;
  border-radius: 10px;
  color: white
}
</style>
