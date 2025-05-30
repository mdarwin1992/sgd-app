<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="/dashboard" class="logo logo-light">
        <span class="logo-lg"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></span>
        <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo"></span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="/dashboard" class="logo logo-dark">
        <span class="logo-lg"><img src="{{ asset('assets/images/logo-dark.png') }}" alt="dark logo"></span>
        <span class="logo-sm"><img src="{{ asset('assets/images/logo-dark-sm.png') }}" alt="small logo"></span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a href="pages-profile.html" style="display: none">
                <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="user-image" height="42"
                    class="rounded-circle shadow-sm">
                <span class="leftbar-user-name mt-2">Dominic Keller</span>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title">Dashboard</li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard" class="side-nav-link visible" data-roles="ADMINISTRADOR,EMPRESA,USUARIO"
                    data-permissions="dashboard.page">
                    <i class="fas fa-home-lg-alt"></i>
                    <span> Paneles de control </span>
                </a>
            </li>

            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/empresa" class="side-nav-link visible" data-roles="ADMINISTRADOR,EMPRESA"
                    data-permissions="business.index">
                    <i class="fas fa-building"></i>
                    <span> Empresa </span>
                </a>
            </li>

            <li class="side-nav-title">Ventanilla Única</li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/ventanilla-unica/recepcion" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="reception.create">
                    <i class="far fa-concierge-bell"></i>
                    <span> Recepción </span>
                </a>
            </li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/ventanilla-unica/transferir-correspondencias-recibidas"
                    class="side-nav-link visible" data-roles="ADMINISTRADOR,EMPRESA,USUARIO"
                    data-permissions="transfer.index">
                    <i class="far fa-random"></i>
                    <span> Transferir </span>
                </a>
            </li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/ventanilla-unica/mi-buzon" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="mailbox.index">
                    <i class="far fa-mailbox"></i>
                    <span> Mi buzon </span>
                </a>
            </li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/ventanilla-unica/enviar" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="sendings.create">
                    <i class="fas fa-paper-plane"></i>
                    <span> Enviar </span>
                </a>
            </li>
            <li class="side-nav-title" id="centralfile">Archivo Central</li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/archivo-central" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="centralfile.create">
                    <i class="fas fa-cabinet-filing"></i>
                    <span> Archivo Central </span>
                </a>
            </li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/archivo-central/consultas" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="query.consultation">
                    <i class="fas fa-file-search"></i>
                    <span> Consultas </span>
                </a>
            </li>
            <li class="side-nav-title">Archivo Historico</li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/archivo-historico/crear" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="historic.create">
                    <i class="fas fa-folders"></i>
                    <span> Archivo Historico </span>
                </a>
            </li>
            <li class="side-nav-title">Prestamos Documentales</li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/prestamos-documental" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA" data-permissions="lending.index">
                    <i class="fas fa-people-arrows"></i>
                    <span> Prestamos Documentales </span>
                </a>
            </li>
            <li class="side-nav-title">Configuracion</li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/usuarios" class="side-nav-link visible" data-roles="ADMINISTRADOR,EMPRESA"
                    data-permissions="users.index">
                    <i class="fas fa-user"></i>
                    <span> Usuarios </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDashboards" aria-expanded="false"
                    aria-controls="sidebarDashboards" class="side-nav-link">
                    <i class="far fa-user-cog"></i>
                    <span> Configuracion </span>
                </a>
                <div class="collapse" id="sidebarDashboards">
                    <ul class="side-nav-second-level">
                        <li id="rutas">
                            <a data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="departments.index"
                                href="/dashboard/departamentos">Departamentos</a>
                        </li>
                        <li id="rutas">
                            <a data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="offices.index"
                                href="/dashboard/oficinas">Oficinas</a>
                        </li>
                        <li id="rutas">
                            <a data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="trd.create"
                                href="/dashboard/tabla-de-retencion-documental">TRD</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-title">Reportes</li>
            <li class="side-nav-item" id="rutas">
                <a href="/dashboard/ventanilla-unica/reportes" class="side-nav-link visible"
                    data-roles="ADMINISTRADOR,EMPRESA,USUARIO" data-permissions="reports.singlewindow">
                    <i class="fas fa-flag-alt"></i>
                    <span> Reportes </span>
                </a>
            </li>
        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>
