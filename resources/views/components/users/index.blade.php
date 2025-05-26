@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="dashboard">
        <div data-permissions="users.index">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-5">
                                            <h4 class="header-title">Listado de Usuarios</h4>
                                            <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate">
                                                Lista todos los usuarios registrados con sus roles y permisos
                                            </h5>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="text-sm-end">
                                                <a role="button" href="/dashboard/usuario/crear"
                                                   class="btn btn-primary rounded-pill btn-sm text-white me-2">
                                                    <i class="mdi mdi-plus"></i> Crear Usuario
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="usersTable"
                                               class="table table-striped table-sm table-centered dt-responsive nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Teléfono</th>
                                                <th>Roles</th>
                                                <th>Permisos</th>
                                                <th width="10%">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        // userList.js
        import HTTPService from '/services/httpService/HTTPService.js';

        const OfficeListComponent = (() => {
            // Private variables
            let users = [];
            let dataTable = null;
            let isEditing = false;

            const fetchUsers = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/users');
                    users = response.data || [];
                    initDataTable();
                } catch (error) {
                    console.error('Error al obtener los usuarios:', error);
                }
            };

            const initDataTable = () => {
                if (dataTable) {
                    dataTable.destroy();
                }

                dataTable = new DataTable('#usersTable', {
                    data: users,
                    columns: [
                        {
                            data: 'name',
                            render: (data) => data.toUpperCase()
                        },
                        {data: 'email'},
                        {
                            data: 'phone',
                            render: (data) => data || 'N/A'
                        },
                        {
                            data: 'roles',
                            render: formatRoles
                        },
                        {
                            data: 'permissions',
                            render: formatPermissions
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                return `
                            <div class="table-action">
                                <a href="javascript:void(0);" class="action-icon edit-icon" data-id="${row.id}"> <i class="uil uil-edit"></i></a>
                                <a href="javascript:void(0);" class="action-icon delete-icon" data-id="${row.id}"> <i class="uil uil-trash-alt"></i></a>
                                <a href="javascript:void(0);" class="action-icon permissions-icon" data-id="${row.id}"> <i class="fas fa-lock-alt"></i></a>
                            </div>
                        `;
                            }
                        }
                    ],
                    responsive: true,
                    language: {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ registros",
                        "sZeroRecords": "No se encontraron resultados",
                        "sEmptyTable": "Ningún dato disponible en esta tabla",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar:",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
                });
            };

            const formatRoles = (roles) => {
                if (!roles || !roles.length) return 'Sin roles';
                return roles.map(role =>
                    `<span class="badge bg-primary me-1">${role.name}</span>`
                ).join(' ');
            };

            const formatPermissions = (permissions) => {
                if (!permissions || !permissions.length) return 'Permisos segun el Rol';
                return permissions.map(permission =>
                    `<span class="badge bg-info me-1">${permission.description}</span>`
                ).join(' ');
            };

            const formatDate = (date) => {
                if (!date) return 'N/A';
                return new Date(date).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            };

            const handleTableEvents = () => {
                $(document).on('click', '.edit-icon', function () {
                    const userId = $(this).data('id');
                    //console.log(userId)
                    window.location.href = `/dashboard/usuarios/actualizar/${userId}`;
                });

                $(document).on('click', '.delete-icon', function () {
                    const userId = $(this).data('id');

                });

                $(document).on('click', '.permissions-icon', function () {
                    const userId = $(this).data('id');

                });
            };

            // Initialize when the DOM is ready
            $(document).ready(() => {
                fetchUsers();
                handleTableEvents()
            });

            // Public API
            const publicApi = {
                fetchUsers,
                handleTableEvents,
                formatRoles,
                formatPermissions,
                formatDate,
            };

            // Expose to window object for inline event handlers
            window.OfficeManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
