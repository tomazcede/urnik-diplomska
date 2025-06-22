<template>
  <div class="w-full h-full flex justify-center">
    <div class="bg-white w-1/2 opacity-100 text-black p-2 md:p-4 flex flex-col" style="height: fit-content">
      <div class="w-full flex justify-end">
        <span @click="modalStore.closeModal" class="cursor-pointer">x</span>
      </div>

      <label>Name</label>
      <input type="text" class="border" v-model="event.name" />

      <label>From hour</label>
      <input type="number" class="border" v-model="event.from_hour" />

      <label>To hour</label>
      <input type="number" class="border" v-model="event.to_hour" />

      <label>Location</label>
      <input type="text" class="border" v-model="event.location" />

      <div class="flex flex-row gap-4">
        <div class="flex flex-col">
          <label>Start date</label>
          <input type="date" class="border" v-model="event.start_date" />
        </div>
        <div class="flex flex-col">
          <label>End date</label>
          <input type="date" class="border" v-model="event.end_date" />
        </div>
        <div class="flex flex-col">
          <label>Day</label>
          <select v-model="event.day">
            <option v-for="day in days" :key="day" :value="day">{{ $t(day) }}</option>
          </select>
        </div>
      </div>

      <div class="my-4">
        <div
            v-for="(ev, index) in events"
            :key="index"
            class="flex flex-row gap-4"
        >
          <div>{{ ev.name }}</div>
          <span @click="removeFromList(index)">x</span>
        </div>
      </div>

      <div class="w-full flex justify-end gap-4">
        <button @click="addToList">
          Add to list
        </button>

        <button @click="addEvents">
          Add to schedule
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import {useScheduleStore} from "~/stores/schedule";
import {useModalStore} from "~/stores/modal";

const events = ref<any[]>([])
const scheduleStore = useScheduleStore()
const modalStore = useModalStore()

function formatDate(date: Date): string {
  return date.toISOString().split('T')[0]
}

const today = formatDate(new Date())
const days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']

const event = ref({
  name: '',
  from_hour: 7,
  to_hour: 8,
  location: '',
  start_date: today,
  end_date: '',
  day: 'mon'
})

function addToList() {
  events.value.push({ ...event.value }) // Copy to avoid reference issue
  resetEvent()
}

function removeFromList(i) {
  events.value.splice(i, 1);
}

function resetEvent() {
  event.value = {
    name: '',
    from_hour: 7,
    to_hour: 8,
    location: '',
    start_date: today,
    end_date: ''
  }
}

async function addEvents(){
    await scheduleStore.addEvents(events.value)
    events.value = []
    resetEvent()
    modalStore.modalType = null
    modalStore.isVisible = false
}
</script>

<style scoped>
/* Add your custom styles here if needed */
</style>
