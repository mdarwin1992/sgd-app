@extends('layouts.app')

@section('title', 'Prestamos documentales archivo historico')

@section('content')
    <div class="dashboard">
        <div data-permissions="transfer.index" class="visible">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title"> Prestamos documentales archivo historico</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Complete el formulario para registrar una nueva prestamos documentales del archivo
                                        central.
                                    </h5>
                                    <div class="row mb-2">
                                        <div class="col-xl-12">
                                            <div class="text-xl-end mt-xl-0 mt-1">
                                                <a role="button" href="/dashboard/prestamos-documental"
                                                    class="btn btn-sm btn btn-outline-info rounded-pill">
                                                    <i class="fas fa-undo-alt me-1"></i>Regresar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="centralArchive"
                                            class="table table-striped table-sm table-centered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Codigo del sistema</th>
                                                    <th>Nombre del documento</th>
                                                    <th>Oficina</th>
                                                    <th>Subserie</th>
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

    <!-- Large modal -->
    <div class="modal fade" id="bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Prestamos de docuemntos</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="AddForm" name="AddForm" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="address" class="form-label">Nor de identificación</label>
                                <input type="text" class="form-control" id="identification" name="identification"
                                    autocomplete="off" required>
                                <input type="hidden" class="form-control" id="historic_file_id" name="historic_file_id"
                                    autocomplete="off" required readonly>
                                <input type="hidden" class="form-control" id="type_of_document_borrowed"
                                    name="type_of_document_borrowed" value="2" autocomplete="off" required readonly>
                            </div>
                            <div class="col-md-9">
                                <label for="names" class="form-label">Nombres y Apellidos</label>
                                <input type="text" class="form-control" id="names" name="names" autocomplete="off"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="office_id" class="form-label">Tipo de Entidad</label>
                                <select class="form-select" id="office_id" name="office_id" required>
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione el tipo de entidad.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="return_date" class="form-label">Fecha de devolucioin</label>
                                <input type="date" class="form-control" id="return_date" name="return_date"
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="/dashboard/prestamos-documental/archivo-central"
                                class="btn btn-primary rounded-pill btn-tool me-2">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill" id="submitButton">
                                <i class="fas fa-check me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Modal -->
    <div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg rounded">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" id="standard-modalLabel">Información del Préstamo</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <!-- Nombre e Identificación en la misma línea -->
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong> <span id="loan-names"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Identificación:</strong> <span id="loan-identification"></span></p>
                            </div>
                            <!-- ID de Oficina y Fecha de Devolución en la misma línea -->
                            <div class="col-md-6">
                                <p><strong>ID de Oficina:</strong> <span id="loan-office-id"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fecha de Devolución:</strong> <span id="loan-return-date"></span></p>
                            </div>
                            <!-- Estado y Número de Orden en la misma línea -->
                            <div class="col-md-6">
                                <p><strong>Estado:</strong> <span class="badge badge-info-lighten" id="loan-state"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Número de Orden:</strong> <span id="loan-order-number"></span></p>
                            </div>
                            <!-- Solo ID de Archivo Central -->
                            <div class="col-12">
                                <p><strong>Archivo Central:</strong> <span id="loan-central-archive-id"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#info-alert-modal">Regresar Doc A.C.</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Info Alert Modal -->
    <div id="info-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="ri-question-line h1 text-warning"></i>
                        <h4 class="mt-2">¿Está seguro?</h4>
                        <p class="mt-3">¿Confirma que desea regresar el documento?</p>
                        <button type="button" class="btn btn-danger my-2" id="confirm-return">Sí, devolver</button>
                        <button type="button" class="btn btn-secondary my-2" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';


        let dataTable; // Variable global para la DataTable

        async function fetchEntities() {
            try {
                const response = await HTTPService.get('/api/dashboard/historical-archive');
                const archives = response.data || [];

                console.log(archives)


                // Llamar a initDataTable con los datos obtenidos
                initDataTable(archives)
            } catch (error) {
                console.error('Error al obtener las entidades:', error);
                Swal.fire('Error', 'Error al cargar las entidades. Por favor, intente de nuevo más tarde.', 'error');
            }
        }

        function initDataTable(archives) {
            if (dataTable) {
                dataTable.destroy(); // Destruye la tabla previa si ya existe
                $('#centralArchive').empty(); // Limpia el contenido de la tabla
            }

            dataTable = $('#centralArchive').DataTable({
                data: archives,
                columns: [{
                        data: 'filed'
                    },
                    {
                        data: 'document_reference'
                    },
                    {
                        data: 'office.name'
                    },
                    {
                        data: 'subseries.subseries_name'

                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (!row.historical_archive_loan || !row.historical_archive_loan
                                .document_loan) {
                                return `
                                <div class="table-action">
                                    <a href="javascript:void(0);" class="action-icon edit-icon" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#bs-example-modal-lg">
                                        <h5><span class="badge badge-outline-success"><i class="far fa-file-pdf"></i> DISPONIBLE</span></h5>
                                    </a>                                   
                                </div>
                            `;
                            } else {
                                if (row.historical_archive_loan.document_loan.state === 0) {
                                    return `
                                        <div class="table-action">
                                            <a href="javascript:void(0);" class="action-icon edit-icon" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#bs-example-modal-lg">
                                                <h5><span class="badge badge-outline-success"><i class="far fa-file-pdf"></i> DISPONIBLE</span></h5>
                                            </a>                                   
                                        </div>
                                    `;

                                } else {
                                    return `
                                        <div class="table-action">
                                            
                                            <a href="javascript:void(0);" class="action-icon loan-icon" data-loan="${row.historical_archive_loan.document_loans_order_number}" data-bs-toggle="modal" data-bs-target="#standard-modal">
                                                <h5><span class="badge badge-outline-warning"><i class="far fa-file-pdf"></i> ${Helpers.verifyLoan(row.historical_archive_loan.document_loan.state)}</span></h5>
                                            </a>                                   
                                        </div>
                                    `;
                                }

                            }
                        }
                    }
                ],
                responsive: true,
                rowId: "afId",
                language: {
                    sProcessing: "Procesando...",
                    sLengthMenu: "Mostrar _MENU_ registros",
                    sZeroRecords: "No se encontraron resultados",
                    sEmptyTable: "Ningún dato disponible en esta tabla",
                    sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                    sSearch: "Buscar:",
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
        }

        // Cargar departamentos en el select
        async function loadOffices() {
            try {
                const response = await HTTPService.get('/api/dashboard/offices');
                const offices = response.data;

                const select = document.getElementById('office_id');
                select.innerHTML = '<option value="">Seleccioné...</option>';

                offices.forEach(office => {
                    const option = document.createElement('option');
                    option.value = office.id;
                    option.textContent = office.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error al obtener los departamentos', error);
            }
        }

        $(document).on('click', '.edit-icon', function() {
            var archiveId = $(this).data('id'); // Obtiene el id del archivo desde el atributo data-id

            if (archiveId) {
                document.getElementById('historic_file_id').value = archiveId
            }
        });

        $(document).on('click', '.loan-icon', async function() {
            var loan = $(this).data('loan'); // Obtiene la cadena JSON del atributo data-loan

            try {
                const response = await HTTPService.get(`/api/dashboard/document-loan/${loan}/central-archive`);
                const loanData = response.data || {};
                console.log(loanData);


                // Rellena el modal con los datos obtenidos
                $('#loan-names').text(loanData.names || 'N/A');
                $('#loan-office-id').text(loanData.office.name || 'N/A');
                $('#loan-order-number').text(loanData.order_number || 'N/A');
                $('#loan-identification').text(loanData.identification || 'N/A');
                $('#loan-return-date').text(loanData.return_date || 'N/A');
                $('#loan-state').text(loanData.state ? 'DOCUMENTO EN PRESTAMO' : 'DOCUMENTO ENTREGADO');
                $('#loan-central-archive-id').text(loanData.historical_archive_loans.historic_file
                    .document_reference || 'N/A');

                // Muestra el modal
                $('#standard-modal').modal('show');
            } catch (error) {
                console.error("Error fetching loan data:", error);
            }
        });

        // Guardar datos del cliente
        async function save() {
            let identification = document.getElementById("identification").value;
            let names = document.getElementById("names").value;
            let office_id = document.getElementById("office_id").value;
            let historic_file_id = document.getElementById("historic_file_id").value;
            let return_date = document.getElementById("return_date").value;
            let type_of_document_borrowed = document.getElementById("type_of_document_borrowed").value;

            try {
                const response = await HTTPService.post('/api/dashboard/document-loan', {
                    identification,
                    names,
                    office_id,
                    historic_file_id,
                    return_date,
                    type_of_document_borrowed
                });

                Helpers.getMessage('Prestamo documental creada exitosamente', '/dashboard/prestamos-documental');

            } catch (error) {
                console.error('Error al almacenar el prestamo documental', error);
                alert("Hubo un error al registrar el prestamo documental.");
            }
        }

        // Validación del formulario
        $(document).ready(function() {
            $("#AddForm").validate({
                rules: {
                    identification: "required",
                    names: "required",
                    office_id: "required",
                    return_date: "required",
                },
                messages: {
                    identification: "Este campo es requerido",
                    names: "Este campo es requerido",
                    office_id: "Este campo es requerido",
                    return_date: "Este campo es requerido",
                },
                submitHandler: function(form) {
                    save();
                }
            });
        });


        // Llamar a la función para obtener datos y mostrar la tabla
        fetchEntities();
        loadOffices();
    </script>
@endsection
