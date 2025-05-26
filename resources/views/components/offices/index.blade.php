@extends('layouts.app')

@section('title', 'Oficinas')

@section('content')
    <div class="dashboard">
        <div data-permissions="offices.index">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-5">
                                            <h4 class="header-title">Listado de Oficinas</h4>
                                            <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Oficinas">
                                                Lista todas las oficinas registradas
                                            </h5>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="text-sm-end">
                                                <a href="oficina/crear" class="btn btn-primary rounded-pill btn-sm text-white me-0 btn-sm">
                                                    <i class="icon-download"></i> Crear oficina
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="officesTable"
                                               class="table table-striped table-sm table-centered dt-responsive nowrap w-10">
                                            <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Departamento</th>
                                                <th>Responsable</th>
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

        const OfficeListComponent = (() => {
            // Private variables
            let offices = [];
            let dataTable = null;

            const fetchOffices = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/offices');
                    offices = response.data || [];
                    initDataTable();
                } catch (error) {
                    console.error('Error al obtener las oficinas:', error);
                    Swal.fire('Error', 'Error al cargar las oficinas. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            const initDataTable = () => {
                if (dataTable) {
                    dataTable.destroy();
                }

                dataTable = $('#officesTable').DataTable({
                    data: offices,
                    columns: [
                        {data: 'code'},
                        {data: 'name'},
                        {data: 'department.name', defaultContent: 'N/A'},
                        {data: 'user.name', defaultContent: 'N/A'},
                        {
                            data: 'status',
                            render: function (data, type, row) {
                                const statusClass = data === 'ACTIVO' ? 'bg-success' : 'bg-success';
                                return `<span class="badge ${statusClass}">${data}</span>`;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                return `
                            <div class="table-action">
                                <a href="javascript:void(0);" data-permissions="offices.update" class="action-icon edit-icon" data-id="${row.id}"> <i class="uil uil-edit"></i></a>
                                <a href="javascript:void(0);" data-permissions="offices.destroy" class="action-icon delete-icon" data-id="${row.id}"> <i class="uil uil-trash-alt"></i></a>
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
                $('#officesTable').on('click', '.edit-icon', (event) => {
                    const officeId = $(event.currentTarget).data('id');
                    window.location.href = `/dashboard/oficina/actualizar/${officeId}`;
                });

                $('#officesTable').on('click', '.delete-icon', async (event) => {
                    const officeId = $(event.currentTarget).data('id');
                    console.log('Eliminar oficina:', officeId);

                    try {
                        await HTTPService.delete(`/api/dashboard/offices/${officeId}`);
                        // Recargar la tabla después de eliminar
                        fetchOffices();
                    } catch (error) {
                        console.error('Error al eliminar la oficina:', error);
                        Swal.fire('Error', 'Error al eliminar la oficina. Por favor, intente de nuevo más tarde.', 'error');
                    }
                });
            };

            const editOffice = (officeId) => {
                console.log('Editando oficina:', officeId);
                window.location.href = `/dashboard/oficina/actualizar/${officeId}`;
            };

            const deleteOffice = async (officeId) => {
                console.log('Eliminando oficina:', officeId);
                try {
                    await HTTPService.delete(`/api/dashboard/offices/${officeId}`);
                    fetchOffices();
                } catch (error) {
                    console.error('Error al eliminar la oficina:', error);
                    Swal.fire('Error', 'Error al eliminar la oficina. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            // Initialize when the DOM is ready
            $(document).ready(() => {
                fetchOffices();
            });

            // Public API
            const publicApi = {
                fetchOffices,
                handleTableEvents,
                editOffice,
                deleteOffice
            };

            // Expose to window object for inline event handlers
            window.OfficeManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
