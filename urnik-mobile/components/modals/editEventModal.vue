<template>
  <div class="w-full h-full flex justify-center items-center">
    <div class="bg-white w-90 opacity-100 text-black p-2 overflow-auto" style="height: 90%">
      <div class="w-full flex mb-2">
        <div class="font-bold">{{ $t('edit_event') }}</div>
        <div class="ml-auto">
          <span @click="modalStore.closeModal" class="cursor-pointer">x</span>
        </div>
      </div>

      <div class="flex flex-col">
        <label>{{ $t('name') }}</label>
        <input type="text" class="border" v-model="editedEvent.name" />

        <label>{{ $t('from_hour') }}</label>
        <input type="number" class="border" v-model="editedEvent.from_hour" />

        <label>{{ $t('to_hour') }}</label>
        <input type="number" class="border" v-model="editedEvent.to_hour" />

        <label>{{ $t('location') }}</label>
        <input type="text" class="border" v-model="editedEvent.location" />

        <label>{{ $t('color') }}</label>
        <div class="flex gap-2 align-items-center">
          <input type="color" v-model="editedEvent.color" />
          <span>{{editedEvent.color}}</span>

          <button @click="editedEvent.color = null">{{ $t('reset') }}</button>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex flex-col">
            <label>{{ $t('start_date') }}</label>
            <input type="date" class="border" v-model="editedEvent.start_date" />
          </div>
          <div class="flex flex-col">
            <label>{{ $t('end_date') }}</label>
            <input type="date" class="border" v-model="editedEvent.end_date" />
          </div>
          <div class="flex flex-col">
            <label>{{ $t('day') }}</label>
            <select v-model="editedEvent.day">
              <option v-for="day in days" :key="day" :value="day">{{ $t(day) }}</option>
            </select>
          </div>
        </div>
      </div>

      <div class="w-full flex justify-between gap-4 mt-4">
        <button class="btn-danger" @click="deleteEvent">
          {{ $t('delete') }}
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
const eventStore = useEventStore()

const days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']

const editedEvent = ref({ ...eventStore.event })

async function saveChanges() {
  await scheduleStore.updateEvent(editedEvent.value)
  modalStore.closeModal()
}

async function deleteEvent() {
  let id = editedEvent.value.id ?? editedEvent.value.eid
  await scheduleStore.removeEvent(id)
  modalStore.closeModal()
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
