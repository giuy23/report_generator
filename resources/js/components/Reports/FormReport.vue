<script setup lang="ts">
import axios from 'axios';
import { ref, reactive } from 'vue';
// import { route } from 'ziggy-js';
import { IForm } from '@/Interfaces/FormInterfaces';
import { fetchData } from '@/composables/useGenerateReport'

const isLoading = ref(false);
const buttonText = ref('Enviar');


const getDateMonth = (dayInitial: boolean = false): string => {
    const month = Number(new Date().getMonth()) + 1;
    const year = new Date().getFullYear();

    const day = dayInitial === true ? '01'
        : new Date(year, month, 0).getDate();

    return `${year}-${String(month).padStart(2, "0")}-${day}`
}


const initialDates: IForm = {
    startDate: getDateMonth(true),
    endDate: getDateMonth()
};

const formDates: IForm = reactive({ ...initialDates });

const sendDatesReport = async () => {
    isLoading.value = true;
    buttonText.value = 'Exportando...';

    try {
        const { data } = await fetchData(formDates);

        const file = data.file;
        const exists = await waitForReport(file);

        if (!exists) {
            console.log('El reporte no se generó a tiempo');
            return;
        }

        const downloadUrl = route('report.download');
        window.location.href = `${downloadUrl}?file=${file}`;

        formDates.startDate = initialDates.startDate;
        formDates.endDate = initialDates.endDate;

    } catch (error) {
        console.error('Error al generar reporte', error);
    } finally {
        isLoading.value = false;
        buttonText.value = 'Enviar';
    }
}


const waitForReport = async (file: string): Promise<boolean> => {
    const attempts = 5
    const interval_time = 3000

    for (let attempt = 1; attempt <= attempts; attempt++) {

        await new Promise(resolve => setTimeout(resolve, interval_time))

        const { data } = await axios.post(route('report.verify'), {
            file
        })

        if (data.exists === true) {
            return true
        }
    }

    return false
}


</script>

<template>
    <section class="flex justify-center items-center h-dvh">
        <form @submit.prevent="sendDatesReport" class="flex flex-col sm:flex-row gap-3 items-end border border-solid border-indigo-500 p-10
         rounded-xl">
            <div>
                <label for="start_date" class="block text-xs text-gray-200 mb-1">
                    Fecha inicio
                </label>
                <input type="date" id="start_date" name="start_date" v-model="formDates.startDate"
                    class="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <div>
                <label for="end_date" class="block text-xs text-gray-200 mb-1">
                    Fecha fin
                </label>
                <input type="date" id="end_date" name="end_date" v-model="formDates.endDate"
                    class="rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>

            <button type="submit" :disabled="isLoading" class="px-4 py-2 text-sm rounded-md border border-gray-300
           hover:border-gray-400 transition disabled:opacity-50">
                {{ buttonText }}
            </button>
        </form>
    </section>
</template>


<style></style>
