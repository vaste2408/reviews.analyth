<template>
    <q-select
        v-model="selected"
        :options="options"
        :loading="loading"
        :use-input="false"
        :hide-dropdown-icon="false"
        :hide-selected-icon="false"
        :hide-no-data="false"
        :no-options-label="'Нет пунктов'"
    />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    url: {
        type: String,
        required: true
    },
    value: {
        type: String,
        default: ''
    }
});

const selected = ref(props.value);
const options = ref([]);
const loading = ref(false);

const loadData = async () => {
    loading.value = true;

    try {
        const response = await axios.get(props.url);
        options.value = response.data;
    } catch (error) {
        console.error(error);
    }

    loading.value = false;
}

onMounted(() => {
    loadData();
})

</script>

<style scoped>

</style>
