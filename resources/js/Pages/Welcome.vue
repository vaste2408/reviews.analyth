<script>

</script>
<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {loadYmap, YandexMap} from 'vue-yandex-maps';
import {onMounted, reactive, ref} from "vue";
import LoadableSelect from '@/Components/LoadableSelect.vue';
import Dashboard from "@/Pages/Dashboard.vue";
import Review from "@/Pages/Reviews/Components/review.vue";
import CreateReview from "@/Pages/Reviews/Components/CreateReview.vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

let map = null;
let objectManager = [];

const data = reactive({
    loading: false,
    settings: { //map settings
        apiKey: "",
        lang: "ru_RU", // Используемый язык
        coordorder: "latlong", // Порядок задания географических координат
        debug: false, // Режим отладки
        enterprise: false,
        center: [55.75, 37.57],
        version: "2.1", // Версия Я.Карт
    },
    events: ['click', 'created', 'boundschange', 'sizechange', 'geo-objects-updated', 'balloonopen'],
    bounds: null,
    center: [55.75, 37.57],
    zoom: 11,
    postamats: [], //исходные объекты
    filtered: [], //отфильтрованные объекты
    selected_filters: {
        category: null,
        rating: {
            min: 0,
            max: 5
        },
        with_reviews: null,
        type_reviews: null,
    },
    current_postamat: null,
    showPostamatBalloon: false,
});

/**
 * АПИ загрузка постаматов. При успехе вызывает showFilteredOnMap
 */
function loadPostamats() {
    data.loading = true;
    axios.get(route('map.postamats'), {
        params: {
            bounds: data.bounds,
            center: data.center,
            zoom: data.zoom,
            rating_min: data.selected_filters.rating.min,
            rating_max: data.selected_filters.rating.max,
            category: data.selected_filters.category,
            type: data.selected_filters.type_reviews,
            with_reviews: data.selected_filters.with_reviews,
        }
    })
    .then(function (response) {
        data.loading = false;
        if (response.data) {
            data.postamats = response.data;
            showFilteredOnMap();
        }
    })
    .catch(function (error) {
        console.log(error);
    });
}

/**
 * Фильтрация объектов по вхождению в границы
 * @param objects
 * @returns {*[]}
 */
function filterByBoundsFunction(objects) {
    return [...objects].filter(el => {
        return parseFloat(el.lat) > parseFloat(data.bounds[0][0])
            && parseFloat(el.lat) < parseFloat(data.bounds[1][0])
            && parseFloat(el.lng) > parseFloat(data.bounds[0][1])
            && parseFloat(el.lng) < parseFloat(data.bounds[1][1]);
    });
}

/**
 * Определение цвета метки по её оценке
 * @param mark
 * @returns {string}
 */
function defineColor(mark) {
    if (mark > 4.5)
        return 'green';
    if (mark > 4)
        return 'lightgreen';
    if (mark > 3)
        return 'yellow';
    if (mark > 2)
        return 'orange';
    return 'red';
}

/**
 * Преобразование массива объектов в читаемый картой json
 * @param objects
 * @returns {{features: *[], type: string}}
 */
function objectsToJson(objects) {
    let _json = {type: "FeatureCollection", features: []};
    objects.forEach(el => {
        el.rating = el.rating ?? (Math.random()*5).toFixed(2);
        _json.features.push({
            //https://yandex.ru/dev/maps/jsapi/doc/2.1/ref/reference/GeoObject.html#GeoObject
            type: "Feature",
            id: el.id,
            postamat: el,
            geometry: {
                type: "Point",
                coordinates: [parseFloat(el.lat), parseFloat(el.lng)],
            },
            properties: {
                iconContent: '', //содержимое иконки (внутри кружка)
                balloonContentHeader: el.name,
                balloonContentBody: el,
                hintContent: `${el.name}`,
            },
            options: {
                balloonAutoPan: false, //перемещать карту, чтобы вместить окно балуна
                preset: "islands#circleDotIcon",
                iconColor: defineColor(el.rating), //el.rating
            },
        });
    });
    return _json;
}

/**
 * Вывести объекты на карту
 */
function showFilteredOnMap() {
    data.loading = true;
    objectManager.removeAll();
    setTimeout(() => {
        data.loading = false;
        data.filtered = filterByBoundsFunction([...data.postamats]);
        let _json = objectsToJson(data.filtered);
        objectManager.add(_json);
        }, 150);
}

/**
 * Событие при открытии баллуна на карте
 * @param evt
 */
function onBalloonOpened(evt) {
    let _target = evt.get('target');
    let _postamat = _target.balloon.getData().postamat;
    _target.balloon.close();
    data.current_postamat = _postamat;
    data.showPostamatBalloon = true;
}

/**
 * Коллбэк при инициализации карты
 * @param map_obj
 */
function onMapCreated(map_obj) {
    map = map_obj;
    data.bounds = map.getBounds();
    let om = new ymaps.ObjectManager({
        clusterize: false,
        gridSize: 32,
    });
    map.geoObjects.add(om);
    objectManager = om;
    setTimeout(() => {
        loadPostamats();
    }, 200);
}

/**
 * Коллбэк перемещения/зума на карте
 * @param event
 */
function onBoundsChanged(event) {
    data.bounds = event.originalEvent.newBounds;
    data.center = event.originalEvent.newCenter;
    data.zoom = event.originalEvent.newZoom;
    showFilteredOnMap();
}

function hidePostamatBalloon() {
    data.current_postamat = null;
    data.showPostamatBalloon = false;
}

//форма написания отзыва
const showNewReviewDialog = ref(false);
function onCloseReviewDialog() {
    showNewReviewDialog.value = false;
}
function onSaveReviewDialog() {
    onCloseReviewDialog();
    hidePostamatBalloon();
    hideAnalythicDialog();
    loadPostamats();
}

//форма аналитики
const showAnalythicDialog = ref(false);
function hideAnalythicDialog() {
    showAnalythicDialog.value = false;
}
/**
 * Коллбэк маунтед компоненты
 */
onMounted(async () => {
    await loadYmap(data.settings);
});
</script>

<template>
    <Head title="Main" />

    <div
        class="relative sm:flex sm:justify-center min-h-screen bg-center dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
    >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Dashboard</Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Войти</Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="hidden ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
            </template>
        </div>

        <div class="w-full q-mt-xl">
            <div class="filters__section w-full row sm:justify-start px-2">
                <div class="col-sm-2 col-xs-12 q-mr-sm">
                    <q-field outlined label="Рейтинг" stack-label>
                        <template v-slot:control>
                            <div class="self-center full-width no-outline pl-1 pr-1" tabindex="0">
                                <q-range
                                    :model-value="data.selected_filters.rating"
                                    @change="val => { data.selected_filters.rating = val; loadPostamats();}"
                                    :min="0"
                                    :max="5"
                                    :step="0.1"
                                    label
                                />
                            </div>
                        </template>
                    </q-field>
                </div>
                <div class="col-sm-2 col-xs-12 q-mr-sm">
                    <LoadableSelect
                        clearable outlined emit-value map-options
                        :url="route('api.with_reviews')"
                        v-model="data.selected_filters.with_reviews"
                        @update:model-value="loadPostamats"
                        label="По наличию отзывов"></LoadableSelect>
                </div>
                <div class="col-sm-2 col-xs-12 q-mr-sm">
                    <LoadableSelect
                        clearable outlined emit-value map-options
                        :url="route('api.categories')"
                        v-model="data.selected_filters.category"
                        @update:model-value="loadPostamats"
                        label="Категория отзывов"></LoadableSelect>
                </div>
                <div class="col-sm-2 col-xs-12">
                    <LoadableSelect
                        clearable outlined emit-value map-options
                        :url="route('api.type_reviews')"
                        v-model="data.selected_filters.type_reviews"
                        @update:model-value="loadPostamats"
                        label="По характеру отзывов"></LoadableSelect>
                </div>
            </div>
            <div class="w-full mt-2">
                <YandexMap
                    :coordinates="data.center"
                    :zoom="data.zoom"
                    :settings="data.settings"
                    :region="data.bounds"
                    :events="data.events"
                    style="height: 700px"
                    @created="onMapCreated"
                    @boundschange="onBoundsChanged"
                    @sizechange="onBoundsChanged"
                    @balloonopen="onBalloonOpened"
                >
                </YandexMap>
                <span v-if="data.loading">Загрузка данных...</span>
            </div>
            <!--КАРТОЧКА ПОСТАМАТА-->
            <q-dialog v-model="data.showPostamatBalloon"
                      @hide="hidePostamatBalloon"
            >
                <q-card class="my-card w-3/4">
                    <q-card-section>
                        <div class="text-h6">{{data.current_postamat.name}}</div>
                        <div class="text-subtitle2">{{data.current_postamat.address}}</div>
                    </q-card-section>
                    <q-card-section class="q-pt-none">
                        <div class="sm:flex no-wrap items-center">
                            <q-rating
                                v-model="data.current_postamat.rating"
                                :title="data.current_postamat.rating"
                                readonly
                                size="2em"
                                color="orange-5"
                                icon="star_border"
                                icon-selected="star"
                            />
                            <span class="text-gray-600 q-ml-sm text-subtitle1 mr-4">{{data.current_postamat.rating}} ({{ data.current_postamat.reviews.length }})</span>
                            <q-btn label="Оставить отзыв" color="primary" outline :size="'md'" @click="showNewReviewDialog = true"/>
                        </div>
                    </q-card-section>
                    <template v-if="data.current_postamat.reviews.length">
                        <q-card-section class="q-pt-none">
                            <div class="text-h6">Люди пишут:</div>
                            <Review :review="data.current_postamat.reviews[0]" />
                        </q-card-section>
                        <q-card-section class="q-pt-none">
                            <div class="flex justify-center">
                                <a :href="route('postamat.info', data.current_postamat)" target="_blank" class="text-primary">Перейти к отзывам</a>
                            </div>
                        </q-card-section>
                        <q-card-section v-if="$page.props.auth.user" class="hidden">
                            <q-btn label="Аналитика" @click="showAnalythicDialog=true"></q-btn>
                        </q-card-section>
                    </template>
                    <template v-else>
                        <q-card-section class="q-pt-none">
                            <div class="text-h6">Ещё не было отзывов</div>
                        </q-card-section>
                    </template>
                </q-card>
            </q-dialog>
            <!--КАРТОЧКА СОЗДАНИЯ ОТЗЫВА-->
            <CreateReview v-model="showNewReviewDialog" :postamat="data.current_postamat" @save="onSaveReviewDialog" @close="onCloseReviewDialog" />
            <!--КАРТОЧКА АНАЛИТИКИ-->
            <q-dialog v-model="showAnalythicDialog" full-width full-height>
                <q-card>
                    <q-card-section class="row items-center text-center bg-gray-100">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" v-if="data.current_postamat">{{data.current_postamat.name}}</h2>
                        <h2 class="text-xl text-gray-500 dark:text-gray-200 leading-tight ml-3" v-if="data.current_postamat">ID: {{data.current_postamat.id}}</h2>
                        <q-space />
                        <q-btn icon="close" flat round dense v-close-popup />
                    </q-card-section>
                    <q-card-section class="q-pt-none">
                        <Dashboard :postamat="data.current_postamat"></Dashboard>
                    </q-card-section>
                </q-card>
            </q-dialog>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
.q-field--labeled .q-field__native {
    padding-bottom: 0;
}
</style>
