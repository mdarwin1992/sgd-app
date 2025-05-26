@extends('layouts.app')

@section('title', 'Entidades')

@section('content')
    <div class="dashboard">
        <div data-permissions="business.index">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-5">
                                            <h4 class="header-title">Listado de Entidades</h4>
                                            <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Entidades">
                                                Lista todas las entidades registradas
                                            </h5>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="text-sm-end">
                                                <a href="/dashboard/empresa/crear"
                                                   class="btn btn-primary rounded-pill btn-sm text-white me-0 btn-sm">
                                                    <i class="icon-download"></i> Crear entidad
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="entitiesTable"
                                               class="table table-striped table-sm table-centered dt-responsive nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>NIT</th>
                                                <th>Nombre</th>
                                                <th>Tipo</th>
                                                <th>Representante Legal</th>
                                                <th>Email</th>
                                                <th>Estado</th>
                                                <th width="5%">Acciones</th>
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
                    <div id="error-message"></div>
                    <div id="loading"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';

        const EntityList = (() => {
            // Private variables
            let entities = [];
            let dataTable = null;

            const fetchEntities = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/entities');
                    entities = response.data || [];
                    initDataTable();
                } catch (error) {
                    console.error('Error al obtener las entidades:', error);
                    Swal.fire('Error', 'Error al cargar las entidades. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            // Función auxiliar para convertir a mayúsculas
            const upperCaseRenderer = (data) => {
                return data ? data.toString().toUpperCase() : '';
            };

            const initDataTable = () => {
                if (dataTable) {
                    dataTable.destroy();
                }

                dataTable = $('#entitiesTable').DataTable({
                    data: entities,
                    columns: [
                        {
                            data: null,
                            render: function (data, type, row) {
                                return `${row.nit}-${row.verification_digit}`;
                            }
                        },
                        {
                            data: 'name',
                            render: function(data, type, row) {
                                return upperCaseRenderer(data);
                            }
                        },
                        {
                            data: 'type',
                            render: function(data, type, row) {
                                return upperCaseRenderer(data);
                            }
                        },
                        {
                            data: 'legal_representative',
                            render: function(data, type, row) {
                                return data ? upperCaseRenderer(data) : 'N/A';
                            }
                        },
                        {
                            data: 'email',
                            render: function(data, type, row) {
                                return data ? data : 'N/A';
                            }
                        },
                        {
                            data: 'status',
                            render: function (data, type, row) {
                                const statusClass = data === 'ACTIVO' ? 'bg-success' : 'bg-danger';
                                return `<span class="badge ${statusClass}">${upperCaseRenderer(data)}</span>`;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                return `
                            <div class="table-action">
                                <a href="javascript:void(0);" class="action-icon edit-icon" data-id="${row.id}"> <i class="uil uil-edit"></i></a>
                                <a href="javascript:void(0);" class="action-icon delete-icon" data-id="${row.id}"> <i class="uil uil-trash-alt"></i></a>
                            </div>
                        `;
                            }
                        }
                    ],
                    responsive: true,
                    language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                        sInfoPostFix: "",
                        sSearch: "Buscar:",
                        sUrl: "",
                        sInfoThousands: ",",
                        sLoadingRecords: "Cargando...",
                        oPaginate: {
                            sFirst: "Primero",
                            sLast: "Último",
                            sNext: "Siguiente",
                            sPrevious: "Anterior",
                        },
                        oAria: {
                            sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                            sSortDescending: ": Activar para ordenar la columna de manera descendente",
                        },
                    }
                });

                handleTableEvents();
            };

            const handleTableEvents = () => {
                $('#entitiesTable').on('click', '.edit-icon', (event) => {
                    const entityId = $(event.currentTarget).data('id');
                    window.location.href = `/dashboard/empresa/actualizar/${entityId}`;
                });

                $('#entitiesTable').on('click', '.delete-icon', async (event) => {
                    const entityId = $(event.currentTarget).data('id');
                    console.log('Eliminar entidad:', entityId);

                    try {
                        await HTTPService.delete(`/api/dashboard/entities/${entityId}`);
                        // Recargar la tabla después de eliminar
                        fetchEntities();
                    } catch (error) {
                        console.error('Error al eliminar la entidad:', error);
                        Swal.fire('Error', 'Error al eliminar la entidad. Por favor, intente de nuevo más tarde.', 'error');
                    }
                });
            };

            // Initialize when the DOM is ready
            $(document).ready(() => {
                fetchEntities();
            });

            // Public API
            const publicApi = {
                fetchEntities,
                handleTableEvents
            };

            // Expose to window object for inline event handlers
            window.EntityList = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
