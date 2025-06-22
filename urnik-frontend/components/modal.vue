<template>
  <div v-if="isVisible" class="absolute top-0 w-full vh-100 z-5">
    <slot class="z-10" />
  </div>
</template>

<script setup lang="ts">
import {useModalStore} from "~/stores/modal";

const modalStore = useModalStore()

const isVisible = computed(() => modalStore.isVisible)
</script>

<style scoped>

</style>