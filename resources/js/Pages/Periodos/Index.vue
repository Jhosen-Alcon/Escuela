
<template>
    <Head title="Periodos" />
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-4">
                            <div class="col">
                                <h4 class="card-title">Listado de Periodos</h4>
                            </div>
                            <div class="col">
                                <a class="btn btn-sm btn-primary float-right" @click="openModal" role="button" aria-expanded="false" aria-controls="collapseExample">
                                    Crear Periodo
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
                                <label for="exampleInputUsername1">Año</label>
                                <select class="form-control" v-model="form.anio">
                                    <option v-for="anio in anios" :key="anio" :value="anio">{{ anio }}</option>
                                </select>
                                <code v-if="errors.anio" v-text="errors.anio[0]"></code>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Semestre</label>
                                <select class="form-control" v-model="form.semestre">
                                    <option v-for="semestre in semestres" :key="semestre" :value="semestre">{{ semestre }}</option>
                                </select>
                                <code v-if="errors.semestre" v-text="errors.semestre[0]"></code>
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
    import Swal from 'sweetalert2';

    const users = ref([]);
    const anios = ref([
        2023, 2024, 2025
    ]);
    const semestres = ref([
        'I', 'II', 'III', 'IV'
    ]);
    const form = ref({
        id: '',
        anio: '',
        semestre: ''
    });
    const errors = ref({});
    const isOpenModal = ref(false);
    const titleForm = ref('Registrar Periodo')

    const submit = async () => {        
        await axios.post('periodos', form.value)
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
        form.value = ref({
            id: '',
            anio: '',
            semestre: ''
        });
        errors.value = {}
        titleForm.value = 'Registrar Periodo';        
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
            const response = await axios.get('/periodos/fetchDataTable');
            return response.data.data;
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
            return [];
        }
    };

    const deleteItem = async (id) => {
        try {
            const response = await axios.delete(`/periodos/${id}`);
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
                    { data: 'anio' },
                    { data: 'semestre' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<a class="btn btn-sm btn-info edit-item" title="Editar" data-id="${row.id}" data-semestre="${row.semestre}" data-anio="${row.anio}"><i class="mdi mdi-pencil menu-icon"></i></a>
                            <a class="btn btn-sm btn-danger delete-item" title="Eliminar" data-id="${row.id}"><i class="mdi mdi-delete menu-icon"></i></a>`;
                        },
                    },
                ],
                data: data,
                initComplete: function () {
                    $('#myDataTable thead').html('<tr><th>ID</th><th>Semestre</th><th>Año</th><th>Opciones</th></tr>');
                }
            });

            $('#myDataTable').on('click', '.edit-item', function () {
                isOpenModal.value = true;
                form.value = ({
                    id: $(this).data('id'),
                    anio: $(this).data('anio'),
                    semestre: $(this).data('semestre'),
                });
                titleForm.value = 'Editar Periodo';
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
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
        }
    });

</script>
