<template>
<div class="w-full">
    <div class="row">
        <div class="col" v-for="day in days">
            <div class="w-20 h-20" v-for="hour in hours">
                {{hour}}
            </div>
        </div>
    </div>
</div>
</template>

<script setup lang="ts">
const days = [
    'mon', 'tue', 'wen', 'thu', 'fri'
]

const hours = [
    1, 2, 3, 4, 5, 6, 7
]
</script>

<style scoped>

</style>