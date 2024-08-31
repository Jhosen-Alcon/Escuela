
<template>
    <Head title="Usuarios" />
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-4">
                            <div class="col">
                                <h4 class="card-title">Listado de estudiantes</h4>
                            </div>
                            <div class="col">
                                <a class="btn btn-sm btn-primary float-right" @click="openCollapse" role="button" aria-expanded="false" aria-controls="collapseExample">
                                    Crear Estudiante
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
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ titleForm }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeCollapse">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="forms-sample">
                            <div class="form-group">
                                <label for="exampleInputUsername1">Nombres</label>
                                <input type="text" v-model="form.nombres" class="form-control" id="exampleInputUsername1">
                                <code v-if="errors.nombres" v-text="errors.nombres[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Apellidos</label>
                                <input type="text" v-model="form.apellidos" class="form-control" id="exampleInputUsername1">
                                <code v-if="errors.apellidos" v-text="errors.apellidos[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="numero_documento">Número de documento</label>
                                <input type="text" v-model="form.numero_documento" class="form-control" id="numero_documento">
                                <code v-if="errors.numero_documento" v-text="errors.numero_documento[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Género</label>
                                <select class="form-control" v-model="form.genero">
                                    <option value="">Seleccione género</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                                <code v-if="errors.genero" v-text="errors.genero[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Correo</label>
                                <input type="email" v-model="form.email" class="form-control" id="exampleInputEmail1">
                                <code v-if="errors.email" v-text="errors.email[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Contraseña</label>
                                <input type="password" v-model="form.password" class="form-control" id="exampleInputPassword1">
                                <code v-if="errors.password" v-text="errors.password[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputConfirmPassword1">Confirmar Contraseña</label>
                                <input type="password" v-model="form.password_confirmation" class="form-control" id="exampleInputConfirmPassword1">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Estado</label>
                                <select class="form-control" v-model="form.estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                <code v-if="errors.estado" v-text="errors.estado[0]"></code>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeCollapse">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="submit">Guardar</button>
                    </div>
                </div>
            </div>
            </div>
        <div v-show="isOpenModal" class="modal-backdrop fade show"></div>
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
    const form = ref({
        id: '',
        rol_id: 4,
        nombre: '',
        apellidos: '',
        genero: '',
        email: '',
        password: '',
        password_confirmation: '',
        estado: 1
    });
    const errors = ref({});
    const isOpenModal = ref(false);
    const titleForm = ref('Registrar Estudiante')

    const submit = async () => {
        let  url = (form.value.id == '')?'users':'users/edit'
        
        await axios.post(url, form.value)
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
            rol_id: 4,
            nombre: '',
            apellidos: '',
            genero: '',
            email: '',
            password: '',
            password_confirmation: '',
            estado: 1
        };
        errors.value = {}
        titleForm.value = 'Registrar Estudiante';   
    }

    const openCollapse = () =>{
        isOpenModal.value = true;
        initForm();
    }

    const closeCollapse = () =>{
        isOpenModal.value = false;
    }

    const fetchData = async () => {
        try {
            const response = await axios.get('/estudiantes/fetchDataTable');
            return response.data;
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
            return [];
        }
    };

    const fetchDataById = async (id) => {
        try {
            const response = await axios.get(`/users/fetchDataById/${id}`);
            form.value = response.data;
            titleForm.value = 'Editar Estudiante';
            return response.data;
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
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
                    { data: 'name' },
                    { data: 'email' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return row.state==1?'Activo':'Inactivo';
                        },
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<a class="btn btn-sm btn-info edit-user" title="Editar" data-id="${row.id}"><i class="mdi mdi-pencil menu-icon"></i></a>`;
                        },
                    },
                ],
                data: data,
                initComplete: function () {
                    $('#myDataTable thead').html('<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Estado</th><th>Opciones</th></tr>');
                }
            });

            $('#myDataTable').on('click', '.edit-user', function () {
                const userId = $(this).data('id');
                isOpenModal.value = true;
                fetchDataById(userId);                
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
