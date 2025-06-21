<template>
<div class="w-full">
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

scheduleStore.getSchedule(1);

// const days = [
//     'mon', 'tue', 'wen', 'thu', 'fri'
// ]

const schedule = computed(() => scheduleStore.schedule)
const days = computed(() => Object.keys(scheduleStore.schedule))
const hours = computed(() => {
  const min = scheduleStore.minHour
  const max = scheduleStore.maxHour
  return Array.from({ length: max - min + 1 }, (_, i) => i + min)
})</script>

<style scoped>

</style>