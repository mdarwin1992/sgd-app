@extends('layouts.app')

@section('title', 'Prestamos documental')

@section('content')
    <div class="dashboard">
        <div data-permissions="dashboard.page" class="visible">
            <div class="row pt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Prestamos Documentales</h4>
                            <p class="text-muted font-14">
                                Complete el formulario para registrar una nueva prestamos documentales.
                            </p>
                            <div class="row mb-2">
                                <div class="col-sm-5">
                                    <a href="/dashboard/prestamos-documental/archivo-central"
                                        class="btn btn-outline-primary rounded-pill mb-2 me-1"><i
                                            class="mdi mdi-plus-circle me-2"></i>Docuemntos archivo central</a>
                                    <a href="/dashboard/prestamos-documental/archivo-historico"
                                        class="btn btn-outline-info  rounded-pill mb-2"><i
                                            class="mdi mdi-plus-circle me-2"></i> Docuemntos
                                        archivo histórico</a>
                                </div>
                                <div class="col-sm-7">
                                    <div class="text-sm-end">
                                        <button type="button" class="btn btn-outline-success rounded-pill mb-2 me-1"
                                            data-bs-toggle="modal" data-bs-target="#standard-modal">Regresar Doc A.C. -
                                            A.H</button>
                                    </div>
                                </div><!-- end col-->
                            </div>
                            <div class="row mb-2">
                                <div class="col-xl-12">
                                    <div class="text-xl-end mt-xl-0 mt-1">
                                        <form class="row g-3">
                                            <div class="col-auto">
                                                <label for="startDate" class="visually-hidden">Fecha de Inicio:</label>
                                                <input type="date" id="startDate" class="form-control">
                                            </div>
                                            <div class="col-auto">
                                                <label for="endDate" class="visually-hidden">Fecha de Fin:</label>
                                                <input type="date" id="endDate" class="form-control">
                                            </div>
                                            <div class="col-auto">
                                                <label for="orderNumber" class="visually-hidden">Número de
                                                    Orden:</label>
                                                <input type="text" id="orderNumber" class="form-control"
                                                    placeholder="Número de Orden">
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" id="filterButton"
                                                    class="btn btn-outline-primary mb-3">Filtrar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="table-responsive">
                                    <table id="centralArchive"
                                        class="table table-striped table-sm table-centered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>Fecha de registro</th>
                                                <th>Nor Orden</th>
                                                <th>Nor Documento</th>
                                                <th>Nombre del prestador</th>
                                                <th>Referencia documental</th>
                                                <th>Tipo de documento</th>
                                                <th style="width: 5%">Estado</th>
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

    <div class="modal fade" id="bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Información del Préstamo</h4>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <a href="#" class="btn btn-primary" id="ticket" target="_blank">Regresar Doc</a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Standard modal -->
    <div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">Regresar documento prestado</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="AddForm">
                        <div class="mb-3">
                            <label for="order_number" class="form-label">Número de orden</label>
                            <select class="form-select js-example-basic-single" name="order_number" id="order_number"
                                required>
                                <option selected>Abrir este menú de selección</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="document_conditions" class="form-label">Condiciones del documento</label>
                            <textarea class="form-control" id="document_conditions" name="document_conditions"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="comments" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="comments" name="comments"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
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
                const response = await HTTPService.get('/api/dashboard/document-loan');
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
                        data: 'registration_date',
                        render: function(data) {
                            return new Date(data).toLocaleDateString(); // Formatea la fecha para mostrarla
                        }
                    },
                    {
                        data: 'order_number'
                    },
                    {
                        data: 'identification'
                    },
                    {
                        data: 'names'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.type_of_document_borrowed == 1) {
                                return `
                                    ${row.central_archive_loans.central_archive.document_reference}
                                `;
                            } else {
                                return `
                                    ${row.historical_archive_loans.historic_file.document_reference}
                                `;
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.type_of_document_borrowed == 1) {
                                return `
                                    <div class="table-action">
                                        <a href="javascript:void(0);" class="action-icon">
                                            <h5><span class="badge badge-outline-info"> ARCHIVO CENTRAL</span></h5>
                                        </a>
                                    </div>
                                `;
                            } else {
                                return `
                                    <div class="table-action">
                                        <a href="javascript:void(0);" class="action-icon">
                                            <h5><span class="badge badge-outline-primary"> ARCHIVO HISTORICO</span></h5>
                                        </a>
                                    </div>
                                `;
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                            <div class="table-action">
                                <a href="javascript:void(0);" class="action-icon edit-icon" data-loan="${row.order_number}">
                                    <h5><span class="badge ${Helpers.verifyDeliveryStyle(row.state)}"><i class="far fa-file-pdf"></i> ${Helpers.verifyDelivery(row.state)} </span></h5>
                                </a>
                            </div>
                        `;
                        }
                    },
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

        $(document).on('click', '.edit-icon', async function() {

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

                if (loanData.type_of_document_borrowed == 1) {
                    $('#loan-central-archive-id').text(loanData.central_archive_loans.central_archive
                        .document_reference || 'N/A');
                } else {
                    $('#loan-central-archive-id').text(loanData.historical_archive_loans.historic_file
                        .document_reference || 'N/A');
                }

                // Muestra el modal
                $('#bs-example-modal-lg').modal('show');

                let enlace = document.getElementById("ticket");

                // Modificar el href con el valor
                enlace.href = `/reportes/tiquete/${loanData.order_number}`;


            } catch (error) {
                console.error("Error fetching loan data:", error);
            }
        });

        // Función para filtrar por fechas y número de orden
        $('#filterButton').on('click', function() {
            var startDate = $('#startDate').val() ? new Date($('#startDate').val()) : null;
            var endDate = $('#endDate').val() ? new Date($('#endDate').val()) : null;
            var orderNumber = $('#orderNumber').val();

            dataTable.rows().every(function() {
                var rowDate = new Date(this.data().registration_date).getTime();
                var rowOrderNumber = this.data().order_number;

                var dateMatch = true;
                var orderNumberMatch = true;

                if (startDate && endDate) {
                    var startTimestamp = startDate.getTime();
                    var endTimestamp = endDate.getTime();
                    dateMatch = rowDate >= startTimestamp && rowDate <= endTimestamp;
                }

                if (orderNumber) {
                    orderNumberMatch = rowOrderNumber === orderNumber;
                }

                if ((!startDate && !endDate) || dateMatch) {
                    if ((!orderNumber) || orderNumberMatch) {
                        this.node().style.display = '';
                    } else {
                        this.node().style.display = 'none';
                    }
                } else {
                    this.node().style.display = 'none';
                }
            });
        });

        async function loadLending() {
            try {
                const response = await HTTPService.get('/api/document-loans/order-number');
                const loanData = response.data;

                const select = document.getElementById('order_number');
                select.innerHTML = '<option value="">Seleccione un departamento</option>';

                loanData.forEach(loan => {
                    const option = document.createElement('option');
                    option.value = loan.order_number;
                    option.textContent = loan.order_number;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error al obtener los departamentos', error);
            }
        }

        // Guardar datos del cliente
        async function save() {
            let order_number = document.getElementById("order_number").value;
            let document_conditions = document.getElementById("document_conditions").value;
            let comments = document.getElementById("comments").value;

            try {
                const response = await HTTPService.post('/api/document-loans/return', {
                    order_number,
                    document_conditions,
                    comments,
                });

                // Helpers.getMessage('La deboucion creada exitosamente', '/dashboard/prestamos-documental');

            } catch (error) {
                console.error('Error al almacenar ea deboucion', error);
                alert("Hubo un error al registrar ea deboucion.");
            }
        }

        // Validación del formulario
        $(document).ready(function() {
            $("#AddForm").validate({
                rules: {
                    order_number: "required",
                },
                messages: {
                    order_number: "Este campo es requerido",
                },
                submitHandler: function(form) {
                    save();
                }
            });
        });

        fetchEntities();
        loadLending();
    </script>
@endsection
