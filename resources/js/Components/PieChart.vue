<template>
    <div>
        <h5 class="text-gray-600 dark:text-gray-200 leading-tight text-center" v-html="title"></h5>
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
                    :pie-style="{ padAngle: 0, colors: colors, innerRadius: 80, }" />
            </template>
        </Chart>
    </div>
</template>

<script setup>
//https://vue3charts.org/docs/charts/pie
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
        default: { width: 250, height: 250 }
    },
    margin: {
        type: Object,
        default: {
            left: 20,
            top: 20,
            right: 20,
            bottom: 20
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
    },
});

const direction = 'circular';
</script>
