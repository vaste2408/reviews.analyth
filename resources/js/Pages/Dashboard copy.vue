<script setup>
import { Head } from '@inertiajs/vue3';
import {onMounted, reactive, ref} from "vue";
import NavBar from "@/Components/NavBar.vue";
import PieChart from '@/Components/PieChart.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { exportFile, useQuasar } from 'quasar';

const props = defineProps({
    postamat: {
        type: Object,
        default: null
    },
    marketplace: {
        type: Object,
        default: null
    },
});

const reviews_table = ref(null);
const showImportDialog = ref(false);
const importResult = ref('');

const data = reactive({
    postamats: [], //data from server
    postamat_options: [], //options for select
    postamat: null,
    marketplaces: [], //data from server
    marketplace_options: [], //options for select
    marketplace: null,
    //https://quasar.dev/vue-components/table
    reviews: [],
    reviews_loading: true,
    reviews_search: '',
    reviews_columns: [
        {name: 'number', required: false, label: 'Номер', align: 'left', field: row => row.id, format: val => `${val}`, sortable: true},
        {name: 'created', required: false, label: 'Создано', align: 'left', field: row => row.created_at, format: val => `${val}`, sortable: true},
        {name: 'source', required: false, label: 'Источник', align: 'left', field: row => row.source?.name, format: val => `${val}`, sortable: true},
        {name: 'category', required: false, label: 'Категория', align: 'left', field: row => row.theme?.name, format: val => `${val}`, sortable: true},
        {name: 'theme', required: false, label: 'Тематика', align: 'left', field: row => row.thematic?.name, format: val => `${val}`, sortable: true},
        {name: 'fio', required: false, label: 'ФИО', align: 'left', field: row => row.user_fio, format: val => `${val}`, sortable: true},
        {name: 'phone', required: false, label: 'Телефон', align: 'left', field: row => row.user_phone, format: val => `${val}`, sortable: true},
        {name: 'text', required: false, label: 'Текст', align: 'left', field: row => row.text, format: val => `${val}`},
        {name: 'score', required: true, label: 'Оценка', align: 'left', field: row => row.score, format: val => `${val}`, sortable: true},
        {name: 'marketplace', required: false, label: 'Маркетплейс', align: 'left', field: row => row.marketplace?.name, format: val => `${val}`, sortable: true},
        {name: 'postamat', required: false, label: 'Постамат', align: 'left', field: row => row.postamat?.name, format: val => `${val}`, sortable: true},
        {name: 'address', required: false, label: 'Адрес', align: 'left', field: row => row.postamat?.address, format: val => `${val}`, sortable: true},
        {name: 'confirmed', required: true, label: 'Одобрено', align: 'left', field: row => row.confirmed, format: val => `${val}`},
        {name: 'reaction', required: true, label: 'Требует устранения', align: 'left', field: row => row.need_reaction, format: val => `${val}`},
        {name: 'closed', required: true, label: 'Устранено', align: 'left', field: row => row.closed, format: val => `${val}`},
        {name: 'emotion', required: false, label: 'Характер', align: 'left', field: row => row.emotion?.name, format: val => `${val}`, sortable: true},
        {name: 'buttons', required: false, label: '', align: 'right', field: row => row.id, sortable: false},
    ],
    reviews_pagination: {
        sortBy: 'number',
        descending: true,
        page: 1,
        rowsPerPage: 15,
    },

});

function postamatsFilter (val, update, abort) {
    update(() => {
        const needle = val.toLowerCase()
        data.postamat_options = data.postamats.filter(v => v.name.toLowerCase().indexOf(needle) > -1)
    })
}

function marketplacesFilter (val, update, abort) {
    update(() => {
        const needle = val.toLowerCase()
        data.marketplace_options = data.marketplaces.filter(v => v.name.toLowerCase().indexOf(needle) > -1)
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

function loadMarketplaces() {
    fetch(`/api/marketplaces`).then(r => {
        r.json().then(val => {
            data.marketplaces = val;
            data.marketplace_options = val;
        });
    });
}

function loadReviews() {
    data.reviews_loading = true;
    fetch(data.postamat ? route('api.postamat.reviews', data.postamat)
        : (data.marketplace ? route('api.marketplace.reviews', data.marketplace)
            : route('api.reviews'))).then(r => {
        r.json().then(answer => {
            data.reviews = answer;
            data.reviews_loading = false;
        });
    }).catch(error => {
        alert('Что-то пошло не так');
        console.log(error);
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
        alert('Что-то пошло не так');
        review.confirmed = false;
        console.log(error);
    });
}

function makeProblem(review) {
    review.need_reaction = true;
    axios.post(route('api.reviews.update', review), {
        need_reaction: review.need_reaction,
    })
    .then(function (response) {
    })
    .catch(function (error) {
        alert('Что-то пошло не так');
        review.need_reaction = false;
        console.log(error);
    });
}

function closeProblem(review) {
    review.closed = true;
    axios.post(route('api.reviews.update', review), {
        closed: review.closed,
    })
    .then(function (response) {
    })
    .catch(function (error) {
        alert('Что-то пошло не так');
        review.closed = false;
        console.log(error);
    });
}

function analythReview(review) {
    axios.post(route('api.review.process', review), {})
    .then(function (response) {
        loadReviews();
    })
    .catch(function (error) {
        alert('Что-то пошло не так');
        console.log(error);
    });
}

function exportXLS () {
    axios({
        url: data.postamat ? route('api.excel.dashboard.postamat', data.postamat)
            : (data.marketplace ? route('api.excel.dashboard.marketplace', data.marketplace)
                : route('api.excel.dashboard')
            ),
        data: {
            postamat: data.postamat,
            marketplace: data.marketplace,
            reviews: reviews_table.value.filteredSortedRows,
        },
        method: 'POST',
        responseType: 'blob',
    }).then((response) => {
        let fileName = response.headers["content-disposition"].split("filename=")[1];
        const href = URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href = href;
        link.setAttribute('download', fileName);
        link.click();
        URL.revokeObjectURL(href);
    }).catch(error => {
        alert('Что-то пошло не так');
        console.log(error);
    });
}

function exportTable () {
    // naive encoding to csv format
    const content = [data.reviews_columns.map(col => wrapCsvValue(col.label))].concat(
        reviews_table.value.filteredSortedRows.map(row => data.reviews_columns.map(col => wrapCsvValue(
        typeof col.field === 'function'
            ? col.field(row)
            : row[ col.field === void 0 ? col.name : col.field ],
        col.format,
        row
        )).join(','))
    ).join('\r\n')

    const status = exportFile(
        'table-export.csv',
        "\ufeff"+content,
        'text/csv'
    )

    if (status !== true) {
        $q.notify({
        message: 'Browser denied file download...',
        color: 'negative',
        icon: 'warning'
        })
    }
}

function wrapCsvValue (val, formatFn, row) {
  let formatted = formatFn !== void 0
    ? formatFn(val, row)
    : val;

  formatted = formatted === void 0 || formatted === null
    ? ''
    : String(formatted);

  formatted = formatted.split('"').join('""')
   .split('\n').join('\\n')
   .split('\r').join('\\r');

  return `"${formatted}"`;
}

function openImportDialog() {
    importResult.value = '';
    showImportDialog.value = true;
}

function processUploadedResult(result) {
    importResult.value = '';
    let _result = JSON.parse(result.xhr.response);
    if (_result.success) {
        importResult.value = `Всего строк: ${_result.total}. Успешно загружено: ${_result.total - _result.failed}`;
        loadReviews();
    }
    else {
        failedUploading();
        console.log(result);
    }
}

function processUploading() {
    importResult.value = 'Загрузка данных...';
}

function failedUploading() {
    importResult.value = 'Ошибка формата данных';
}

function rowBackground (row) {
    if (row.emotion_id === -1 && ! row.closed)
        return 'bg-red-4 text-white';
    if (row.closed)
        return 'bg-green-1';
    return '';
}

onMounted(async () => {
    data.postamat = props.postamat;
    loadPostamats();
    loadMarketplaces();
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
            <div class="filters__section w-full row sm:justify-start mb-4">
                <div class="col-xs-12 col-sm-6 col-md-2 me-4" v-show="!postamat">
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
                <div class="col-xs-12 col-sm-6 col-md-2" v-show="!marketplace">
                    <q-select v-model="data.marketplace" label="Маркетплейс" clearable outlined
                            :options="data.marketplace_options"
                            option-label="name"
                            option-value="id"
                            use-input fill-input input-debounce="0"
                            hide-selected
                            @filter="marketplacesFilter"
                            @update:model-value="loadReviews"
                    ></q-select>
                </div>
                <q-btn label="Импорт XLSX" class="ml-4" @click="openImportDialog"></q-btn>
                <!--КАРТОЧКА ИМПОРТА-->
                <q-dialog v-model="showImportDialog">
                    <q-card>
                        <q-card-section class="row items-center text-center bg-gray-100">
                            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Импорт xlsx</h2>
                            <q-space />
                            <q-btn icon="close" flat round dense v-close-popup />
                        </q-card-section>
                        <q-card-section class="q-pt-none">
                            <q-uploader
                                url="/api/import"
                                max-files="1"
                                auto-upload
                                @uploaded="processUploadedResult"
                                @uploading="processUploading"
                                @failed="failedUploading"
                            />
                            {{ importResult }}
                        </q-card-section>
                    </q-card>
                </q-dialog>
            </div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight border-t pt-4 mt-4">Модерация отзывов</h2>

            <div class="w-full mt-4">
                <q-table
                    ref="reviews_table"
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
                        <q-btn class="ml-4" color="primary" label="Экспорт csv" @click="exportTable" icon-right="archive" no-caps />
                        <q-btn class="ml-4" color="primary" label="Экспорт xls" @click="exportXLS" icon-right="archive" no-caps />
                    </template>
                    <template v-slot:body="props">
                        <q-tr :props="props" :class="rowBackground(props.row)">
                            <q-td v-for="column in data.reviews_columns" :key="column.name" :props="props">
                                <template v-if="column.name === 'buttons'">
                                    <q-btn outline round color="green" size="sm" icon="check"
                                    :disabled="props.row.confirmed"
                                    class="q-mr-sm bg-white"
                                        @click="confirmReview(props.row)"
                                        title="Подтвердить" />
                                    <q-btn outline round color="primary" size="sm" icon="search"
                                        class="q-mr-sm bg-white"
                                        @click="analythReview(props.row)"
                                        title="Исследовать" />
                                    <q-btn outline round color="red" size="sm" icon="report_problem"
                                        :disabled="props.row.emotion_id==-1"
                                        class="q-mr-sm bg-white"
                                        @click="makeProblem(props.row)"
                                        title="Создать проблему" />
                                    <q-btn outline round color="green" size="sm" icon="report_problem"
                                        :disabled="props.row.closed"
                                        class="q-mr-sm bg-white"
                                        @click="closeProblem(props.row)"
                                        title="Проблема решена" />
                                </template>
                                <template v-else>
                                    {{column.field(props.row)}}
                                </template>
                            </q-td>
                        </q-tr>
                    </template>
                </q-table>
            </div>
            <div class="hidden">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight border-t pt-4 mt-4">Аналитика отзывов </h2>
                <div class="w-full mt-4 flex justify-evenly border-b mb-4 pb-4">
                    <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Общая оценка"/>
                    <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Работа курьеров"/>
                    <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Удобство расположения"/>
                    <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Скорость доставки"/>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">О партнёрах</h2>
                <div class="w-full mt-4 flex justify-evenly mb-4">
                    <PieChart :data="chart_data" :labels="labels" :colors="colors" title="OZON"/>
                    <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Яндекс.Маркет"/>
                    <PieChart :data="chart_data" :labels="labels" :colors="colors" title="Почта России"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
