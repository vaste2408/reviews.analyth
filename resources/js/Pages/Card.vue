<script setup>
import { Head } from '@inertiajs/vue3';
import {onMounted, reactive, ref} from "vue";
import NavBar from "@/Components/NavBar.vue";
import Review from "@/Pages/Reviews/Components/review.vue";
import CreateReview from "@/Pages/Reviews/Components/CreateReview.vue";

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

//форма написания отзыва
const showNewReviewDialog = ref(false);
function onCloseReviewDialog() {
    showNewReviewDialog.value = false;
}
function onSaveReviewDialog() {
    onCloseReviewDialog();
    loadReviews();
}

onMounted(async () => {
    data.postamat = props.postamat;
    loadReviews();
});
</script>

<template>
    <Head title="Postamat Card" />
    <NavBar :links="[{route: 'welcome', name: 'Main'}]" />
    <div class="row q-pa-md justify-center bg-gray-100">
        <div class="col-8 row bg-white justify-between">
            <div class="col-8 q-ma-md q-pa-md">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" v-if="props.postamat">{{props.postamat.name}}</h2>
                <div class="flex no-wrap items-center q-mt-sm">
                    <q-rating
                        v-model="props.postamat.rating"
                        :title="props.postamat.rating"
                        readonly
                        size="2em"
                        color="orange-5"
                        icon="star_border"
                        icon-selected="star"
                    />
                    <span class="text-gray-600 q-ml-sm text-subtitle1">{{props.postamat.rating}} ({{ data.reviews.length }})</span>
                    <q-btn class="ml-4" label="Оставить отзыв" color="primary" outline :size="'md'" @click="showNewReviewDialog = true"/>
                </div>
                <div>
                    <q-carousel
                        v-model="data.slide"
                        vertical
                        swipeable
                        animated
                        transition-prev="slide-down"
                        transition-next="slide-up"
                        :arrows="true"
                        :navigation="false"
                        class="bg-white rounded-borders"
                        control-color="black"
                        height="auto"
                    >
                        <q-carousel-slide v-for="(review, ind) in data.reviews" :key="review.id" :name="'review'+ind" class="column no-wrap flex-center q-px-sm">
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
    <CreateReview v-model="showNewReviewDialog"
                  :postamat="data.postamat"
                  @save="onSaveReviewDialog"
                  @close="onCloseReviewDialog"
    />
</template>
