<script setup lang="ts">
import {useFacultyStore} from "~/stores/faculty";
import {useEventStore} from "~/stores/event";
import {ref} from "vue";

const props = defineProps({
  events: {
    type: Array,
    required: true
  },
})

const facultyStore = useFacultyStore()
const eventStore = useEventStore()
const faculties = computed(() => facultyStore.faculties)

const filter = ref({
  faculty_id: null,
  search: null,
  current_page: 1,
  per_page: 3
})

async function handleInput(){
  filter.value.current_page = 1
  await eventStore.paginate(filter.value)
}

async function handlePageChange(i: number){
  filter.value.current_page += i
  await eventStore.paginate(filter.value)
}

function addEvent(selection: object){
  props.events.push({ ...selection })
}

onMounted(async () => {
  await eventStore.paginate(filter.value)
})
</script>

<template>
<div class="flex flex-col gap-2">
  <div class="flex flex-row gap-4">
    <div class="flex flex-col">
      <label>{{ $t('search') }}</label>
      <input @input="handleInput" type="text" v-model="filter.search" />
    </div>
    <div class="flex flex-col">
      <label>{{ $t('faculty') }}</label>
      <select @change="handleInput" class="border" v-model="filter.faculty_id">
        <option value="">{{ $t('select_faculty') }}</option>
        <option v-for="fac in faculties" :key="fac.id" :value="fac.id">
          {{ fac.name }}
        </option>
      </select>
    </div>
  </div>
  <div class="w-100 flex flex-col justify-content-center">
    <div class="w-100 h-30 overflow-auto flex flex-col gap-2">
      <div v-for="event in eventStore.search_events">
        <div class="flex">
          <span>{{event.name}}</span>
          <div class="ml-auto">
            <button class="btn-primary" @click="addEvent(event)">{{ $t('add') }}</button>
          </div>
        </div>
      </div>
    </div>
    <div class="flex gap-4 mx-auto">
      <button @click="handlePageChange(-1)" :disabled="filter.current_page == 1" ><</button>
      <span>{{ filter.current_page }} </span>
      <button @click="handlePageChange(1)" :disabled="eventStore.last_page == filter.current_page" >></button>
    </div>
  </div>
</div>
</template>

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