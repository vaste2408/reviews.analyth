<script setup>
import { Head } from '@inertiajs/vue3';
import {onMounted, reactive} from "vue";
import Review from "@/Pages/Reviews/Components/review.vue";

const props = defineProps({
    postamat: {
        type: Object,
        default: null
    },
});

const data = reactive({
    slide: 'review0',
    postamat: null,
    reviews: [],
    reviews_loading: false,
});

function loadReviews() {
    data.reviews_loading = true;
    fetch(data.postamat ? route('api.postamat.reviews', data.postamat.id) : route('api.reviews')).then(r => {
        r.json().then(val => {
            data.reviews = val;
            data.reviews_loading = false;
        });
    });
}

onMounted(async () => {
    data.postamat = props.postamat;
    loadReviews();
});
</script>

<template>
    <Head title="Postamat Card" />

    <div class="row q-pa-md justify-center bg-gray-100">
        <div class="col-8 row bg-white">
            <div class="col-8 q-ma-md">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" v-if="props.postamat">{{props.postamat.name}}</h2>
                <div>
                    <q-carousel
                        v-model="data.slide"
                        vertical
                        swipeable
                        animated
                        :arrows="true"
                        :navigation="false"
                        height="230px"
                        class="bg-white rounded-borders"
                        control-color="black"
                    >
                        <q-carousel-slide v-for="(review, ind) in data.reviews" :key="review.id" :name="'review'+ind" class="column no-wrap flex-center">
                            <div class="text-left w-full">
                                <Review :review="review" />
                            </div>
                        </q-carousel-slide>
                    </q-carousel>
                </div>
            </div>
            <div class="col-3 q-ma-md">
                <div class="q-card page-all__card bg-white shadow-bottom-large overflow-hidden letter-spacing-300">
                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">Город</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.city}}</div>

                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">Регион</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.region}}</div>

                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">Адрес</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.address}}</div>

                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">Метро</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.metro}}</div>

                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">График работы</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.scheduleTime}}</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.schedule}}</div>

                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">Способы оплаты</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.payType}}</div>
                </div>

                <div class="q-mt-md q-card page-all__card bg-white shadow-bottom-large overflow-hidden letter-spacing-300">
                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">Максимальные габариты упаковки</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.maxDimensions}} ({{props.postamat.maxBoxSize}})</div>

                    <div class="q-card__section q-card__section--vert text-brand-primary text-weight-bold">Максимальный допустимый вес</div>
                    <div class="q-card__section q-card__section--vert page-all__card-description text-dark q-pt-none">{{props.postamat.maxWeight}}</div>
                </div>
            </div>
        </div>
    </div>
</template>
