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
const import_disabled = ref(true)

const schedule_import = ref({
  faculty_id: null,
  file: null
})

function handleFileChanged(event) {
  schedule_import.value.file = event.target.files[0];
  import_disabled.value = false
}

async function importEvents() {
  let ev = await eventStore.parseFromFile(schedule_import.value)

  props.events.push(...ev)
}
</script>

<template>
<div class="flex flex-col gap-2">
  <label>{{ $t('faculty') }}</label>
  <!--        <input type="text" class="border" v-model="schedule_import.faculty_id" />-->
  <select class="border" v-model="schedule_import.faculty_id">
    <option value="">{{ $t('select_faculty') }}</option>
    <option v-for="fac in faculties" :key="fac.id" :value="fac.id">
      {{ fac.name }}
    </option>
  </select>

  <label>{{ $t('file') }}</label>
  <input type="file" @change="handleFileChanged"/>

  <button class="btn-primary w-25" :disabled="import_disabled" :class="import_disabled ? 'btn-disabled' : ''" @click="importEvents">
    {{ $t('import') }}
  </button>
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