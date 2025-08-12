<template>
<div class="w-full">
  <div class="relative">
    <div class="w-full mb-4 justify-center flex flex-row gap-4">

      <VueDatePicker
          class="w-50 md:w-25"
          v-model="dateRange"
          :range="{ maxRange: 6 }"
          @update:model-value="datesChanged"
          :enable-time-picker="false"
          :clearable="false"
      >

      </VueDatePicker>
    </div>

    <button class="absolute right-2 top-0" :title="$t('add_new_event')" @click="sendData">
        +
    </button>
  </div>
  <div>
    <table class="w-full md:w-[75%] mx-auto table-fixed border-collapse border border-gray-300 text-xs">
      <thead>
      <tr :style="colors.primary_color ? 'background-color: ' + colors.primary_color + ';' : 'background-color: var(--color-gray-100);'">
        <th v-if="!isMobile" class="border border-gray-300 p-2 w-20"></th>
        <th
            v-for="day in days"
            :key="day"
            class="border border-gray-300 p-2 text-center font-semibold text-gray-700"
        >
          {{ formatMobile($t(day)) }}
        </th>
      </tr>
      </thead>
      <tbody class="relative">
      <div class="absolute w-full" style="height: 1px; background: red" :style="offset <= 100 ? 'top: '+offset+'%' : 'display: none'"></div>
      <tr
          v-for="hour in hours"
          :key="hour"
          class="hover:bg-gray-50 transition-colors"
      >
        <td v-if="!isMobile"
            class="border border-gray-300 p-2 text-center font-medium text-sm"
            :style="colors.secondary_color ? 'background-color: ' + colors.secondary_color + ';' : 'background-color: var(--color-gray-50);'">
          {{ formatHour(hour) }}
        </td>
        <td
            v-for="day in days"
            :key="day"
            class="border border-gray-300 p-2 align-top h-16 relative"
            :style="colors.background_color ? 'background-color: ' + colors.background_color + ';' : ''"
        >
          <div
              class="space-y-1"
              v-if="schedule[day][hour] && schedule[day][hour].length"
          >
            <div
                class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded shadow-sm"
                :style="event.color ? 'background-color: ' + event.color + ';' : ''"
                v-for="event in schedule[day][hour]"
                :key="event.id"
                @click = "eventStore.openEditModal(event)"
            >
              {{ formatMobile(event.name) }}
            </div>
          </div>
          <div v-else class="h-full">

          </div>
          <span v-if="isMobile && day == days[0]" class="absolute bottom-0">{{ formatHour(hour) }}</span>
        </td>
      </tr>
      </tbody>
    </table>

  </div>
</div>
</template>

<script setup lang="ts">
import { useScheduleStore } from "~/stores/schedule";
import {computed} from "vue";
import {useModalStore} from "~/stores/modal";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import {useUserStore} from "~/stores/user";
import {useEventStore} from "~/stores/event";

const modalStore = useModalStore()
const userStore = useUserStore()
const scheduleStore = useScheduleStore()
const eventStore = useEventStore()

const schedule = computed(() => scheduleStore.schedule)
const colors = computed(() => scheduleStore.colors)
const days = computed(() => Object.keys(scheduleStore.schedule))
const hours = computed(() => {
  const min = scheduleStore.minHour
  const max = scheduleStore.maxHour
  return Array.from({ length: max - min + 1 }, (_, i) => i + min)
})

function datesChanged(){
  scheduleStore.getSchedule(null, dateRange.value[0], dateRange.value[1]);
}

async function sendData(){
  modalStore.isVisible = true
  modalStore.modalType = 'addEvent'
}

const dateRange = ref();

function formatDate(date) {
  return date.toISOString().split('T')[0]
}

function formatHour(hour: number) {
  return (hour.toString().length == 1 ? '0' + hour : hour) + ':00'
}

function formatMobile(text: string) {
  if(!isMobile)
    return text

  return text.length <= 3 ? text : text.substring(0, 3) + "."
}

const offset = ref(0);

onMounted(async () => {
  const today = new Date()

  const hour = new Date().getHours();
  const min = new Date().getMinutes();

  const max = scheduleStore.maxHour - scheduleStore.minHour

  let now = (hour + (min / 60)) - scheduleStore.minHour
  offset.value = (now * 100) / max

  const day = today.getDay()
  const diffToMonday = today.getDate() - day + (day === 0 ? -6 : 1)
  const diffToSunday = today.getDate() - day + (day === 0 ? 0 : 7)

  const monday = new Date(today)
  monday.setDate(diffToMonday)
  const sunday = new Date(today)
  sunday.setDate(diffToSunday)
  dateRange.value = [formatDate(monday), formatDate(sunday)];

  await userStore.getCurrentUser()
  const id = userStore.user?.default_schedule?.id ?? null

  scheduleStore.getSchedule(id, dateRange.value[0], dateRange.value[1]);

  setInterval(() => {
    const hour = new Date().getHours();
    const min = new Date().getMinutes();

    const max = scheduleStore.maxHour - scheduleStore.minHour

    let now = (hour + (min / 60)) - scheduleStore.minHour
    offset.value = (now * 100) / max
  }, 60000)
})

const isMobile = useIsMobile()
</script>

<style scoped>

</style>