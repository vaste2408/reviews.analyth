<template>
    <div>
        <h5 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" v-html="title"></h5>
        <Chart
            :size="size"
            :data="data"
            :margin="margin"
            :direction="direction"
            :axis="axis"
        >
            <template #layers>
                <Pie
                    :dataKeys="labels"
                    :pie-style="{ padAngle: 0.01, colors: colors }" />
            </template>
        </Chart>
    </div>
</template>

<script setup>
import { Chart, Pie } from 'vue3-charts'

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    labels: {
        type: Array,
        required: true,
    },
    colors: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
        default: 'Pie Chart',
    },
    size: {
        type: Object,
        default: { width: 200, height: 200 }
    },
    margin: {
        type: Object,
        default: {
            left: 0,
            top: 20,
            right: 20,
            bottom: 0
        }
    },
    axis: {
        type: Object,
        default: {
            primary: {
                hide: true,
            },
            secondary: {
                hide: true,
            },
        }
    }
});

const direction = 'circular';
</script>
