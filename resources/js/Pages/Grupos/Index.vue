
<template>
    <Head title="Usuarios" />
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-4">
                            <div class="col">
                                <h4 class="card-title">Grupos</h4>
                            </div>
                            <div class="col">
                                <a class="btn btn-sm btn-primary float-right" @click="openModal" role="button" aria-expanded="false" aria-controls="collapseExample">
                                    Crear Grupo
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
                                <label for="rol">Nivel</label>
                                <select class="form-control" v-model="form.nivel_id">
                                    <option value="">Seleccione nivel</option>
                                    <option v-for="nivel in niveles" :key="nivel.id" :value="nivel.id">{{ nivel.nombre }}</option>
                                </select>
                                <code v-if="errors.nivel_id" v-text="errors.nivel_id[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="rol">Grado</label>
                                <select class="form-control" v-model="form.grado">
                                    <option value="">Seleccione grado</option>
                                    <option v-for="grado in grados" :key="grado" :value="grado">{{ grado }}</option>
                                </select>
                                <code v-if="errors.grado" v-text="errors.grado[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="rol">Sección</label>
                                <select class="form-control" v-model="form.seccion">
                                    <option value="">Seleccione sección</option>
                                    <option v-for="seccion in secciones" :key="seccion" :value="seccion">{{ seccion }}</option>
                                </select>
                                <code v-if="errors.seccion" v-text="errors.seccion[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="rol">Empleado</label>
                                <select class="form-control" v-model="form.empleado_id">
                                    <option value="">Seleccione empleado</option>
                                    <option v-for="empleado in empleados" :key="empleado.id" :value="empleado.id">{{ empleado.persona.nombre_completo }}</option>
                                </select>
                                <code v-if="errors.empleado_id" v-text="errors.empleado_id[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="rol">Año Académico</label>
                                <select class="form-control" v-model="form.anio">
                                    <option v-for="anio in anios" :key="anio" :value="anio">{{ anio }}</option>
                                </select>
                                <code v-if="errors.anio" v-text="errors.anio[0]"></code>
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
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Estudiantes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModalEstudiantes">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="forms-sample">
                            <div class="form-group">
                                <label for="exampleTextarea1">Agregar estudiante</label>
                                <div class="input-group">
                                    <select class="form-control" v-model="form_grupo_estudiante.estudiante_id">
                                        <option value="" selected>Seleccione estudiante</option>
                                        <option v-for="estudiante in estudiantes" :key="estudiante.id" :value="estudiante.id">{{ estudiante.persona.nombres }} {{ estudiante.persona.apellidos }}</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-primary" type="button" @click="submitGrupoEstudiante">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <h4 class="card-title">Listado</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr v-for="grupo_estudiante in grupo_estudiantes" :key="grupo_estudiante.id" >
                                        <td>{{ grupo_estudiante.estudiante.persona.nombre_completo }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-danger edit-grupo" @click="deleteGrupoEstudiante(grupo_estudiante.id)">
                                                <i class="mdi mdi-close menu-icon"></i>
                                            </a>
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
        <div class="modal fade" v-show="isOpenModalAsingnaturas" :class="{ 'show': isOpenModalAsingnaturas }" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cursos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModalEstudiantes">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="forms-sample">
                            <div class="form-group">
                                <label for="exampleTextarea1">Agregar Curso</label>
                                <div class="input-group">
                                    <select class="form-control" v-model="form_grupo_asignatura.asignatura_id">
                                        <option value="" selected>Seleccione curso</option>
                                        <option v-for="asignatura in asignaturas" :key="asignatura.id" :value="asignatura.id">{{ asignatura.nombre }}</option>
                                    </select>
                                    <select class="form-control" v-model="form_grupo_asignatura.empleado_id">
                                        <option value="" selected>Seleccione empleado</option>
                                        <option v-for="empleado in empleados" :key="empleado.id" :value="empleado.id">{{ empleado.persona.nombre_completo }}</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-primary" type="button" @click="submitGrupoAsignatura">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <h4 class="card-title">Listado</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr v-for="(grupo_asignatura, index) in grupo_asignaturas" :key="grupo_asignatura.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ grupo_asignatura.asignatura.nombre }}</td>
                                        <td>{{ grupo_asignatura.empleado.persona.nombre_completo }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-danger edit-grupo" @click="deleteGrupoAsignatura(grupo_asignatura.id)">
                                                <i class="mdi mdi-close menu-icon"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModalCursos">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="isOpenModal" class="modal-backdrop fade show"></div>
        <div v-show="isOpenModalEstudiantes" class="modal-backdrop fade show"></div>
        <div v-show="isOpenModalAsingnaturas" class="modal-backdrop fade show"></div>
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

    const users = ref([]);
    const niveles = ref([]);
    const grados = ref([
        '1', '2', '3', '4', '5', '6'
    ]);
    const secciones = ref([
        'A', 'B', 'C', 'D', 'E', 'F'
    ]);
    const anios = ref([
        2023, 2024, 2025
    ]);
    const empleados = ref([]);
    const estudiantes = ref([]);
    const asignaturas = ref([]);
    const grupo_estudiantes = ref([]);
    const grupo_asignaturas = ref([]);
    const form = ref({
        id: '',
        nivel_id: '',
        grado: '',
        seccion: '',
        empleado_id: '',
        anio: 2023,
        estado: 1
    });
    const form_grupo_estudiante = ref({
        grupo_id: '',
        estudiante_id: ''
    });
    const form_grupo_asignatura = ref({
        grupo_id: '',
        asignatura_id: '',
        empleado_id: ''
    });
    const errors = ref({});
    const isOpenModal = ref(false);
    const isOpenModalEstudiantes = ref(false);
    const isOpenModalAsingnaturas = ref(false);
    const titleForm = ref('Crear grupo')

    const initForm = () =>{
        form.value = {
            id: '',
            nivel_id: '',
            grado: '',
            seccion: '',
            empleado_id: '',
            anio: 2023,
            estado: 1
        };
        errors.value = {};
        titleForm.value = 'Crear grupo';
    }

    const openModal = () =>{
        isOpenModal.value = true;
        initForm();
    }

    const closeModal = () =>{
        isOpenModal.value = false;
    }

    const closeModalEstudiantes = () =>{
        isOpenModalEstudiantes.value = false;
    }

    const closeModalCursos = () =>{
        isOpenModalAsingnaturas.value = false;
    }

    const submit = async () => {
        await axios.post('grupos', form.value)
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

    const submitGrupoEstudiante = async () => {
        await axios.post('grupos/store_estudiantes', form_grupo_estudiante.value)
        .then(response => {
            if (response.data.success) {
                toast.success(response.data.message);
                initForm();
                fetchGrupoEstudiantes(form_grupo_estudiante.value.grupo_id);
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

    const deleteGrupoEstudiante = async (grupo_estudiante_id) => {
        await axios.delete(`grupos/estudiantes/${grupo_estudiante_id}`)
        .then(response => {
            if (response.data.success) {
                toast.success(response.data.message);
                initForm();
                fetchGrupoEstudiantes(form_grupo_estudiante.value.grupo_id);
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

    const deleteGrupoAsignatura = async (grupo_asignatura_id) => {
        await axios.delete(`grupos/asignaturas/${grupo_asignatura_id}`)
        .then(response => {
            if (response.data.success) {
                toast.success(response.data.message);
                initForm();
                fetchGrupoAsignaturas(form_grupo_asignatura.value.grupo_id);
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

    const submitGrupoAsignatura = async () => {
        await axios.post('grupos/store_asignaturas', form_grupo_asignatura.value)
        .then(response => {
            if (response.data.success) {
                toast.success(response.data.message);
                initForm();
                fetchGrupoAsignaturas(form_grupo_asignatura.value.grupo_id);
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

    const fetchData = async () => {
        try {
            const response = await axios.get('/grupos/fetchDataTable');
            return response.data;
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
            return [];
        }
    };

    const fetchDataById = async (id) => {
        try {
            const response = await axios.get(`/grupos/fetchDataById/${id}`);
            form.value = response.data;
            titleForm.value = 'Editar Grupo';
            return response.data;
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
            return [];
        }
    };

    const fechAllTables = async () => {
        const response = await axios.get('/grupos/fechAllTables');
        niveles.value = response.data.niveles;
        empleados.value = response.data.empleados;
    }

    const fetchGrupoEstudiantes = async (id) => {
        const response = await axios.get(`/grupos/fetchGrupoEstudiantes/${id}`);
        grupo_estudiantes.value = response.data.grupo_estudiantes;
        estudiantes.value = response.data.estudiantes;
    }

    const fetchGrupoAsignaturas = async (id) => {
        const response = await axios.get(`/grupos/fetchGrupoAsignaturas/${id}`);
        grupo_asignaturas.value = response.data.grupo_asignaturas;
        asignaturas.value = response.data.asignaturas;
    }

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
                    { data: 'anio' },
                    { data: 'nombre' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return row.empleado.persona.nombres+' '+row.empleado.persona.apellidos;
                        },
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                            <a class="btn btn-sm btn-info edit-grupo" title="Editar" data-id="${row.id}">
                                <i class="mdi mdi-pencil menu-icon"></i>
                            </a>
                            <a class="btn btn-sm btn-success view-estudiantes" data-id="${row.id}" data-toggle="tooltip" title="Texto del tooltip">
                                <i class="mdi mdi-account-multiple menu-icon"></i> Estudiantes
                            </a>
                            <a class="btn btn-sm btn-warning view-cursos" data-id="${row.id}" data-toggle="tooltip" title="Texto del tooltip">
                                <i class="mdi mdi-book menu-icon"></i> Cursos
                            </a>`;
                        },
                    },
                ],
                data: data,
                initComplete: function () {
                    $('#myDataTable thead').html('<tr><th>ID</th><th>Año</th><th>Nombre</th><th>Profesor</th><th>Opciones</th></tr>');
                }
            });

            $('#myDataTable').on('click', '.edit-grupo', function () {
                const id = $(this).data('id');
                isOpenModal.value = true;
                fetchDataById(id);
            });

            $('#myDataTable').on('click', '.view-estudiantes', function () {
                const id = $(this).data('id');
                form_grupo_estudiante.value.grupo_id = id;
                isOpenModalEstudiantes.value = true;
                fetchGrupoEstudiantes(id);
            });

            $('#myDataTable').on('click', '.view-cursos', function () {
                const id = $(this).data('id');
                form_grupo_asignatura.value.grupo_id = id;
                isOpenModalAsingnaturas.value = true;
                fetchGrupoAsignaturas(id);
            });
        } catch (error) {
            console.error('Error al actualizar el DataTable:', error);
        }
    };

    onMounted(async () => {
        try {
            await fechAllTables();
            await refreshDataTable();
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
        }
    });

</script>
