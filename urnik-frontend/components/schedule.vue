<template>
<div class="w-full">
  <div class="relative">
    <div class="w-full mb-4 justify-center flex flex-row gap-4">

      <VueDatePicker
          class="w-25"
          v-model="dateRange"
          :range="{ maxRange: 6 }"
          @update:model-value="datesChanged"
          :enable-time-picker="false"
      >

      </VueDatePicker>
    </div>

    <button class="absolute right-2 top-0" :title="$t('add_new_event')" @click="sendData">
        +
    </button>
  </div>
  <div class="row">
    <div class="col">
      <div class="h-5 md:h-10" ></div>
      <div class="h-10 md:h-20" v-for="hour in hours" :key="hour">
          {{hour}}
      </div>
    </div>
    <div class="col" v-for="day in days" :key="day">
      <div class="h-5 md:h-10" >{{ $t(day) }}</div>
      <div class="h-10 md:h-20" v-for="hour in hours" :key="hour">
        <div class="row" v-if="schedule[day][hour] && schedule[day][hour].length">
          <div class="col" v-for="event in schedule[day][hour]" :key="event.id">
            {{ event.name }}
          </div>
        </div>
        <div v-else>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script setup lang="ts">
import { useScheduleStore } from "~/stores/schedule";
import {computed} from "vue";
import {useModalStore} from "~/stores/modal";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const modalStore = useModalStore()
const scheduleStore = useScheduleStore()

const schedule = computed(() => scheduleStore.schedule)
const days = computed(() => Object.keys(scheduleStore.schedule))
const hours = computed(() => {
  const min = scheduleStore.minHour
  const max = scheduleStore.maxHour
  return Array.from({ length: max - min + 1 }, (_, i) => i + min)
})

function datesChanged(){
  scheduleStore.getSchedule(1, dateRange.value[0], dateRange.value[1]);
}

async function sendData(){
  modalStore.isVisible = true
  modalStore.modalType = 'addEvent'
}

const dateRange = ref();

function formatDate(date) {
  return date.toISOString().split('T')[0]
}

onMounted(() => {
  const today = new Date()
  const day = today.getDay()
  const diffToMonday = today.getDate() - day + (day === 0 ? -6 : 1)
  const diffToSunday = today.getDate() - day + (day === 0 ? 0 : 7)

  const monday = new Date(today)
  monday.setDate(diffToMonday)
  const sunday = new Date(today)
  sunday.setDate(diffToSunday)
  dateRange.value = [formatDate(monday), formatDate(sunday)];

  scheduleStore.getSchedule(1, dateRange.value[0], dateRange.value[1]);
})

</script>

<style scoped>

</style>