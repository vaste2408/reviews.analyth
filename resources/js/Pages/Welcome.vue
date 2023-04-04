<script>

</script>
<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {loadYmap, YandexMap} from 'vue-yandex-maps';
import {onMounted, reactive} from "vue";
import LoadableSelect from '@/components/LoadableSelect.vue'

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
    settings: {
        apiKey: "",
        lang: "ru_RU", // Используемый язык
        coordorder: "latlong", // Порядок задания географических координат
        debug: false, // Режим отладки
        enterprise: false,
        center: [55.75, 37.57],
        version: "2.1", // Версия Я.Карт
    },
    events: ['click', 'created', 'boundschange', 'sizechange', 'geo-objects-updated'],
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
    }
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
 * формирует див со звёздами
 * @param rating
 * @param size
 * @returns {string}
 */
function htmlStars(rating, size = '1.5rem') {
    let _stars = '';
    for (let i = 1; i <=5; i++) {
        _stars +=
            `<i class="q-icon notranslate material-icons text-orange" style="font-size: ${size};" aria-hidden="true" role="presentation">
                ${rating > i && rating < i+1 ? 'star_half' : (rating < i ? 'star_outline' : 'star')}
                </i>`;
    }
    return `<div title="${rating}">${_stars}</div>`;
}

/**
 * Формирует блок отзывов
 * @param el
 * @returns {string}
 */
function reviewToHtml(el) {
    if (!el.reviews.length)
        return '';
    let _last = `<div class=" w-full flex">` +
        `<div class="w-full flex mt-2">Люди пишут:</div>` +
        `<div class="p-2 w-full q-card--bordered border-gray-100 shadow-box shadow-1 mb-1">` +
        `${htmlStars(el.reviews[0].score, '1rem')}` +
        `<p class="text-weight-bold m-0">${el.reviews[0].user_fio}</p>` +
        `<p class="m-0">${el.reviews[0].text}</p>` +
        `</div>` +
        `<a target="_blank" class="w-full text-primary text-center text-decoration-none" href="${route('postamat.dashboard', el.id)}">Посмотреть все отзывы</a>` +
        `</div>`;
    return `<div class="mt-2">${htmlStars(el.rating)}</div>`
        + `<div class="flex items-end ms-1 text-gray-800">Отзывов: ${el.reviews.length}</div>`
        + _last;
}

function formBalloonContentBody(el) {
    return `<div class="flex">` +
        `<span class="w-full text-gray-400">${el.address}</span>` +
        reviewToHtml(el) +
        `<div class="w-full mt-2"><hr/>` +
        `<h6 class="w-full mt-2 text-center">Анализ отзывов:</h6>` +
        `<p>Позитивных / Негативных: 45/94</p>` +
        `<h6 class="w-full mt-2 text-center">Анализ постамата:</h6>` +
        `<p>13 место из 1234 по рейтингу</p>` +
        `<h6 class="w-full mt-2 text-center">Индекс удовлетворённости:</h6>` +
        `<ul class="mb-0"></ul>` +
        `<li>По удобству расположения: 0.5</li>` +
        `<li>По работе курьеров: 0.5</li>` +
        `<li>По скорости доставки: 0.5</li>` +
        `<li>По сервису: 0.5</li>` +
        `<h6 class="w-full mt-2 text-center">По кампаниям-партнёрам:</h6>` +
        `<ul class="mb-0"></ul>` +
        `<li>Яндекс Маркет: 0.5</li>` +
        `<li>OZON: 0.5</li>` +
        `<li>Почта России: 0.5</li>` +
        `</div>` +
        `<a target="_blank" class="w-full text-primary text-center text-decoration-none mt-2" href="${route('postamat.dashboard', el.id)}">Подобрный анализ</a>` +
    `</div>`;
}

/**
 * Преобразование массива объектов в читаемый картой json
 * @param objects
 * @returns {{features: *[], type: string}}
 */
function objectsToJson(objects) {
    let _json = {type: "FeatureCollection", features: []};
    objects.forEach(el => {
        el.rating = el.rating ?? Math.random()*5;
        _json.features.push({
            //https://yandex.ru/dev/maps/jsapi/doc/2.1/ref/reference/GeoObject.html#GeoObject
            type: "Feature",
            id: el.id,
            geometry: {
                type: "Point",
                coordinates: [parseFloat(el.lat), parseFloat(el.lng)],
            },
            properties: {
                iconContent: '', //содержимое иконки (внутри кружка)
                balloonContentHeader: el.name,
                balloonContentBody: formBalloonContentBody(el),
                hintContent: `${el.name}`,
                clusterCaption: "<strong><s>Еще</s> одна</strong> метка",
            },
            options: {
                balloonAutoPan: false, //перемещать карту, чтобы вместить окно балуна
                preset: "islands#circleDotIcon",
                iconColor: defineColor(el.rating) //el.rating
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
 * Коллбэк клика на карту
 * @param payload
 */
function onMapClick(payload) {
    console.log(payload);
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

/**
 * Коллбэк изменения гео объектов на карте (пока не пригодился)
 * @param event
 */
function onGeoUpdated(event) {
    //alert(data.postamats.length);
}

/**
 * КОллбэк маунтед компоненты
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
                    @click="onMapClick"
                    @created="onMapCreated"
                    @boundschange="onBoundsChanged"
                    @sizechange="onBoundsChanged"
                    @geo-objects-updated="onGeoUpdated"
                >
                </YandexMap>
                <p>count: {{data.postamats.length}}</p>
                <p>filtered: {{data.filtered.length}}</p>
            </div>
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
