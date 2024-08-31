
<template>
    <Head title="Periodos" />
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-4">
                            <div class="col">
                                <h4 class="card-title">Curso: {{ curso.asignatura.nombre }}</h4>
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
                            <div class="row">
                                <div class="col">
                                    <br>
                                    <button type="button" class="btn btn-info" @click="predecir(estudiante_notas)">Predecir nota final</button>
                                    <div v-if="nota_prediccion != ''" class="alert alert-info mt-1" role="alert">
                                        Nota predicha: {{ nota_prediccion }}
                                        <br>
                                        Observación: {{ observacion_prediccion }}
                                    </div>
                                </div>
                            </div>
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
            curso: {
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

    const { curso } = defineProps(['curso']);

    const estudiante_notas = ref(null);
    const nota_prediccion = ref('');
    const observacion_prediccion = ref('');
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

    const encontrarValorMasFrecuente = (lista) => {
        const frecuencias = {};

        lista.forEach((valor) => {
            frecuencias[valor] = (frecuencias[valor] || 0) + 1;
        });

        let valorMasFrecuente;
        let frecuenciaMasAlta = 0;

        for (const valor in frecuencias) {
            if (frecuencias[valor] > frecuenciaMasAlta) {
                valorMasFrecuente = valor;
                frecuenciaMasAlta = frecuencias[valor];
            }
        }

        return valorMasFrecuente;
    };

    const obtenerFormato = (data) => {
        const formato = {};

        for (let i = 1; i <= 4; i++) {
            const keyNota = `nota${i}`;
            formato[keyNota] = parseFloat(data.nota.notas[i - 1]) || 100;
        }

        for (let i = 1; i <= 4; i++) {
            const keyAsistencia = `asistencia${i}`;
            const keyObservacion = `observacion${i}`;

            const dataAsistencias = data.asistencias[i - 1];
            const asistencias = dataAsistencias.map(asistencia => asistencia.tipo);
            const observaciones = dataAsistencias.map(asistencia => asistencia.observacion);

            formato[keyAsistencia] = encontrarValorMasFrecuente(asistencias) || 'A'

            formato[keyObservacion] = encontrarValorMasFrecuente(observaciones) || 'N';
        }

        formato['estudiante_id'] = data.estudiante_id;
        formato['curso_id'] = data.curso_id;
        return formato;
    };

    const predecir = async (estudiante_notas) => {
        const resultadoFormato = obtenerFormato(estudiante_notas);
        
        const response = await axios.post(`/cursos/predecir`, resultadoFormato);
        nota_prediccion.value = response.data.nota_prediccion;
        observacion_prediccion.value = observaciones.value[response.data.observacion_prediccion];
    }

    const fetchGrupoEstudiantes = async (id) => {
        const response = await axios.get(`/cursos/fetchGrupoEstudiantes/${id}`);
        return response.data.estudiantes;
    }

    const fetchNotasByEstudiante = async (curso_id, estudiante_id) => {
        const response = await axios.get(`/cursos/fetchNotasByEstudiante/${curso_id}/${estudiante_id}`);
        isOpenModalNotas.value = true;
        estudiante_notas.value = response.data.estudiante;
    }

    const refreshDataTable = async () => {
        try {
            const data = await fetchGrupoEstudiantes(curso.id);
            cursos.value = data;
            if ($.fn.DataTable.isDataTable('#myDataTable')) {
                $('#myDataTable').DataTable().destroy();
            }
            $('#myDataTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                columns: [
                    { data: 'estudiante_id' },
                    { data: 'estudiante_nombre' },
                    { data: 'nota_promedio' },
                    { data: null,
                        render: function (data, type, row) {
                            return `<p class="text-primary">${data.nota_prediccion} - ${observaciones.value[data.observacion_prediccion]}</p>`;
                        },
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<a class="btn btn-sm btn-info view-notas" data-estudiante_id="${row.estudiante_id}" data-toggle="tooltip" title="Texto del tooltip">
                                <i class="mdi mdi mdi-clipboard-text menu-icon"></i> Notas y asistencias
                            </a>`;
                        },
                    },
                ],
                data: data,
                initComplete: function () {
                    $('#myDataTable thead').html('<tr><th>ID</th><th>Estudiante</th><th>Nota promedio</th><th>Predicciones</th><th>Opciones</th></tr>');
                }
            });

            $('#myDataTable').on('click', '.view-notas', function () {
                const estudiante_id = $(this).data('estudiante_id');
                fetchNotasByEstudiante(curso.id, estudiante_id);
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
