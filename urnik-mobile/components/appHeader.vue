<template>
  <div>
    <button @click="sidebarOpen = true" class="p-2">
      <img :src="'/img/menus.png'" alt="menu" width="30" />
    </button>

    <div
        v-if="sidebarOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-30"
        @click="sidebarOpen = false"
    ></div>

    <div
        class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-40 transform transition-transform duration-300"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="p-4 flex flex-col gap-4">
        <div class="flex flex-row align-center mb-4">
          <NuxtLink v-if="!user" to="/" class="w-fit text-decoration-none font-bold"
                    style="color: sandybrown"
                    @click="sidebarOpen = false">
            {{ $t('home') }}
          </NuxtLink>
          <button @click="sidebarOpen = false" style="margin-left: auto">
            ✕
          </button>
        </div>

        <button class="flex gap-2 items-center" @click="locale = locale == 'sl' ? 'en' : 'sl'">
          <span>{{ $t('switch_locale') }}:</span>
          <div class="flex gap-1" v-if="locale == 'sl'">
            <span> Angleščina</span>
            <img src="/img/england.png" alt="england" class="w-5 h-5 shrink-0" />
          </div>
          <div class="flex gap-1" v-if="locale == 'en'">
            <span> Slovenian</span>
            <img src="/img/slovenia.png" alt="slovenia" class="w-5 h-5 shrink-0" />
          </div>
        </button>

        <NuxtLink v-if="!user" to="/login" class="btn-primary" @click="sidebarOpen = false">
          {{ $t('login') }}
        </NuxtLink>

        <div v-else class="relative flex flex-col gap-2">
          <div class="flex gap-1 items-center">
            <span>{{ user.email }}</span>
            <button @click="showDropdown = !showDropdown"
                    :style="showDropdown ? 'transform: rotate(180deg)' : ''"
            >
              <img src="/img/down.png" alt="down-arrow" width="20" />
            </button>
          </div>

          <div v-if="showDropdown" class="flex flex-col gap-2 border-t pt-2">
            <button class="py-2 px-4 w-full text-left" @click="userStore.delete()">
              {{ $t('delete_account') }}
            </button>
            <button class="py-2 px-4 w-full text-left" @click="userStore.logout()">
              {{ $t('logout') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '@/stores/user' // adjust if using Pinia

const sidebarOpen = ref(false)
const showDropdown = ref(false)
const locale = ref('sl')
const userStore = useUserStore()
const user = userStore.user
</script>

<style scoped>
.btn-primary {
  background: sandybrown;
  padding: 5px;
  border-radius: 10px;
  color: white;
  text-decoration: none;
  text-align: center;
}
</style>
