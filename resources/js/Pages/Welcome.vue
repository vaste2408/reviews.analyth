<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {YandexMap, YandexObjectManager, loadYmap} from 'vue-yandex-maps';
import {onMounted, reactive} from "vue";

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
        debug: true, // Режим отладки
        enterprise: false,
        center: [55.75, 37.57],
        version: "2.1", // Версия Я.Карт
    },
    events: ['click', 'created', 'boundschange', 'sizechange', 'geo-objects-updated'],
    bounds: null,
    center: [55.75, 37.57],
    zoom: 11,
    postamats: [],
    action: ''
});

function loadPostamats() {
    fetch(`/api/postamats`).then(r => {
        r.json().then(val => {
            data.postamats = val;
            showFilteredOnMap();
        });
    });
}

function showFilteredOnMap() {
    let _filtered = [...data.postamats];
    let _json = {type: "FeatureCollection", features: []};
    _filtered.forEach(el => {
        _json.features.push({
            type: "Feature",
            id: el.id,
            geometry: {
                type: "Point",
                coordinates: [parseFloat(el.lat), parseFloat(el.lng)],
            },
            properties: {
                balloonContentHeader: el.name,
                balloonContentBody: `${el.address}`,
                hintContent: `${el.name}`,
                clusterCaption: "<strong><s>Еще</s> одна</strong> метка",
            }
        });
    });
    objectManager.add(_json);
}

function onMapClick(payload) {
    console.log(payload);
}

function onMapCreated(map_obj) {
    map = map_obj;
    data.bounds = map.getBounds();
    let om = new ymaps.ObjectManager({});
    map.geoObjects.add(om);
    objectManager = om;
    loadPostamats();
}

function onBoundsChanged(event) {
    data.bounds = event.originalEvent.newBounds;
    data.center = event.originalEvent.newCenter;
    data.zoom = event.originalEvent.newZoom;
    //showFilteredOnMap();
}

function onGeoUpdated(event) {
    //alert(data.postamats.length);
}

onMounted(async () => {
    await loadYmap(data.settings);
});
</script>

<template>
    <Head title="Main" />

    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
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
        <div class="w-full ">
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
            <p>action: {{data.action}}</p>
            <p>center: {{data.center}}</p>
            <p>zoom: {{data.zoom}}</p>
            <p>bounds: {{data.bounds}}</p>
            <p>count: {{data.postamats.length}}</p>
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
