<script setup lang="ts">
import {useI18n} from "vue-i18n";
import {useUserStore} from "~/stores/user";
import {useModalStore} from "~/stores/modal";

const { locale } = useI18n()
const userStore = useUserStore()
const modalStore = useModalStore()

const user = computed(() => userStore.user)
const showDropdown = ref(false)

async function loginModalOpen(){
  modalStore.isVisible = true
  modalStore.modalType = 'login'
}
</script>

<template>
  <div class="p-2 w-100 flex mb-2">
    <div class="ml-auto flex gap-4">
      <button class="flex gap-2" @click="locale = locale == 'sl' ? 'en' : 'sl'">
        <span>{{ $t('switch_locale') }}:</span>
        <div class="flex gap-1" v-if="locale == 'sl'">
          <span> Angleščina</span>
          <img :src="'/img/england.png'" alt="england" class="w-5 h-5 shrink-0" />
        </div>

        <div class="flex gap-1" v-if="locale == 'en'">
          <span> Slovenian</span>
          <img :src="'/img/slovenia.png'" alt="slovenia" class="w-5 h-5 shrink-0" />
        </div>
      </button>
      <button v-if="!user" @click="loginModalOpen">
        {{ $t('login') }}
      </button>
      <div v-else class="relative">
        <div class="flex gap-1">
          <span>
            {{ user.email }}
          </span>
          <button @click="showDropdown = !showDropdown"
              :style="showDropdown ? 'transform: rotate(180deg)' : ''"
          >
            <img :src="'/img/down.png'" alt="down-arrow" width="20" />
          </button>
        </div>
        <div v-if="showDropdown" class="absolute z-40 border-bottom-1 border w-100">
          <button class="w-100" @click="userStore.delete()">{{ $t('delete_account') }}</button>
          <button class="w-100" @click="userStore.logout()">{{ $t('logout') }}</button>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>

</style>