<template>
  <div>
    <modal class="z-30">
      <add-event-modal v-if="modalStore.modalType == 'addEvent'" />
    </modal>
    <div class="absolute top-0 w-full h-full bg-black opacity-50 z-20" v-if="modalStore.isVisible">

    </div>
    <slot />
  </div>
</template>

<script setup lang="ts">
import Modal from '../components/modal.vue'
import AddEventModal from "~/components/modals/addEventModal.vue";
import {useModalStore} from "~/stores/modal";

const modalStore = useModalStore()
</script>

<style scoped>

</style>