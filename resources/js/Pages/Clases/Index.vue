
<template>
    <Head title="Lecciones" />
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-4">
                            <div class="col">
                                <h4 class="card-title">Listado de Clases</h4>
                            </div>
                            <div class="col">
                                <a class="btn btn-sm btn-primary float-right" @click="openModal" role="button" aria-expanded="false" aria-controls="collapseExample">
                                    Crear clase
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="myDataTable"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" v-show="isOpenModal"  :class="{ 'show': isOpenModal }" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ titleForm }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="forms-sample">
                            <div class="form-group">
                                <label for="exampleInputUsername1">Curso</label>
                                <select class="form-control" v-model="form.curso_id">
                                    <option value="">Seleccione curso</option>
                                    <option v-for="curso in cursos" :key="curso.id" :value="curso.id">{{ curso.grupo.nombre }} {{ curso.asignatura.nombre }}</option>
                                </select>
                                <code v-if="errors.curso_id" v-text="errors.curso_id[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Periodo</label>
                                <select class="form-control" v-model="form.periodo_id">
                                    <option value="">Seleccione periodo</option>
                                    <option v-for="periodo in periodos" :key="periodo.id" :value="periodo.id">{{ periodo.nombre }}</option>
                                </select>
                                <code v-if="errors.periodo_id" v-text="errors.periodo_id[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Fecha</label>
                                <input type="date" v-model="form.fecha_clase" class="form-control" id="exampleInputUsername1">
                                <code v-if="errors.semestre" v-text="errors.semestre[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Descripción</label>
                                <textarea v-model="form.descripcion" class="form-control"></textarea>
                                <code v-if="errors.descripcion" v-text="errors.descripcion[0]"></code>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="submit">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" v-show="isOpenModalEstudiantes" :class="{ 'show': isOpenModalEstudiantes }" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Asistencias</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModalEstudiantes">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped" v-if="estudiantes.length>0">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 30%;">Alumno</th>
                                        <th>Asistencia</th>
                                        <th>Nota</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="estudiante in estudiantes" :key="estudiante.estudiante_id">
                                        <td>{{ estudiante.estudiante_nombre }}</td>
                                        <td>
                                            <select class="form-control" v-model="estudiante.tipo" @change="saveAsistencia(estudiante)">
                                                <option value="">Marcar asistencia</option>
                                                <option value="A">Asistió</option>
                                                <option value="F">Falta</option>
                                                <option value="J">Justificado</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" v-model="estudiante.nota"  @blur="saveAsistencia(estudiante)"></td>
                                        <td>
                                            <select class="form-control" v-model="estudiante.observacion" @change="saveAsistencia(estudiante)">
                                                <option value="N">Ninguno</option>
                                                <option value="A">No asiste a clases</option>
                                                <option value="B">No trajo su tarea</option>
                                                <option value="C">Llega tarde a clases</option>
                                                <option value="D">Hace mucho ruido en clase</option>
                                                <option value="E">Llamadas consecutivas a padres de familia</option>
                                                <option value="F">No presento actividad dentro de clase</option>
                                            </select>
                                        </td>
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
        <div v-show="isOpenModal" class="modal-backdrop fade show"></div>
        <div v-show="isOpenModalEstudiantes" class="modal-backdrop fade show"></div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import $ from 'jquery';
    import "datatables.net-dt/js/dataTables.dataTables"
    import "datatables.net-dt/css/jquery.dataTables.min.css"
    import { toast } from 'vue3-toastify';
    import 'vue3-toastify/dist/index.css';
    import Swal from 'sweetalert2';

    const users = ref([]);
    const periodos = ref([]);
    const cursos = ref([]);
    const estudiantes = ref([]);

    const recurso = 'clases'
    
    const form = ref({
        id: '',
        curso_id: '',
        periodo_id: '',
        fecha_clase: '',
        descripcion: '',
        estado: 1
    });
    const errors = ref({});
    const isOpenModal = ref(false);
    const isOpenModalEstudiantes = ref(false);
    const titleForm = ref('Crear clase')

    const closeModalEstudiantes = () =>{
        isOpenModalEstudiantes.value = false;
    }

    const submit = async () => {        
        await axios.post(`${recurso}`, form.value)
        .then(response => {
            if (response.data.success) {
                toast.success(response.data.message);
                initForm();
                refreshDataTable();
                isOpenModal.value = false;
            } else {
                toast.error(response.data.message)
            }
        })
        .catch(error => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            } else {
                console.log(error)
            }
        })
    };

    const initForm = () =>{
        form.value = {
            id: '',
            curso_id: '',
            periodo_id: '',
            fecha_clase: '',
            descripcion: '',
            estado: 1
        };
        errors.value = {}
        titleForm.value = 'Registrar Clase';        
    }

    const openModal = () =>{
        isOpenModal.value = true;
        initForm();
    }

    const closeModal = () =>{
        isOpenModal.value = false;
    }

    const fetchData = async () => {
        try {
            const response = await axios.get(`/${recurso}/fetchDataTable`);
            return response.data;
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
            return [];
        }
    };

    const fetchLoadForm = async () => {
        try {
            const response = await axios.get(`/${recurso}/fetchLoadForm`);
            let data = response.data;
            cursos.value = data.cursos
            periodos.value = data.periodos
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
        }
    };

    const fetchGrupoEstudiantes = async (clase_id, curso_id) => {
        const response = await axios.get(`/${recurso}/fetchGrupoEstudiantes/${clase_id}/${curso_id}`);
        estudiantes.value = response.data.estudiantes;
        isOpenModalEstudiantes.value = true;
    }

    const saveAsistencia = async (estudiante) => {
        let form_nota = {
            clase_id: estudiante.clase_id,
            estudiante_id: estudiante.estudiante_id,
            tipo: estudiante.tipo,
            nota: estudiante.nota,
            observacion: estudiante.observacion,
        }

        await axios.post(`/${recurso}/storeAsistencia`, form_nota).then(response => {
            if (response.data.success) {
                toast.success(response.data.message);
            } else {
                toast.error(response.data.message)
            }
        })
        .catch(error => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            } else {
                console.log(error)
            }
        })
    }

    const deleteItem = async (id) => {
        try {
            const response = await axios.delete(`/${recurso}/${id}`);
            toast.success(response.data.message);
            refreshDataTable();
        } catch (error) {
            console.error('Error al eliminar item:', error);
            return [];
        }
    };

    const refreshDataTable = async () => {
        try {
            const data = await fetchData();
            users.value = data;
            if ($.fn.DataTable.isDataTable('#myDataTable')) {
                $('#myDataTable').DataTable().destroy();
            }
            $('#myDataTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                columns: [
                    { data: 'id' },
                    { data: 'curso.asignatura.nombre' },
                    { data: 'periodo.nombre' },
                    { data: 'fecha_clase' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return ` <a class="btn btn-sm btn-success asistencia-item" title="Asistencia" data-id="${row.id}" data-curso_id="${row.curso_id}">Asistencia <i class="mdi mdi mdi-clipboard-check menu-icon"></i></a>
                                <a class="btn btn-sm btn-info edit-item" title="Editar" data-id="${row.id}" data-periodo_id="${row.periodo_id}"
                                data-curso_id="${row.curso_id}" data-fecha_clase="${row.fecha_clase}" data-descripcion="${row.descripcion}"><i class="mdi mdi-pencil menu-icon"></i></a>
                            <a class="btn btn-sm btn-danger delete-item" title="Eliminar" data-id="${row.id}"><i class="mdi mdi-delete menu-icon"></i></a>`;
                        },
                    },
                ],
                data: data,
                initComplete: function () {
                    $('#myDataTable thead').html('<tr><th>ID</th><th>Asignatura</th><th>Periodo</th><th>Fecha clase</th><th>Opciones</th></tr>');
                }
            });

            $('#myDataTable').on('click', '.asistencia-item', function () {
                const id = $(this).data('id');
                const curso_id = $(this).data('curso_id');
                fetchGrupoEstudiantes(id, curso_id);
            });

            $('#myDataTable').on('click', '.edit-item', function () {
                isOpenModal.value = true;
                form.value = ({
                    id: $(this).data('id'),
                    periodo_id: $(this).data('periodo_id'),
                    curso_id: $(this).data('curso_id'),
                    fecha_clase: $(this).data('fecha_clase'),
                    descripcion: $(this).data('descripcion'),
                });
                titleForm.value = 'Editar clase';
            });

            $('#myDataTable').on('click', '.delete-item', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Estas seguro de eliminar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteItem(id)
                    }
                })
            });
        } catch (error) {
            console.error('Error al actualizar el DataTable:', error);
        }
    };

    onMounted(async () => {
        try {
            await refreshDataTable();
            await fetchLoadForm();
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
        }
    });

</script>
