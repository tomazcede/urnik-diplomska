<template>
  <div class="w-full h-full flex justify-center items-center">
    <div class="bg-white w-75 md:w-50 opacity-100 text-black p-2 md:p-4" style="height: fit-content">
      <div class="w-full flex mb-2">
        <div class="flex gap-4">
          <button v-for="tab_btn in tabs"
                  @click="tab = tab_btn.tab"
                  :disabled="tab == tab_btn.tab"
                  :style="tab == tab_btn.tab ? 'color: gray' : ''"
          >{{ $t(tab_btn.name) }}</button>
        </div>
        <div class="ml-auto">
          <span @click="modalStore.closeModal" class="cursor-pointer">x</span>
        </div>
      </div>

      <div v-if="tab == 'new'">
        <new-event-tab :event="event"/>
      </div>

      <div v-if="tab == 'import'">
        <import-events-tab :events="events"/>
      </div>

      <div v-if="tab == 'search'">
        <select-events-tab :events="events"/>
      </div>

      <div class="my-4 h-20 overflow-auto">
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
        <button v-if="tab == 'new'" :disabled="!checkValues" :class="!checkValues ? 'btn-disabled' : ''" class="btn-primary" @click="addToList">
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
import {useModalStore} from "~/stores/modal";
import NewEventTab from "~/components/modals/addEventModal/newEventTab.vue";
import ImportEventsTab from "~/components/modals/addEventModal/importEventsTab.vue";
import SelectEventsTab from "~/components/modals/addEventModal/selectEventsTab.vue";

const events = ref<any[]>([])
const scheduleStore = useScheduleStore()
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
  },
  {
    name: 'search_events',
    tab: 'search'
  }
]

const checkValues = computed(() => {
  return event.value.name != ''
})

function formatDate(date: Date): string {
  return date.toISOString().split('T')[0]
}

const today = formatDate(new Date())

const event = ref({
  name: '',
  from_hour: 7,
  to_hour: 8,
  location: '',
  start_date: today,
  end_date: '',
  day: 'mon',
  color: null
})

function addToList() {
  events.value.push({ ...event.value })
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
    end_date: '',
    color: null
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
