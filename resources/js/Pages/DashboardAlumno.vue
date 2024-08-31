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
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { defineProps } from 'vue';

    defineProps({
            cantidad_cursos: {
                type: String,
            },
            total_asistencia: {
                type: String,
            },
            total_faltas: {
                type: String,
            },
    });
</script>