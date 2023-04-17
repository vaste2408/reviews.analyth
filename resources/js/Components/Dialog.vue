<template>
    <q-dialog v-model="show">
        <q-card>
            <q-card-section class="text-center">
                {{show}}
                <q-card-title>{{ title }}</q-card-title>
            </q-card-section>

            <q-card-section>
                {{ message }}
            </q-card-section>

            <q-card-actions align="right">
                <q-btn label="Cancel" color="primary" @click="close" />
                <q-btn label="OK" color="positive" @click="confirm" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script setup>
import { ref, defineEmits } from 'vue'

const props = {
    title: String,
    message: String
}

const emits = defineEmits(['close', 'confirm'])

const show = ref(false)

function open() {
    show.value = true
}

function close() {
    show.value = false
    emits('close')
}

function confirm() {
    show.value = false
    emits('confirm')
}
</script>

<style scoped>

</style>
