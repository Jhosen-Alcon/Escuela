<template>
    <Head title="Dashboard" />
  
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
        <div class="row">
            <div class="col-md-4 grid-margin">
                <div class="card bg-info d-flex align-items-center">
                    <div class="card-body">
                        <div class="d-flex flex-row align-items-center">
                            <i class="mdi mdi-book menu-icon text-white icon-md pr-2"></i>
                            <div class="ms-3">
                                <h6 class="text-white">{{ cantidad_cursos }} cursos</h6>
                                <p class="mt-2 text-white card-text">Cantidad de cursos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin">
                <div class="card bg-facebook d-flex align-items-center">
                    <div class="card-body">
                        <div class="d-flex flex-row align-items-center">
                            <i class="mdi mdi-check-all text-white icon-md pr-2"></i>
                            <div class="ms-3">
                                <h6 class="text-white">{{ total_asistencia }} asistencia</h6>
                                <p class="mt-2 text-white card-text">Total asistencia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin">
                <div class="card bg-warning d-flex align-items-center">
                    <div class="card-body">
                        <div class="d-flex flex-row align-items-center">
                            <i class="mdi mdi-alert-circle text-white icon-md pr-2"></i>
                            <div class="ms-3">
                                <h6 class="text-white">{{ total_faltas }} faltas</h6>
                                <p class="mt-2 text-white card-text">Total faltas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="array_labels">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Gráfico de asistencias y faltas</div>
                        <LineChart v-if="testData" :chartData="testData" style="height: 400px;" />
                    </div>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { LineChart } from 'vue-chart-3';
    import { Chart, registerables } from "chart.js";
    import { onMounted, ref } from 'vue';

    const cantidad_cursos = ref('0');
    const total_asistencia = ref('0');
    const total_faltas = ref('0');
    const array_labels = ref([]);
    const array_dataset = ref([]);
    const testData = ref({});

    const fetchCursosByEstudiante = async () => {
        const response = await axios.get(`/dashboard/fetch`);
        cantidad_cursos.value = response.data.cantidad_cursos;
        total_asistencia.value = response.data.total_asistencia;
        total_faltas.value = response.data.total_faltas;
        array_labels.value = response.data.array_labels;
        array_dataset.value = response.data.array_dataset;
    }

    onMounted(async () => {
        try {
            await fetchCursosByEstudiante();
            Chart.register(...registerables);
    
            testData.value = {
                labels: array_labels,
                datasets: array_dataset,
            };
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
        }
    });

</script>
  