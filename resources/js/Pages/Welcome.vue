<script>

</script>
<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {loadYmap, YandexMap} from 'vue-yandex-maps';
import {onMounted, reactive, ref} from "vue";
import LoadableSelect from '@/components/LoadableSelect.vue'
import Dashboard from "@/Pages/Dashboard.vue";

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

const showDialog = ref(true);

const data = reactive({
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
        with_reviewes: null,
        type_reviewes: null,
    },
    current_postamat: null,
    showPostamatBalloon: false,
});

/**
 * АПИ загрузка постаматов. При успехе вызывает showFilteredOnMap
 */
function loadPostamats() {
    fetch(route('map.postamats')).then(r => {
        r.json().then(val => {
            data.postamats = val;
            showFilteredOnMap();
        });
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
 * Вывести объекты на карту
 */
function showFilteredOnMap() {
    objectManager.removeAll();
    setTimeout(() => {
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
    loadPostamats();
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
const newReviewRating = ref(3);
const newReviewText = ref('');
const newReviewFio = ref('');
function onResetReviewForm() {
    newReviewRating.value = 3;
    newReviewText.value = '';
    newReviewFio.value = '';
}

//форма аналитики
const showAnalythicDialog = ref(false);
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
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
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
                    >Log in</Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
            </template>
        </div>

        <div class="w-full">
            <div class="filters__section w-full sm:flex sm:justify-start px-2">
                <div class="w-1/6">
                    <q-field outlined label="Рейтинг" stack-label>
                        <template v-slot:control>
                            <div class="self-center full-width no-outline pl-1 pr-1" tabindex="0">
                                <q-range
                                    v-model="data.selected_filters.rating"
                                    :min="0"
                                    :max="5"
                                    :step="0.1"
                                    color="blue"
                                    label
                                />
                            </div>
                        </template>
                    </q-field>
                </div>
                <div class="w-1/6 ml-2">
                    <LoadableSelect url="/api/categories" v-model="data.selected_filters.category" label="Категория" clearable outlined></LoadableSelect>
                </div>
                <div class="w-1/6 ml-2">
                    <LoadableSelect url="/api/with_reviewes" v-model="data.selected_filters.with_reviewes" label="По наличию отзывов" clearable outlined></LoadableSelect>
                </div>
                <div class="w-1/6 ml-2">
                    <LoadableSelect url="/api/type_reviewes" v-model="data.selected_filters.type_reviewes" label="По характеру отзывов" clearable outlined></LoadableSelect>
                </div>
            </div>
            <div class="w-full mt-2">
                <YandexMap
                    :coordinates="data.center"
                    :zoom="data.zoom"
                    :settings="data.settings"
                    :region="data.bounds"
                    :events="data.events"
                    style="height: 600px"
                    @created="onMapCreated"
                    @boundschange="onBoundsChanged"
                    @sizechange="onBoundsChanged"
                    @balloonopen="onBalloonOpened"
                >
                </YandexMap>
                <p>count: {{data.postamats.length}}</p>
                <p>filtered: {{data.filtered.length}}</p>
                <p>current_postamat: {{data.current_postamat}}</p>
                <p>showDialog: {{data.showPostamatBalloon}}</p>
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
                    <template v-if="data.current_postamat.reviews.length">
                        <q-card-section class="q-pt-none">
                            <div class="flex no-wrap items-center">
                                <q-rating
                                    v-model="data.current_postamat.rating"
                                    :title="data.current_postamat.rating"
                                    readonly
                                    size="2em"
                                    color="orange-5"
                                    icon="star_border"
                                    icon-selected="star"
                                />
                                <span class="text-gray-600 q-ml-sm text-subtitle1">{{data.current_postamat.rating}} ({{ data.current_postamat.reviews.length }})</span>
                                <q-btn class="ml-4" label="Оставить отзыв" color="primary" outline :size="'md'" @click="showNewReviewDialog = true"/>
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-none">
                            <div class="text-h6">Люди пишут:</div>
                            <q-card>
                                <q-card-section>
                                    <q-rating
                                        v-model="data.current_postamat.reviews[0].score"
                                        :title="data.current_postamat.reviews[0].score"
                                        readonly
                                        size="1em"
                                        color="orange-5"
                                        icon="star_border"
                                        icon-selected="star"
                                    />
                                    <div class="text-subtitle1">{{data.current_postamat.reviews[0].user_fio}}</div>
                                    <div class="text-h6">{{data.current_postamat.reviews[0].text}}</div>
                                </q-card-section>
                            </q-card>
                        </q-card-section>
                        <q-card-section class="q-pt-none">
                            <div class="flex justify-center">
                                <a :href="route('postamat.info', data.current_postamat)" target="_blank" class="text-primary">Перейти к отзывам</a>
                            </div>
                        </q-card-section>
                        <q-card-section>
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
            <q-dialog v-model="showNewReviewDialog">
                <q-card class="w-3/4">
                    <q-card-section>
                        <q-form action="{{route('api.reviews.create')}}" method="post"
                                @reset="onResetReviewForm"
                        >
                            <div class="q-gutter-y-md column">
                                <q-rating
                                    v-model="newReviewRating"
                                    size="3em"
                                    color="orange-5"
                                    icon="star_border"
                                    icon-selected="star"
                                />
                            </div>
                            <q-input name="firstname" v-model="newReviewFio" label="ФИО" hint="Как Вас зовут?" lazy-rules
                                     :rules="[ val => val && val.length > 0 || 'Пожалуйста, заполните поле']"
                            />
                            <q-input name="firstname" v-model="newReviewText" label="Комментарий" hint="Напишите плюсы и минусы"
                                     type="textarea"
                                     lazy-rules :rules="[ val => val && val.length > 0 || 'Пожалуйста, напишите что-нибудь']"
                            />
                            <div class="mt-2">
                                <q-btn label="Сохранить" type="submit" color="primary"/>
                                <q-btn label="Отмена" type="reset" color="primary" flat class="q-ml-sm" />
                            </div>
                        </q-form>
                    </q-card-section>
                </q-card>
            </q-dialog>
            <!--КАРТОЧКА АНАЛИТИКИ-->
            <q-dialog v-model="showAnalythicDialog" full-width full-height>
                <q-card>
                    <q-card-section class="row items-center q-pb-none">
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
</style>
