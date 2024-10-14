@extends('layouts.app')

@section('title', 'Transferir Correspondencia')

@section('content')
    <div class="dashboard">
        <div data-permissions="api.reception.index" class="visible">

            <!-- end page title -->

            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title"> Transferir Correspondencia</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Lista todas la recepciones registradas
                                    </h5>

                                    <div class="table-responsive">
                                        <table id="example" class="table table-striped dt-responsive nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>Estado</th>
                                                <th>No Radicado</th>
                                                <th>Asunto</th>
                                                <th>Procedencia</th>
                                                <th>Remitente</th>
                                                <th></th>
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

        const TransferListComponent = HTTPService.createComponent({
            data: () => ({
                dataTable: null,
            }),

            methods: {
                initDataTable() {
                    if (this.dataTable) {
                        this.dataTable.destroy();
                    }

                    const token = localStorage.getItem('token');
                    if (!token) {
                        console.error('Token de autenticación no encontrado.');
                        return;
                    }

                    this.dataTable = $('#example').DataTable({
                        autoWidth: false,
                        ajax: {
                            url: '/api/dashboard/reception',
                            type: 'GET',
                            dataType: 'json',
                            dataSrc: function (json) {
                                console.log(json.data);
                                return json.data || [];
                            },
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('Authorization', "Bearer " + token);
                            },
                            error: function (xhr, status, error) {
                                if (xhr.status === 401) {
                                    console.error('No autorizado. El token puede haber expirado o ser inválido.');
                                } else {
                                    console.error('Error en la solicitud:', error);
                                }
                            }
                        },
                        columns: [
                            {
                                data: "document_status.status",
                                render: function (data, type, row) {
                                    return type === "display" ?
                                        `<span class="badge bg-primary">${data}</span>`
                                        : data;
                                },
                            },
                            {data: 'reference_code'},
                            {data: 'subject'},
                            {data: 'origin'},
                            {data: 'sender_name'},
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return `
                            <div class="table-action">
                                <a href="javascript:void(0);" class="action-icon"> <i class="uil-file-redo-alt"></i></a>
                                <a href="javascript:void(0);" class="action-icon"> <i class="uil uil-comment-alt-lines font-18"></i></a>
                            </div>`;
                                }
                            }
                        ],
                        rowId: "id",
                        responsive: true,
                        select: true,
                        language: {
                            sProcessing: "Procesando...",
                            sLengthMenu: "Mostrar _MENU_ registros",
                            sZeroRecords: "No se encontraron resultados",
                            sEmptyTable: "Ningún dato disponible en esta tabla",
                            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                            sSearch: "Buscar:",
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
                },

                handleTableEvents() {
                    $('#example').on('click', '.action-icon', (event) => {
                        event.preventDefault();
                        const $icon = $(event.currentTarget).find('i');
                        const action = $icon.hasClass('uil-comment-alt-lines') ? 'comment' : 'edit';
                        const rowData = this.dataTable.row($(event.currentTarget).closest('tr')).data();

                        switch (action) {
                            case 'comment':
                                console.log('Abrir comentarios para:', rowData.id);
                                // Implement logic for opening comments modal
                                break;
                            case 'edit':
                                window.location.href = `/dashboard/transferir-correspondencias/${rowData.id}`;
                                break;
                        }
                    });
                }
            },

            elements: {
                businessTable: '#example',
            },

            created() {
                // DataTables will handle data fetching
            },

            render() {
                this.initDataTable();
                this.handleTableEvents();
            }
        });

        // Inicializar el componente cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            TransferListComponent.init();
        });

    </script>
@endsection

