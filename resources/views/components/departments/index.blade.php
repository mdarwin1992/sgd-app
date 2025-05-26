@extends('layouts.app')

@section('title', 'Departamentos')

@section('content')
    <div class="dashboard">
        <div data-permissions="departments.index">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-5">
                                            <h4 class="header-title">Listado de Departamentos</h4>
                                            <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate"
                                                title="Departamentos">
                                                Lista todos los departamentos registrados
                                            </h5>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="text-sm-end">
                                                <a href="departamento/crear"
                                                   class="btn btn-primary rounded-pill text-white me-0 btn-sm"><i
                                                        class="icon-download "></i> Crear Departamento</a>
                                            </div>
                                        </div><!-- end col-->
                                    </div>

                                    <div class="table-responsive">
                                        <table id="departmentsTable"
                                               class="table table-striped table-sm table-centered dt-responsive nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Entidad</th>
                                                <th>Estado</th>
                                                <th width="5%">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> <!-- end card-body -->
                            </div> <!-- end card -->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
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

        const DepartmentList = (() => {
            // Private variables
            let departments = [];
            let dataTable = null;

            const fetchDepartments = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/departments');
                    departments = response.data || [];
                    initDataTable();
                } catch (error) {
                    console.error('Error al obtener los departamentos:', error);
                    Swal.fire('Error', 'Error al cargar los departamentos. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            const initDataTable = () => {
                if (dataTable) {
                    dataTable.destroy();
                }

                dataTable = $('#departmentsTable').DataTable({
                    data: departments,
                    columns: [
                        {data: 'code'},
                        {data: 'name'},
                        {data: 'entity.name', defaultContent: 'N/A'},
                        {
                            data: 'status',
                            render: function (data, type, row) {
                                const statusClass = data === 'activo' ? 'bg-danger' : 'bg-success';
                                return `<span class="badge ${statusClass}">${data}</span>`;
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
                $('#departmentsTable').on('click', '.edit-icon', (event) => {
                    const departmentId = $(event.currentTarget).data('id');
                    editDepartment(departmentId);
                });

                $('#departmentsTable').on('click', '.delete-icon', (event) => {
                    const departmentId = $(event.currentTarget).data('id');
                    deleteDepartment(departmentId);
                });
            };

            const editDepartment = (departmentId) => {
                console.log('Editando departamento:', departmentId);
                window.location.href = `/dashboard/departamento/actualizar/${departmentId}`;
            };

            const deleteDepartment = async (departmentId) => {
                console.log('Eliminando departamento:', departmentId);
                try {
                    await HTTPService.delete(`/api/dashboard/departments/${departmentId}`);
                    Swal.fire('Éxito', 'Departamento eliminado correctamente.', 'success');
                    fetchDepartments();
                } catch (error) {
                    console.error('Error al eliminar el departamento:', error);
                    Swal.fire('Error', 'Error al eliminar el departamento. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            // Initialize when the DOM is ready
            $(document).ready(() => {
                fetchDepartments();
            });

            // Public API
            const publicApi = {
                fetchDepartments,
                handleTableEvents,
                editDepartment,
                deleteDepartment
            };

            // Expose to window object for inline event handlers
            window.DepartmentList = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
