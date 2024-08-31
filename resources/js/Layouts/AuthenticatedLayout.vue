<script setup>
import DropdownLink from '@/Components/DropdownLink.vue';
</script>

<template>
    <div class="container-scroller d-flex">
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item sidebar-category">
                    <p>Inicio</p>
                    <span></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :href="route('dashboard')">
                        <i class="mdi mdi-view-quilt menu-icon"></i>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item sidebar-category">
                    <p>Panel</p>
                    <span></span>
                </li>
                <template v-if="$page.props.auth.user.rol_id == 1 || $page.props.auth.user.rol_id == 2">
                    <li class="nav-item" >
                        <a class="nav-link" :href="route('profesores.index')">
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                            <span class="menu-title">Profesores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :href="route('estudiantes.index')">
                            <i class="mdi mdi-emoticon menu-icon"></i>
                            <span class="menu-title">Alumnos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :href="route('asignaturas.index')">
                            <i class="mdi mdi-book menu-icon"></i>
                            <span class="menu-title">Asignaturas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :href="route('grupos.index')">
                            <i class="mdi mdi-human-male-female menu-icon"></i>
                            <span class="menu-title">Grupos</span>
                        </a>
                    </li>
                    <li class="nav-item sidebar-category">
                        <p>Gestion</p>
                        <span></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :href="route('periodos.index')">
                            <i class="mdi mdi-playlist-minus menu-icon"></i>
                            <span class="menu-title">Periodos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :href="route('users.index')">
                            <i class="mdi mdi-view-quilt menu-icon"></i>
                            <span class="menu-title">Usuarios</span>
                        </a>
                    </li>
                </template>
                <template v-if="$page.props.auth.user.rol_id == 3">
                    <li class="nav-item" >
                        <a class="nav-link" :href="route('cursos.index')">
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                            <span class="menu-title">Cursos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :href="route('clases.index')">
                            <i class="mdi mdi-book-open-page-variant menu-icon"></i>
                            <span class="menu-title">Clases</span>
                        </a>
                    </li>
                </template>
                <template v-if="$page.props.auth.user.rol_id == 4">
                    <li class="nav-item" >
                        <a class="nav-link" :href="route('cursos.index_estudiante')">
                            <i class="mdi mdi-book menu-icon"></i>
                            <span class="menu-title">Cursos</span>
                        </a>
                    </li>
                </template>
            </ul>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <nav class="navbar col-lg-12 col-12 px-0 py-0 py-lg-4 d-flex flex-row">
                <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                        <span class="mdi mdi-menu"></span>
                    </button>
                    <ul class="navbar-nav navbar-nav-right">
                        <li class="nav-item nav-profile dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                                <img src="layout/images/faces/face.jpg" width="30" height="30" alt="profile" class="img-profile"/>
                                <span class="nav-profile-name">{{ $page.props.auth.user.name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                                <DropdownLink :href="route('profile.edit')"><i class="mdi mdi-settings text-primary"></i> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    <i class="mdi mdi-logout text-primary"></i> Log Out
                                </DropdownLink>
                            </div>
                        </li>
                    </ul>
                    <button class="navbar-toggler navbar-toggle-mini navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                        <span class="mdi mdi-menu"></span>
                    </button>
                </div>
            </nav>
            <div class="main-panel">
                <div class="content-wrapper">
                    <main>
                        <slot />
                    </main>
                </div>
                <footer class="footer">
                <div class="card">
                    <div class="card-body">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Jhosen Alcon 2023</span>
                    </div>
                    </div>
                </div>
                </footer>
            </div>
        </div>
    </div>    
</template>
