<template>
    <q-dialog>
        <q-card class="w-3/4">
            <q-card-section>
                <q-form
                    @submit.prevent="onSubmitReviewForm"
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
                    <q-input name="user_fio" v-model="newReviewFio" label="ФИО" hint="Как Вас зовут?" lazy-rules
                             :rules="[ val => val && val.length > 0 || 'Пожалуйста, заполните поле']"
                    />
                    <q-input name="user_phone" v-model="newReviewPhone" label="Телефон" type="tel" hint="Как Вам позвонить?"
                            lazy-rules :rules="[ val => val && val.length > 0 && val !== '+7 (___) ___ - ____' || 'Пожалуйста, заполните поле']"
                            mask="+7 (###) ### - ####" fill-mask class="mt-4"
                    />
                    <q-input name="text" v-model="newReviewText" label="Комментарий" hint="Напишите плюсы и минусы, или опишите проблему"
                             type="textarea"
                             class="mt-4"
                             lazy-rules :rules="[ val => val && val.length > 0 || 'Пожалуйста, опишите достоинства/недостатки или опишите проблему']"
                    />
                    <p class="mt-4 mb-0">Возникла проблема? Выберите тему, чтобы нашим специалистам было проще разобраться и исправить ситуацию.</p>
                    <LoadableSelect
                        hint="Выберите категорию проблемы"
                        clearable emit-value map-options
                        :url="route('api.thematics')"
                        v-model="newReviewTheme"
                        label="Тема"></LoadableSelect>
                    <div class="mt-2">
                        <q-btn label="Сохранить" type="submit" color="primary"/>
                        <q-btn label="Отмена" type="reset" color="primary" flat class="q-ml-sm" />
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>

<script setup>
import {onMounted, ref} from "vue";
import {usePage} from '@inertiajs/vue3';
import LoadableSelect from '@/Components/LoadableSelect.vue';

const props = defineProps({
    postamat: {
        type: Object,
        required: true,
    },
});
const emit = defineEmits(['open', 'close', 'save']);
const newReviewTheme = ref(null);
const newReviewRating = ref(3);
const newReviewText = ref('');
const newReviewFio = ref('');
const newReviewPhone = ref('');
function onResetReviewForm() {
    newReviewTheme.value = null;
    newReviewRating.value = 3;
    newReviewText.value = '';
    newReviewFio.value = '';
    newReviewPhone.value = '';
    emit('close');
}
function onSubmitReviewForm() {
    let _data = {
        postamat_id: props.postamat.id,
        text: newReviewText.value,
        user_fio: newReviewFio.value,
        user_phone: newReviewPhone.value,
        score: newReviewRating.value,
    };
    if (newReviewTheme.value)
        _data.thematic_id = newReviewTheme.value;
    axios.post(route('api.reviews.create'), _data)
    .then(function (response) {
        if (response.data.success) {
            emit('save');
        }
    })
    .catch(function (error) {
        alert('Что-то пошло не так...');
        console.log(error);
    });
}
onMounted(async () => {
    newReviewFio.value = usePage().props.auth.user?.name;
    newReviewPhone.value = usePage().props.auth.user?.phone;
});
</script>

<style scoped>

</style>
