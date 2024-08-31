
<template>
    <Head title="Periodos" />
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-4">
                            <div class="col">
                                <h4 class="card-title">Cursos:</h4>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="myDataTable"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" v-show="isOpenModalNotas" :class="{ 'show': isOpenModalNotas }" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Notas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModalEstudiantes">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped" v-if="estudiante_notas">
                                <tbody>
                                    <tr>
                                        <th>Periodo</th>
                                        <th>Nota</th>
                                        <th>Asistencia</th>
                                    </tr>
                                    <tr v-for="(nota, index) in estudiante_notas.nota.notas" :key="index">
                                        <td>Periodo {{index + 1}}</td>
                                        <td>
                                            <input type="text" readonly class="form-control" v-model="estudiante_notas.nota.notas[index]"/>
                                        </td>
                                        <td>
                                            <span v-for="(row, index) in estudiante_notas.asistencias[index]">
                                                {{ row.tipo }} - {{ row.observacion }} |
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nota final</td>
                                        <td>{{ estudiante_notas.nota.nota_final }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModalEstudiantes">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="isOpenModalNotas" class="modal-backdrop fade show"></div>
    </AuthenticatedLayout>
</template>

<script>
    import { defineProps } from 'vue';
    defineProps({
            estudiante: {
                type: Object,
            },
    });
</script>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import $ from 'jquery';
    import "datatables.net-dt/js/dataTables.dataTables"
    import "datatables.net-dt/css/jquery.dataTables.min.css"
    import 'vue3-toastify/dist/index.css';

    const { estudiante } = defineProps(['estudiante']);

    const estudiante_notas = ref(null);
    const cursos = ref([]);
    
    const isOpenModalNotas = ref(false);

    const closeModalEstudiantes = () =>{
        isOpenModalNotas.value = false;
    }

    const observaciones = ref({
        "N": "Ninguna observacion",
        "A": "No asiste a clases",
        "B": "No trajo sus tareas",
        "C": "Llega tarde a clases",
        "D": "Hace mucho ruido en clase",
        "E": "Llamadas consecutivas a padres de familia",
        "F": "No presentó actividad dentro de clase",
    });

    const fetchCursosByEstudiante = async (id) => {
        const response = await axios.get(`/cursos/fetchCursosByEstudiante/${id}`);
        return response.data.cursos;
    }

    const fetchNotasByEstudiante = async (curso_id, estudiante_id) => {
        const response = await axios.get(`/cursos/fetchNotasByEstudiante/${curso_id}/${estudiante_id}`);
        isOpenModalNotas.value = true;
        estudiante_notas.value = response.data.estudiante;
    }

    const refreshDataTable = async () => {
        try {
            const data = await fetchCursosByEstudiante(estudiante.id);
            cursos.value = data;
            if ($.fn.DataTable.isDataTable('#myDataTable')) {
                $('#myDataTable').DataTable().destroy();
            }
            $('#myDataTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                columns: [
                    { data: 'curso_id' },
                    { data: 'curso_nombre' },
                    { data: null,
                        render: function (data, type, row) {
                            if (row.nota_prediccion != '') {
                                return `<p class="text-primary">${data.nota_prediccion} - ${observaciones.value[data.observacion_prediccion]}</p>`;
                            }
                            else {
                                return '';
                            }
                        },
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<a class="btn btn-sm btn-info view-notas" data-curso_id="${row.curso_id}">
                                <i class="mdi mdi mdi-clipboard-text menu-icon"></i> Detalle
                            </a>`;
                        },
                    },
                ],
                data: data,
                initComplete: function () {
                    $('#myDataTable thead').html('<tr><th>ID</th><th>Curso</th><th>Predicciones</th><th>Opciones</th></tr>');
                }
            });

            $('#myDataTable').on('click', '.view-notas', function () {
                const curso_id = $(this).data('curso_id');
                fetchNotasByEstudiante(curso_id, estudiante.id);
            });

        } catch (error) {
            console.error('Error al actualizar el DataTable:', error);
        }
    };

    onMounted(async () => {
        try {
            await refreshDataTable();
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
        }
    });

</script>
