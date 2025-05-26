<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="/dashboard" class="logo-light">
                    <span class="logo-lg"><img src="{{ asset ('assets/images/logo.png')}}" alt="logo"></span>
                    <span class="logo-sm"><img src="{{ asset ('assets/images/logo-sm.png')}}" alt="small logo"></span>
                </a>

                <!-- Logo Dark -->
                <a href="/dashboard" class="logo-dark">
                    <span class="logo-lg"><img src="{{ asset ('assets/images/logo-dark.png')}}" alt="dark logo"></span>
                    <span class="logo-sm"><img src="{{ asset ('assets/images/logo-dark-sm.png')}}"
                                               alt="small logo"></span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="mdi mdi-menu"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown d-lg-none">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class="ri-search-line font-22"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                    <form class="p-3">
                        <input type="search" class="form-control" placeholder="Search ..."
                               aria-label="Recipient's username">
                    </form>
                </div>
            </li>

            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class="ri-notification-3-line font-22"></i>

                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 font-16 fw-semibold"> Notificación</h6>
                            </div>
                            <div class="col-auto">
                                <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                    <small>Limpiar todo</small>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="px-3" style="max-height: 300px;" data-simplebar>
                        <!-- Notifications will be dynamically inserted here -->
                    </div>

                    <!-- All-->
                    <a href="javascript:void(0);"
                       class="dropdown-item text-center text-primary notify-item border-top py-2">
                        Ver todo
                    </a>
                </div>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="left"
                     title="Theme Mode">
                    <i class="ri-moon-line font-22"></i>
                </div>
            </li>


            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="#" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line font-22"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <img src="{{ asset ('assets/images/users/avatar-1.jpg')}}" alt="user-image" width="32"
                             class="rounded-circle">
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0" data-user-attribute="name"></h5>
                        <h6 class="my-0 fw-normal" data-user-attribute="roles"></h6>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Buenas noches bienvenido !</h6>
                    </div>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <i class="mdi mdi-account-circle me-1"></i>
                        <span>Mi cuenta</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <i class="mdi mdi-account-edit me-1"></i>
                        <span>Ajustes</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <i class="mdi mdi-lifebuoy me-1"></i>
                        <span>Apoyo</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item" id="logout-btn">
                        <i class="mdi mdi-logout me-1"></i>
                        <span>Cerrar sesión</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>

