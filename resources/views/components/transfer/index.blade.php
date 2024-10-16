@extends('layouts.app')

@section('title', 'Transferencias')

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
                documents: [],
                dataTable: null,
            }),

            methods: {
                async fetchDocuments() {
                    try {
                        const token = localStorage.getItem('token');
                        if (!token) {
                            console.error('Token de autenticación no encontrado.');
                            return;
                        }

                        const response = await fetch('/api/dashboard/reception', {
                            method: 'GET',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Content-Type': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();
                        this.documents = data.data || [];
                        this.initDataTable();
                    } catch (error) {
                        console.error('Error al obtener documentos:', error);
                    }
                },

                initDataTable() {
                    if (this.dataTable) {
                        this.dataTable.destroy();
                    }

                    this.dataTable = $('#example').DataTable({
                        data: this.documents,
                        columns: [
                            {
                                data: "document_status.status",
                                render: (data) => `<span class="badge bg-primary">${data}</span>`
                            },
                            {data: 'reference_code'},
                            {data: 'subject'},
                            {data: 'origin'},
                            {data: 'sender_name'},
                            {
                                data: null,
                                render: (data) => `
                            <div class="table-action">
                                <a  role="button" class="action-icon export-btn" data-id="${data.id}">
                                    <i class="fas fa-file-export"></i>
                                </a>
                                <a role="button" class="action-icon view-btn" data-id="${data.id}">
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

                    this.handleTableEvents();
                },

                handleTableEvents() {
                    $('#example').on('click', '.export-btn', (event) => {
                        const documentId = $(event.currentTarget).data('id');
                        const document = this.documents.find(doc => doc.id === documentId);
                        if (document) {
                            this.exportDocument(document);
                            window.location.href = `/dashboard/transferir-correspondencias/${documentId}`;
                        } else {
                            console.error('Documento no encontrado:', documentId);
                        }
                    });

                    $('#example').on('click', '.view-btn', (event) => {
                        const documentId = $(event.currentTarget).data('id');
                        const document = this.documents.find(doc => doc.id === documentId);
                        if (document) {
                            this.viewDocument(document);
                        } else {
                            console.error('Documento no encontrado:', documentId);
                        }
                    });
                },

                exportDocument(document) {
                    console.log('Exportando documento:', document.reference_code);
                    // Implementa la lógica de exportación aquí
                },

                viewDocument(document) {
                    console.log('Viendo documento:', document.reference_code);
                    // Implementa la lógica de visualización aquí
                }
            },

            created() {
                this.fetchDocuments();
            },

            render() {
                // La tabla se inicializa en fetchDocuments() -> initDataTable()
            }
        });

        // Inicializar el componente cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            TransferListComponent.init();
        });


    </script>
@endsection

