<template>
  <div v-if="isVisible" class="fixed top-0 left-0 w-full h-screen z-50 flex items-center justify-center">
    <slot />
  </div>
</template>

<script setup lang="ts">
import {useModalStore} from "~/stores/modal";

const modalStore = useModalStore()

const isVisible = computed(() => modalStore.isVisible)
</script>