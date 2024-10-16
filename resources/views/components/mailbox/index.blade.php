@extends('layouts.app')

@section('title', 'Buzon de solicitudes')

@section('content')
    <div class="dashboard">
        <div data-permissions="api.mailbox.index" class="visible">

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

        const TransferListComponent = HTTPService.createComponent({
            data: () => ({
                receivedDataTable: null,
                answeredDataTable: null,
            }),

            methods: {
                initDataTableAnswered() {
                    if (this.answeredDataTable) {
                        this.answeredDataTable.destroy();
                    }

                    const token = localStorage.getItem('token');
                    const officeId = localStorage.getItem('user_office_id');

                    if (!token || !officeId) {
                        console.error('Token de autenticación o ID de oficina no encontrado.');
                        $('#answeredTable').html('<div class="alert alert-danger">No se pudo inicializar la tabla. Falta información de autenticación.</div>');
                        return;
                    }

                    this.answeredDataTable = $('#answeredTable').DataTable({
                        autoWidth: false,
                        ajax: {
                            url: '/api/dashboard/mailbox/office',
                            type: 'GET',
                            dataType: 'json',
                            data: function (d) {
                                d.office_id = officeId;
                            },
                            dataSrc: function (json) {
                                return Array.isArray(json.data) ? json.data : [];
                            },
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('Authorization', "Bearer " + token);
                            },
                            error: function (xhr, status, error) {
                                let errorMessage = 'Ha ocurrido un error al cargar los datos.';

                                switch (xhr.status) {
                                    case 401:
                                        errorMessage = 'No autorizado. El token puede haber expirado o ser inválido.';
                                        break;
                                    case 403:
                                        errorMessage = 'Acceso prohibido. No tiene permisos para ver estos datos.';
                                        break;
                                    case 404:
                                        errorMessage = 'Recurso no encontrado. La URL puede ser incorrecta.';
                                        break;
                                    case 500:
                                        errorMessage = 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.';
                                        break;
                                    default:
                                        errorMessage = `Error en la solicitud: ${error}`;
                                }

                                $('#answeredTable').html(`<div class="alert alert-danger">${errorMessage}</div>`);
                            }
                        },
                        columns: [
                            {
                                data: "status",
                                render: function (data, type, row) {
                                    return type === "display" ?
                                        `<span class="badge bg-success">CONTESTADO</span>`
                                        : 'CONTESTADO';
                                },
                            },
                            {data: 'reference_code', defaultContent: 'N/A'},
                            {data: 'subject', defaultContent: 'N/A'},
                            {data: 'origin', defaultContent: 'N/A'},
                            {
                                data: 'sender_name',
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return `
                        <div class="table-action">
                            <a href="javascript:void(0);" class="action-icon view-icon"><i class="mdi mdi-eye"></i></a>
                            <a href="javascript:void(0);" class="action-icon comment-icon"><i class="mdi mdi-comment-text-outline"></i></a>
                        </div>
                    `;
                                }
                            }
                        ],
                        order: [[0, 'desc']],
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
                        drawCallback: () => {
                            this.handleTableAnsweredEvents();
                        }
                    });
                },

                initDataTable() {
                    if (this.receivedDataTable) {
                        this.receivedDataTable.destroy();
                    }

                    const token = localStorage.getItem('token');
                    const officeId = localStorage.getItem('user_office_id');

                    if (!token || !officeId) {
                        console.error('Token de autenticación o ID de oficina no encontrado.');
                        return;
                    }

                    this.receivedDataTable = $('#receivedTable').DataTable({
                        autoWidth: false,
                        ajax: {
                            url: '/api/dashboard/correspondence-transfer/office',
                            type: 'GET',
                            dataType: 'json',
                            data: function (d) {
                                d.office_id = officeId;
                            },
                            dataSrc: function (json) {
                                return Array.isArray(json.data) ? json.data : [];
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
                                data: "status",
                                render: function (data, type, row) {
                                    return type === "display" ?
                                        `<span class="badge bg-warning">PROCESANDO</span>`
                                        : 'PROCESANDO';
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
                                    <a href="javascript:void(0);" class="action-icon edit-icon"><i class="mdi mdi-square-edit-outline"></i></a>
                                    <a href="javascript:void(0);" class="action-icon comment-icon"><i class="mdi mdi-comment-text-outline"></i></a>
                                </div>
                            `;
                                }
                            }
                        ],
                        order: [[0, 'desc']],
                        rowId: "id",
                        responsive: true,
                        select: true,
                        language: {
                            // Opciones de idioma (mantener como estaban)
                        },
                        drawCallback: () => {
                            this.handleTableEvents();
                        }
                    });
                },

                handleTableEvents() {
                    $('#receivedTable').off('click', '.edit-icon, .comment-icon');

                    $('#receivedTable').on('click', '.edit-icon', (event) => {
                        event.preventDefault();
                        const rowData = this.receivedDataTable.row($(event.currentTarget).closest('tr')).data();
                        window.location.href = `/dashboard/mi-buzon/responder/${rowData.id}`;
                    });

                    $('#receivedTable').on('click', '.comment-icon', (event) => {
                        event.preventDefault();
                        const rowData = this.receivedDataTable.row($(event.currentTarget).closest('tr')).data();
                        console.log('Abrir comentarios para:', rowData.id);
                        // Implementar lógica para abrir un modal de comentarios
                    });
                },

                handleTableAnsweredEvents() {
                    $('#answeredTable').off('click', '.view-icon, .comment-icon');

                    $('#answeredTable').on('click', '.view-icon', (event) => {
                        event.preventDefault();
                        const rowData = this.answeredDataTable.row($(event.currentTarget).closest('tr')).data();
                        // Implementar lógica para ver el detalle de la respuesta
                        console.log('Ver detalle de la respuesta:', rowData.id);
                    });

                    $('#answeredTable').on('click', '.comment-icon', (event) => {
                        event.preventDefault();
                        const rowData = this.answeredDataTable.row($(event.currentTarget).closest('tr')).data();
                        console.log('Abrir comentarios para:', rowData.id);
                        // Implementar lógica para abrir un modal de comentarios
                    });
                }
            },

            elements: {
                receivedTable: '#receivedTable',
                answeredTable: '#answeredTable',
            },

            created() {
                // No es necesario fetchBusinesses aquí porque DataTables se encarga de obtener los datos
            },

            render() {
                this.initDataTable();
                this.initDataTableAnswered();
            }
        });

        // Inicializar el componente cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            TransferListComponent.init();
        });

        /*  const TransferListComponent = HTTPService.createComponent({
              data: () => ({
                  receivedDataTable: null,
                  answeredDataTable: null,
              }),

              methods: {
                  initDataTableAnswered() {
                      // Destruir la instancia existente de DataTable si existe
                      if (this.answeredDataTable) {
                          this.answeredDataTable.destroy();
                      }

                      // Verificar si el token está disponible en localStorage
                      const token = localStorage.getItem('token');
                      if (!token) {
                          console.error('Token de autenticación no encontrado.');
                          return;
                      }

                      // Inicializar DataTable con la configuración personalizada
                      this.answeredDataTable = $('#answeredTable').DataTable({
                          autoWidth: false,
                          ajax: {
                              url: '/api/dashboard/mailbox',
                              type: 'GET',
                              dataType: 'json',
                              dataSrc: function (json) {
                                  console.log(json);
                                  if (Array.isArray(json.data)) {
                                      return json.data;
                                  }
                                  return [];
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
                                  data: null,
                                  render: function (data, type, row) {
                                      return type === "display" ?
                                          `<span class="badge bg-primary">Respondido</span>`
                                          : 'Respondido';
                                  },
                              },
                              {data: 'document_id', defaultContent: 'N/A'},
                              {data: 'response_content', defaultContent: 'N/A'},
                              {data: 'response_email', defaultContent: 'N/A'},
                              {
                                  data: 'response_document_path',
                                  render: function (data, type, row) {
                                      return data ? 'Disponible' : 'No disponible';
                                  }
                              },
                              {
                                  data: null,
                                  render: function (data, type, row) {
                                      return `
      <div class="table-action">
          <a href="javascript:void(0);" class="action-icon comment-icon"><i class="uil uil-comment-alt-lines font-18"></i></a>
      </div>
      `;
                                  }
                              }
                          ],
                          rowId: "id",
                          responsive: true,
                          select: true,
                          language: {
                              // ... (mantener las opciones de idioma como estaban)
                          },
                          drawCallback: () => {
                              this.handleTableAnsweredEvents();
                          }
                      });
                  },

                  initDataTable() {
                      // Destruir la instancia existente de DataTable si existe
                      if (this.receivedDataTable) {
                          this.receivedDataTable.destroy();
                      }

                      // Verificar si el token está disponible en localStorage
                      const token = localStorage.getItem('token');
                      if (!token) {
                          console.error('Token de autenticación no encontrado.');
                          return;
                      }

                      // Inicializar DataTable con la configuración personalizada
                      this.receivedDataTable = $('#receivedTable').DataTable({
                          autoWidth: false,
                          ajax: {
                              url: '/api/dashboard/correspondence-transfer',
                              type: 'GET',
                              dataType: 'json',
                              dataSrc: function (json) {
                                  if (Array.isArray(json.data)) {
                                      return json.data;
                                  }
                                  return [];
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
                                  data: "document.document_status.status",
                                  render: function (data, type, row) {
                                      return type === "display" ?
                                          `<span class="badge bg-primary">${data}</span>`
                                          : data;
                                  },
                              },
                              {data: 'document.reference_code'},
                              {data: 'document.subject'},
                              {data: 'document.origin'},
                              {data: 'document.sender_name'},
                              {
                                  data: null,
                                  render: function (data, type, row) {
                                      return `
                                      <div class="table-action">
                                          <a href="javascript:void(0);" class="action-icon edit-icon"> <i class="uil-file-redo-alt"></i></a>
                                          <a href="javascript:void(0);" class="action-icon comment-icon"><i class="uil uil-comment-alt-lines font-18"></i></a>
                                      </div>
                                      `;
                                  }
                              }
                          ],
                          rowId: "id",
                          responsive: true,
                          select: true,
                          language: {
                              // ... (mantener las opciones de idioma como estaban)
                          },
                          drawCallback: () => {
                              this.handleTableEvents();
                          }
                      });
                  },

                  handleTableEvents() {
                      // Remover los eventos anteriores para evitar duplicados
                      $('#receivedTable').off('click', '.edit-icon, .comment-icon');

                      // Agregar nuevos event listeners
                      $('#receivedTable').on('click', '.edit-icon', (event) => {
                          event.preventDefault();
                          const rowData = this.receivedDataTable.row($(event.currentTarget).closest('tr')).data();
                          window.location.href = `/dashboard/mi-buzon/responder/${rowData.id}`;
                      });

                      $('#receivedTable').on('click', '.comment-icon', (event) => {
                          event.preventDefault();
                          const rowData = this.receivedDataTable.row($(event.currentTarget).closest('tr')).data();
                          console.log('Abrir comentarios para:', rowData.id);
                          // Aquí puedes implementar la lógica para abrir un modal de comentarios
                      });
                  },

                  handleTableAnsweredEvents() {
                      // Remover los eventos anteriores para evitar duplicados
                      $('#answeredTable').off('click', '.edit-icon, .comment-icon');

                      // Agregar nuevos event listeners
                      $('#answeredTable').on('click', '.edit-icon', (event) => {
                          event.preventDefault();
                          const rowData = this.answeredDataTable.row($(event.currentTarget).closest('tr')).data();
                          window.location.href = `/dashboard/mi-buzon/responder/${rowData.id}`;
                      });

                      $('#answeredTable').on('click', '.comment-icon', (event) => {
                          event.preventDefault();
                          const rowData = this.answeredDataTable.row($(event.currentTarget).closest('tr')).data();
                          console.log('Abrir comentarios para:', rowData.id);
                          // Aquí puedes implementar la lógica para abrir un modal de comentarios
                      });
                  }
              },

              elements: {
                  receivedTable: '#receivedTable',
                  answeredTable: '#answeredTable',
              },

              created() {
                  // No necesitamos fetchBusinesses aquí porque DataTables se encargará de obtener los datos
              },

              render() {
                  this.initDataTable();
                  this.initDataTableAnswered();
              }
          });*/

        // Inicializar el componente cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            TransferListComponent.init();
        });

    </script>
@endsection
