
<template>
    <Head title="Periodos" />
    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-4">
                            <div class="col">
                                <h4 class="card-title">Listado de Cursos</h4>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="myDataTable"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    const cursos = ref([]);
    const estudiantes = ref([]);
    const form = ref({
        id: '',
        anio: '',
        semestre: ''
    });
    const errors = ref({});
    const isOpenModal = ref(false);
    const isOpenModalEstudiantes = ref(false);
    const titleForm = ref('Registrar Periodo')

    const initForm = () =>{
        form.value = {
            id: '',
            anio: '',
            semestre: ''
        };
        errors.value = {}
        titleForm.value = 'Registrar Periodo';        
    }

    const closeModalEstudiantes = () =>{
        isOpenModalEstudiantes.value = false;
    }

    const fetchDataTable = async () => {
        try {
            const response = await axios.get('/cursos/fetchDataTable');
            return response.data.cursos;
        } catch (error) {
            console.error('Error al obtener datos de la API:', error);
            return [];
        }
    };

    const fetchGrupoEstudiantes = async (id) => {
        const response = await axios.get(`/cursos/fetchGrupoEstudiantes/${id}`);
        estudiantes.value = response.data.estudiantes;
        isOpenModalEstudiantes.value = true;
    }
    const refreshDataTable = async () => {
        try {
            const data = await fetchDataTable();
            cursos.value = data;
            if ($.fn.DataTable.isDataTable('#myDataTable')) {
                $('#myDataTable').DataTable().destroy();
            }
            $('#myDataTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                columns: [
                    { data: 'id' },
                    { data: 'grupo.nombre' },
                    { data: 'grupo.nivel.nombre' },
                    { data: 'asignatura.nombre' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<a class="btn btn-sm btn-info view-estudiantes" href="/cursos/${row.id}">
                                <i class="mdi mdi mdi-clipboard-text menu-icon"></i> Detalle
                            </a>`;
                        },
                    },
                ],
                data: data,
                initComplete: function () {
                    $('#myDataTable thead').html('<tr><th>ID</th><th>Grupo</th><th>Nivel</th><th>Asignatura</th><th>Opciones</th></tr>');
                }
            });

            $('#myDataTable').on('click', '.view-estudiantes', function () {
                const id = $(this).data('id');
                fetchGrupoEstudiantes(id);
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
