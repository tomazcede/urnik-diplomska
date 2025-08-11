<template>
  <div class="w-full h-full flex justify-center items-center">
    <div class="bg-white w-75 md:w-50 opacity-100 text-black p-2 md:p-4" style="height: fit-content">
      <div class="w-full flex mb-2">
        <div class="flex gap-4">
          <button v-for="tab_btn in tabs" @click="tab = tab_btn.tab">{{ $t(tab_btn.name) }}</button>
        </div>
        <div class="ml-auto">
          <span @click="modalStore.closeModal" class="cursor-pointer">x</span>
        </div>
      </div>

      <div v-if="tab == 'new'" class="flex flex-col">
        <label>{{ $t('name') }}</label>
        <input type="text" class="border" v-model="event.name" />

        <label>{{ $t('from_hour') }}</label>
        <input type="number" class="border" v-model="event.from_hour" />

        <label>{{ $t('to_hour') }}</label>
        <input type="number" class="border" v-model="event.to_hour" />

        <label>{{ $t('location') }}</label>
        <input type="text" class="border" v-model="event.location" />

        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex flex-col">
            <label>{{ $t('start_date') }}</label>
            <input type="date" class="border" v-model="event.start_date" />
          </div>
          <div class="flex flex-col">
            <label>{{ $t('end_date') }}</label>
            <input type="date" class="border" v-model="event.end_date" />
          </div>
          <div class="flex flex-col">
            <label>{{ $t('day') }}</label>
            <select v-model="event.day">
              <option v-for="day in days" :key="day" :value="day">{{ $t(day) }}</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="tab == 'import'" class="flex flex-col">
        <label>{{ $t('faculty') }}</label>
        <input type="text" class="border" v-model="schedule_import.faculty_id" />

        <label>{{ $t('file') }}</label>
        <input type="file" @change="handleFileChanged"/>

        <button class="btn-primary" :disabled="import_disabled" :class="import_disabled ? 'btn-disabled' : ''" @click="importEvents">
          {{ $t('import') }}
        </button>
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
        <button class="btn-primary" @click="addToList">
          {{ $t('add_to_list') }}
        </button>

        <button class="btn-primary" :disabled="!events.length" :class="!events.length ? 'btn-disabled' : ''" @click="addEvents">
          {{ $t('add_to_schedule') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import {useScheduleStore} from "~/stores/schedule";
import {useEventStore} from "~/stores/event";
import {useModalStore} from "~/stores/modal";

const events = ref<any[]>([])
const scheduleStore = useScheduleStore()
const eventStore = useEventStore()
const modalStore = useModalStore()
const tab = ref('new')
const tabs = [
  {
    name: 'new_event',
    tab: 'new'
  },
  {
    name: 'import_events',
    tab: 'import'
  }
]
const import_disabled = ref(true)

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

const schedule_import = ref({
  faculty_id: null,
  file: null
})


function addToList() {
  events.value.push({ ...event.value })
  resetEvent()
}

function handleFileChanged(event) {
  schedule_import.value.file = event.target.files[0];
  import_disabled.value = false
}

async function importEvents() {
  let ev = await eventStore.parseFromFile(schedule_import.value)

  events.value.push(...ev)
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
