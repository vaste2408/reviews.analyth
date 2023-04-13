<template>
    <q-card>
        <q-card-section>
            <q-rating
                v-model="props.review.score"
                :title="props.review.score"
                readonly
                size="1em"
                color="orange-5"
                icon="star_border"
                icon-selected="star"
            />
            <div class="text-subtitle1">{{props.review.user_fio}}</div>
            <div class="text-h6">{{props.review.text}}</div>
        </q-card-section>
    </q-card>
</template>

<script setup>
const props = defineProps({
    review: {
        type: Object,
        required: true
    },
});
</script>

<style scoped>

</style>
