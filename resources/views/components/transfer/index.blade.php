@extends('layouts.app')

@section('title', 'Transferencias')

@section('content')
    <div class="dashboard">
        <div data-permissions="transfer.index" class="visible">
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
                                        <table id="TransferListTable"
                                            class="table table-striped table-sm table-centered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Estado</th>
                                                    <th>No Radicado</th>
                                                    <th>Asunto</th>
                                                    <th>Procedencia</th>
                                                    <th>Remitente</th>
                                                    <th style="width: 5%">Acciones</th>
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
    <!-- ========== VENTANA MODAL PARA VER DETALLES ========== -->
    <div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDocumentModalLabel">Detalles de la Correspondencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>No. Radicado:</strong> <span id="modal-reference-code"></span></p>
                            <p><strong>No. Sistema:</strong> <span id="modal-system-code"></span></p>
                            <p><strong>Fecha de Recepción:</strong> <span id="modal-received-date"></span></p>
                            <p><strong>Estado:</strong> <span id="modal-status"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Origen:</strong> <span id="modal-origin"></span></p>
                            <p><strong>Nombre del Remitente:</strong> <span id="modal-sender-name"></span></p>
                            <p><strong>¿Tiene Adjuntos?:</strong> <span id="modal-has-attachments"></span></p>
                            <p><strong>Número de Páginas:</strong> <span id="modal-page-count"></span></p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Asunto:</strong></p>
                    <p><span id="modal-subject"></span></p>
                    <hr>
                    <div class="text-center">
                        <a href="#" id="modal-file-link" target="_blank" class="btn btn-primary"><i
                                class="fas fa-file-pdf"></i> Ver Documento Adjunto</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== FIN DE LA VENTANA MODAL ========== -->

@endsection
@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';

        const TransferList = (() => {
            // Variables privadas del módulo
            let documents = [];
            let dataTable = null;

            const fetchDocuments = async () => {
                try {
                    const response = await HTTPService.get(`/api/dashboard/reception`);
                    documents = response.data || response;
                    console.log('Documentos obtenidos:', documents);
                    initDataTable();
                } catch (error) {
                    console.error('Error al obtener los documentos:', error);
                    Swal.fire('Error',
                        'Error al cargar los documentos. Por favor, intente de nuevo más tarde.',
                        'error');
                }
            };

            const initDataTable = () => {
                if (dataTable) {
                    dataTable.destroy();
                }

                dataTable = $('#TransferListTable').DataTable({
                    data: documents,
                    columns: [{
                            data: "document_status.status",
                            render: (data) =>
                                `<h5><span class="badge badge-outline-primary">${data}</span></h5>`
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
                            <a role="button" data-permissions="mailbox.create" class="action-icon export-btn" data-id="${data.id}">
                                <i class="far fa-exchange-alt"></i>
                            </a>
                            <a role="button" data-permissions="mailbox.show" class="action-icon view-btn" data-id="${data.id}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>`
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
                    },
                });

                handleTableEvents();
            };

            const handleTableEvents = () => {
                $('#TransferListTable').on('click', '.export-btn', (event) => {
                    const documentId = $(event.currentTarget).data('id');
                    const document = documents.find(doc => doc.id === documentId);
                    if (document) {
                        exportDocument(document);
                        window.location.href =
                            `/dashboard/ventanilla-unica/transferir-correspondencias/${documentId}`;
                    } else {
                        console.error('Documento no encontrado:', documentId);
                    }
                });

                $('#TransferListTable').on('click', '.view-btn', (event) => {
                    const documentId = $(event.currentTarget).data('id');
                    const document = documents.find(doc => doc.id === documentId);
                    if (document) {
                        // LLAMADA A LA FUNCIÓN ACTUALIZADA
                        viewDocument(document);
                    } else {
                        console.error('Documento no encontrado:', documentId);
                    }
                });
            };

            const viewDocument = (item) => {
                console.log('Viendo documento:', item);

                // 1. Llenar los campos de la modal con los datos del documento
                $('#modal-reference-code').text(item.reference_code || 'N/A');
                $('#modal-system-code').text(item.system_code || 'N/A');

                const receivedDate = new Date(item.received_date);
                const formattedDate = receivedDate.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                $('#modal-received-date').text(formattedDate);

                $('#modal-status').html(
                    `<span class="badge badge-outline-primary">${item.document_status.status || 'N/A'}</span>`
                );

                $('#modal-origin').text(item.origin || 'N/A');
                $('#modal-sender-name').text(item.sender_name || 'N/A');

                $('#modal-has-attachments').text(item.has_attachments ? 'Sí' : 'No');
                $('#modal-page-count').text(item.page_count || 'N/A');

                $('#modal-subject').text(item.subject || 'Sin asunto');

                // 2. Construir y asignar el enlace al archivo adjunto
                // Ahora `document` se refiere correctamente al DOM global del navegador.
                const filePath = item.file_path;
                const fileUrl = filePath ? `/${filePath}` : '#';
                const fileLink = document.getElementById('modal-file-link'); // <-- ESTO YA NO FALLARÁ

                if (fileLink) {
                    fileLink.setAttribute('href', fileUrl);
                    // Opcional: deshabilitar el botón si no hay archivo
                    if (!filePath) {
                        fileLink.classList.add('disabled');
                    } else {
                        fileLink.classList.remove('disabled');
                    }
                }

                // 3. Mostrar la ventana modal
                // `document` aquí es el DOM global, como debe ser.
                const modalElement = document.getElementById('viewDocumentModal'); // <-- ESTO YA NO FALLARÁ
                const viewModal = new bootstrap.Modal(modalElement);
                viewModal.show();
            };


            const exportDocument = (document) => {
                console.log('Exportando documento:', document.reference_code);
                // Implementa la lógica de exportación aquí
            };



            // Inicializar cuando el DOM esté listo
            $(document).ready(() => {
                fetchDocuments();
            });

            // API pública
            const publicApi = {
                fetchDocuments,
                handleTableEvents,
                exportDocument,
                viewDocument
            };

            // Exponer al objeto window para manejadores de eventos en línea
            window.DocumentManagement = publicApi;

            // Retornar API pública para importaciones de módulos
            return publicApi;
        })();
    </script>
@endsection
