@extends('layouts.app')

@section('title', 'Buzon de solicitudes')

@section('content')
    <div class="dashboard">
        <div data-permissions="mailbox.index" class="visible">

            <!-- end page title -->

            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title"> Buzon de Correspondencia</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Buzon de Correspondencia Recibidas Por Responder
                                    </h5>
                                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                        <li class="nav-item">
                                            <a href="#received" data-bs-toggle="tab" aria-expanded="true"
                                                class="nav-link rounded-0 active">
                                                <i class="mdi mdi-home-variant d-md-none d-block"></i>
                                                <span class="d-none d-md-block">Recibidos</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#answered" data-bs-toggle="tab" aria-expanded="false"
                                                class="nav-link rounded-0">
                                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                                <span class="d-none d-md-block">Contestados</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="table-responsive">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="received">
                                                <table id="receivedTable"
                                                    class="table table-striped dt-responsive nowrap w-100">
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
                                            <div class="tab-pane show" id="answered">
                                                <table id="answeredTable"
                                                    class="table table-striped dt-responsive nowrap w-100">
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
                                        </div>
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

        const Mailbox = (() => {
            // Private module variables
            let receivedDocuments = [];
            let answeredDocuments = [];
            let receivedDataTable = null;
            let answeredDataTable = null;

            const fetchReceivedDocuments = async () => {
                try {
                    const office_id = localStorage.getItem('user_office_id');
                    const response = await HTTPService.get(
                        `/api/dashboard/correspondence-transfer/office/${office_id}`);
                    receivedDocuments = response.data || response;
                    initReceivedDataTable();
                } catch (error) {
                    console.error('Error al obtener los documentos recibidos:', error);
                    Swal.fire('Error',
                        'Error al cargar los documentos recibidos. Por favor, intente de nuevo más tarde.',
                        'error');
                }
            };

            const fetchAnsweredDocuments = async () => {
                try {
                    const office_id = localStorage.getItem('user_office_id');

                    const response = await HTTPService.get(`/api/dashboard/mailbox/office/${office_id}`);
                    answeredDocuments = response.data || response;
                    initAnsweredDataTable();
                } catch (error) {
                    console.error('Error al obtener los documentos contestados:', error);
                    Swal.fire('Error',
                        'Error al cargar los documentos contestados. Por favor, intente de nuevo más tarde.',
                        'error');
                }
            };

            const initReceivedDataTable = () => {
                if (receivedDataTable) {
                    receivedDataTable.destroy();
                }

                receivedDataTable = $('#receivedTable').DataTable({
                    data: receivedDocuments,
                    columns: [{
                            data: "status",
                            render: () => `<span class="badge bg-warning">PROCESANDO</span>`
                        },
                        {
                            data: 'reference_code'
                        },
                        {
                            data: 'subject'
                        },
                        {
                            data: 'origin'
                        },
                        {
                            data: 'sender_name'
                        },
                        {
                            data: null,
                            render: (data) => `
                        <div class="table-action">
                            <a href="javascript:void(0);" class="action-icon edit-icon" data-id="${data.id}"><i class="fas fa-envelope-open-text"></i></a>
                        </div>`
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    rowId: "id",
                    responsive: true,
                    select: true,
                    language: {
                        processing: "Procesando...",
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ elementos",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ elementos",
                        infoEmpty: "Mostrando 0 a 0 de 0 elementos",
                        infoFiltered: "(filtrado de _MAX_ elementos en total)",
                        infoPostFix: "",
                        loadingRecords: "Cargando registros...",
                        zeroRecords: "No se encontraron registros",
                        emptyTable: "No hay datos disponibles en la tabla",
                        paginate: {
                            first: "Primero",
                            previous: "Anterior",
                            next: "Siguiente",
                            last: "Último"
                        },
                        aria: {
                            sortAscending: ": activar para ordenar la columna de manera ascendente",
                            sortDescending: ": activar para ordenar la columna de manera descendente"
                        }
                    },
                });

                handleReceivedTableEvents();
            };

            const initAnsweredDataTable = () => {
                if (answeredDataTable) {
                    answeredDataTable.destroy();
                }

                answeredDataTable = $('#answeredTable').DataTable({
                    data: answeredDocuments,
                    columns: [{
                            data: "status",
                            render: () => `<span class="badge bg-success">CONTESTADO</span>`
                        },
                        {
                            data: 'reference_code',
                            defaultContent: 'N/A'
                        },
                        {
                            data: 'subject',
                            defaultContent: 'N/A'
                        },
                        {
                            data: 'origin',
                            defaultContent: 'N/A'
                        },
                        {
                            data: 'sender_name'
                        },
                        {
                            data: null,
                            render: (data) => `
                        <div class="table-action">
                            <a href="javascript:void(0);" class="action-icon view-icon" data-id="${data.id}"><i class="mdi mdi-eye"></i></a>
                            <a href="javascript:void(0);" class="action-icon comment-icon" data-id="${data.id}"><i class="mdi mdi-comment-text-outline"></i></a>
                        </div>`
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    rowId: "id",
                    responsive: true,
                    select: true,
                    language: {
                        processing: "Procesando...",
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ elementos",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ elementos",
                        infoEmpty: "Mostrando 0 a 0 de 0 elementos",
                        infoFiltered: "(filtrado de _MAX_ elementos en total)",
                        infoPostFix: "",
                        loadingRecords: "Cargando registros...",
                        zeroRecords: "No se encontraron registros",
                        emptyTable: "No hay datos disponibles en la tabla",
                        paginate: {
                            first: "Primero",
                            previous: "Anterior",
                            next: "Siguiente",
                            last: "Último"
                        },
                        aria: {
                            sortAscending: ": activar para ordenar la columna de manera ascendente",
                            sortDescending: ": activar para ordenar la columna de manera descendente"
                        }
                    },
                });

                handleAnsweredTableEvents();
            };

            const handleReceivedTableEvents = () => {
                $('#receivedTable').off('click', '.edit-icon, .comment-icon');

                $('#receivedTable').on('click', '.edit-icon', (event) => {
                    event.preventDefault();
                    const documentId = $(event.currentTarget).data('id');
                    const document = receivedDocuments.find(doc => doc.id === documentId);
                    if (document) {
                        window.location.href =
                            `/dashboard/ventanilla-unica/mi-buzon/responder/${documentId}`;
                    } else {
                        console.error('Documento no encontrado:', documentId);
                    }
                });

                $('#receivedTable').on('click', '.comment-icon', (event) => {
                    event.preventDefault();
                    const documentId = $(event.currentTarget).data('id');
                    const document = receivedDocuments.find(doc => doc.id === documentId);
                    if (document) {
                        console.log('Abrir comentarios para:', documentId);
                        // Implementar lógica para abrir un modal de comentarios
                    } else {
                        console.error('Documento no encontrado:', documentId);
                    }
                });
            };

            const handleAnsweredTableEvents = () => {
                $('#answeredTable').off('click', '.view-icon, .comment-icon');

                $('#answeredTable').on('click', '.view-icon', (event) => {
                    event.preventDefault();
                    const documentId = $(event.currentTarget).data('id');
                    const document = answeredDocuments.find(doc => doc.id === documentId);
                    if (document) {
                        console.log('Ver detalle de la respuesta:', documentId);
                        // Implementar lógica para ver el detalle de la respuesta
                    } else {
                        console.error('Documento no encontrado:', documentId);
                    }
                });

                $('#answeredTable').on('click', '.comment-icon', (event) => {
                    event.preventDefault();
                    const documentId = $(event.currentTarget).data('id');
                    const document = answeredDocuments.find(doc => doc.id === documentId);
                    if (document) {
                        console.log('Abrir comentarios para:', documentId);
                        // Implementar lógica para abrir un modal de comentarios
                    } else {
                        console.error('Documento no encontrado:', documentId);
                    }
                });
            };

            // Initialize when the DOM is ready
            $(document).ready(() => {
                fetchReceivedDocuments();
                fetchAnsweredDocuments();
            });

            // Public API
            const publicApi = {
                fetchReceivedDocuments,
                fetchAnsweredDocuments,
                handleReceivedTableEvents,
                handleAnsweredTableEvents
            };

            // Expose to window object for inline event handlers
            window.DocumentManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
