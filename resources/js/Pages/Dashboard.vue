<script setup>
import { Head } from '@inertiajs/vue3';
import {onMounted, reactive} from "vue";

const data = reactive({
    postamats: [], //data from server
    postamat_options: [], //options for select
    postamat: null, //current
    //https://quasar.dev/vue-components/table
    reviews: [],
    filter: '',
    reviews_columns: [
        {name: 'postamat', required: false, label: 'Постамат', align: 'left', field: row => row.postamat.name, format: val => `${val}`, sortable: true},
        {name: 'address', required: false, label: 'Адрес', align: 'left', field: row => row.postamat.address, format: val => `${val}`, sortable: true},
        {name: 'score', required: true, label: 'Оценка', align: 'left', field: row => row.postamat.score, format: val => `${val}`, sortable: true},
        {name: 'text', required: false, label: 'Текст', align: 'left', field: row => row.text, format: val => `${val}`},
        {name: 'emotion', required: false, label: 'Характер', align: 'left', field: row => row.emotion, format: val => `${val}`, sortable: true},
        {name: 'about', required: false, label: 'Категория', align: 'left', field: row => row.about, format: val => `${val}`, sortable: true},
        {name: 'influ_cat', required: false, label: 'Влияние на категорию', align: 'left', field: row => row.influ_cat, format: val => `${val}`, sortable: true},
        {name: 'influ_post', required: false, label: 'Влияние на постамат', align: 'left', field: row => row.influ_post, format: val => `${val}`, sortable: true},
    ],
    initialPagination: {
        sortBy: 'postamat',
        descending: false,
        page: 1,
        rowsPerPage: 10
        // rowsNumber: xx if getting data from a server
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
    fetch(`/api/reviews`).then(r => {
        r.json().then(val => {
            data.reviews = val;
        });
    });
}

onMounted(async () => {
    loadPostamats();
    loadReviews();
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="w-full p-5">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Аналитика отзывов</h2>
        <div class="filters__section w-full sm:flex sm:justify-start mt-2">
            <div class="w-1/6">
                <q-select v-model="data.postamat" label="Постамат" clearable
                          :options="data.postamat_options"
                          option-label="name"
                          option-value="id"
                          use-input
                          hide-selected
                          fill-input
                          input-debounce="0"
                          @filter="postamatsFilter"
                ></q-select>
                {{data.postamat}}
            </div>
        </div>
        <div class="w-full mt-2">
            <q-table
                flat bordered
                title="Отзывы"
                no-data-label="Нет отзывов"
                row-key="id"
                :rows="data.reviews"
                :columns="data.reviews_columns"
                :pagination="data.initialPagination"
                :filter="data.filter"
            >
                <template v-slot:top-right>
                    <q-input borderless dense debounce="300" v-model="data.filter" placeholder="Поиск">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
            </q-table>
            {{data.reviews}}
        </div>
    </div>
</template>
