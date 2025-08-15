<script setup lang="ts">
const props = defineProps({
  event: {
    type: Object,
    required: true
  },
})

const days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']
</script>

<template>
  <div class="flex flex-col gap-2">
    <label>{{ $t('name') }}</label>
    <input type="text" class="border" v-model="event.name" />

    <label>{{ $t('from_hour') }}</label>
    <input type="number" class="border" v-model="event.from_hour" />

    <label>{{ $t('to_hour') }}</label>
    <input type="number" class="border" v-model="event.to_hour" />

    <label>{{ $t('location') }}</label>
    <input type="text" class="border" v-model="event.location" />

    <label>{{ $t('color') }}</label>
    <div class="flex gap-2 align-items-center">
      <input type="color" v-model="event.color" />
      <span>{{event.color}}</span>
      <button @click="event.color = null">{{ $t('reset') }}</button>
    </div>

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
</template>

<style scoped>

</style>