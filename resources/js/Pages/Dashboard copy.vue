<script setup>
import { Head } from '@inertiajs/vue3';
import {onMounted, reactive} from "vue";
import NavBar from "@/Components/NavBar.vue";
import PieChart from '@/Components/PieChart.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    postamat: {
        type: Object,
        default: null
    },
});

const data = reactive({
    postamats: [], //data from server
    postamat_options: [], //options for select
    postamat: null,
    //https://quasar.dev/vue-components/table
    reviews: [],
    reviews_loading: true,
    reviews_search: '',
    reviews_columns: [
        {name: 'postamat', required: false, label: 'Постамат', align: 'left', field: row => row.postamat.name, format: val => `${val}`, sortable: true},
        {name: 'address', required: false, label: 'Адрес', align: 'left', field: row => row.postamat.address, format: val => `${val}`, sortable: true},
        {name: 'score', required: true, label: 'Оценка', align: 'left', field: row => row.score, format: val => `${val}`, sortable: true},
        {name: 'text', required: false, label: 'Текст', align: 'left', field: row => row.text, format: val => `${val}`},
        {name: 'confirmed', required: true, label: 'Подтверждено', align: 'left', field: row => row.confirmed, format: val => `${val}`},
        {name: 'emotion', required: false, label: 'Характер', align: 'left', field: row => row.emotion, format: val => `${val}`, sortable: true},
        {name: 'category', required: false, label: 'Категория', align: 'left', field: row => row.category, format: val => `${val}`, sortable: true},
        {name: 'influ_cat', required: false, label: 'Влияние на категорию', align: 'left', field: row => row.influ_cat, format: val => `${val}`, sortable: true},
        {name: 'influ_post', required: false, label: 'Влияние на постамат', align: 'left', field: row => row.influ_post, format: val => `${val}`, sortable: true},
        {name: 'buttons', required: false, label: '', align: 'right', field: row => row.id, sortable: false},
    ],
    reviews_pagination: {
        sortBy: 'postamat',
        descending: false,
        page: 1,
        rowsPerPage: 10,
    },

});

function postamatsFilter (val, update, abort) {
    update(() => {
        const needle = val.toLowerCase()
        data.postamat_options = data.postamats.filter(v => v.name.toLowerCase().indexOf(needle) > -1)
    })
}

function loadPostamats() {
    fetch(`/api/postamats`).then(r => {
        r.json().then(val => {
            data.postamats = val;
            data.postamat_options = val;
        });
    });
}

function loadReviews() {
    data.reviews_loading = true;
    fetch(data.postamat ? route('api.postamat.reviews', data.postamat.id) : route('api.reviews')).then(r => {
        r.json().then(val => {
            data.reviews = val;
            data.reviews_loading = false;
        });
    });
}

function confirmReview(review) {
    review.confirmed = true;
    axios.post(route('api.reviews.update', review), {
        confirmed: review.confirmed,
    })
    .then(function (response) {
    })
    .catch(function (error) {
        review.confirmed = false;
        console.log(error);
    });
}
function analythReview(review) {
    alert(review.id);
}

onMounted(async () => {
    data.postamat = props.postamat;
    loadPostamats();
    loadReviews();
});

const chart_data = [
    { label: 'Negative', value: 5 },
    { label: 'Neutral', value: 20 },
    { label: 'Positive', value: 75 },
];
const labels = ['label', 'value'];
const colors = ['red', '#ffc700', '#21BA45'];
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full p-8 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="filters__section w-full sm:flex sm:justify-start mb-4" v-show="!postamat">
                <div class="w-1/6">
                    <q-select v-model="data.postamat" label="Постамат" clearable outlined
                            :options="data.postamat_options"
                            option-label="name"
                            option-value="id"
                            use-input fill-input input-debounce="0"
                            hide-selected
                            @filter="postamatsFilter"
                            @update:model-value="loadReviews"
                    ></q-select>
                </div>
            </div>
            <h5>Аналитика отзывов</h5>
            <div class="w-full mt-4 flex justify-evenly border-b mb-4 pb-4">
                <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Общая оценка"/>
                <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Работа курьеров"/>
                <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Удобство расположения"/>
                <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Скорость доставки"/>
            </div>
            <h5>О партнёрах</h5>
            <div class="w-full mt-4 flex justify-evenly border-b mb-4 pb-4">
                <PieChart :data="chart_data" :labels="labels" :colors="colors" title="OZON"/>
                <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Яндекс.Маркет"/>
                <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Почта России"/>
            </div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Модерация отзывов</h2>
            <div class="w-full mt-4">
                <q-table
                    flat bordered
                    title="Отзывы"
                    no-data-label="Нет отзывов"
                    row-key="id"
                    :loading="data.reviews_loading"
                    :rows="data.reviews"
                    :columns="data.reviews_columns"
                    :pagination="data.reviews_pagination"
                    :rows-per-page-label="'Показывать по '"
                    :filter="data.reviews_search"
                >
                    <template v-slot:top-right>
                        <q-input borderless dense debounce="300" v-model="data.reviews_search" placeholder="Поиск">
                            <template v-slot:append>
                                <q-icon name="search" />
                            </template>
                        </q-input>
                    </template>
                    <template v-slot:body="props">
                        <q-tr :props="props">
                            <q-td v-for="column in data.reviews_columns" :key="column.name" :props="props">
                                <template v-if="column.name === 'buttons'">
                                    <q-btn outline round color="green" size="sm" icon="check"
                                        class="q-mr-sm"
                                        @click="confirmReview(props.row)"
                                        title="Подтвердить" />
                                    <q-btn outline round color="primary" size="sm" icon="search"
                                        class="q-mr-sm"
                                        @click="analythReview(props.row)"
                                        title="Исследовать" />
                                </template>
                                <template v-else>
                                    {{column.field(props.row)}}
                                </template>
                            </q-td>
                        </q-tr>
                    </template>
                </q-table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
