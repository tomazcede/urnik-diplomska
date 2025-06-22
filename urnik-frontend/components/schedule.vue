<template>
<div class="w-full">
  <div class="w-full mb-4 justify-center flex flex-row gap-4">
    <input
        type="date"
        v-model="startOfWeek"
        @change="datesChanged"
        :min="minStartDate"
        :max="endOfWeek"
    />

    <input
        type="date"
        v-model="endOfWeek"
        @change="datesChanged"
        :min="startOfWeek"
        :max="maxEndDate"
    />

  </div>
  <div class="row">
    <div class="col">
      <div class="col w-20 h-10" ></div>
      <div class="col w-20 h-20" v-for="hour in hours" :key="hour">
          {{hour}}
      </div>
    </div>
    <div class="col" v-for="day in days" :key="day">
      <div class="col w-20 h-10" >{{ $t(day) }}</div>
      <div class="col w-20 h-20" v-for="hour in hours" :key="hour">
        <div v-if="schedule[day][hour] && schedule[day][hour].length">
          <div v-for="event in schedule[day][hour]" :key="event.id">
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

const scheduleStore = useScheduleStore();

const schedule = computed(() => scheduleStore.schedule)
const days = computed(() => Object.keys(scheduleStore.schedule))
const hours = computed(() => {
  const min = scheduleStore.minHour
  const max = scheduleStore.maxHour
  return Array.from({ length: max - min + 1 }, (_, i) => i + min)
})

const today = new Date()
const day = today.getDay()
const diffToMonday = today.getDate() - day + (day === 0 ? -6 : 1)
const diffToSunday = today.getDate() - day + (day === 0 ? 0 : 6)

const monday = new Date(today)
monday.setDate(diffToMonday)
const sunday = new Date(today)
sunday.setDate(diffToSunday)

function formatDate(date) {
  return date.toISOString().split('T')[0]
}

const startOfWeek = ref(formatDate(monday))
const endOfWeek = ref(formatDate(sunday))

scheduleStore.getSchedule(1, startOfWeek.value, endOfWeek.value);

function datesChanged(){
  scheduleStore.getSchedule(1, startOfWeek.value, endOfWeek.value);
}

const minStartDate = computed(() => {
  const end = new Date(endOfWeek.value)
  end.setDate(end.getDate() - 6)
  return formatDate(end)
})

// Computed max for endOfWeek = startOfWeek + 6 days
const maxEndDate = computed(() => {
  const start = new Date(startOfWeek.value)
  start.setDate(start.getDate() + 6)
  return formatDate(start)
})

</script>

<style scoped>

</style>