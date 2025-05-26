@extends('layouts.app')

@section('title', 'Generar Reporte')

@section('content')
    <div class="dashboard">
        <div data-permissions="reports.singlewindow" class="visible">
            <div class="row pt-3">
                <div class="col-xl-12 col-lg-12">
                    <div class="card card-h-100">
                        <div class="card-body pt-0">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h4 class="header-title"> Reporte general ventañilla unica</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Resumen de todas las solicitudes recibidas y procesadas, incluyendo su estado y
                                        tiempos de respuesta. Evalúa la eficiencia del servicio.
                                    </h5>
                                    <form id="reportForm">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="reportType" class="form-label">Tipo de Reporte</label>
                                                <select class="form-control" id="reportType" name="report_type"
                                                        required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="resumen_general">RESUMEN GENERAL</option>
                                                    <option value="flujo_documentos">RECEPCIÓN</option>
                                                    <option value="tiempos_respuesta">TRANSFERENCIA</option>
                                                    <option value="transferencias_correspondencia">BUZON</option>
                                                    <option value="actividad_entidad">ENVÍO DE DOCUMENTO</option>
                                                    <option value="recepcion">FLUJO DE DOCUMENTOS</option>
                                                    <option value="respuesta_solicitud">TIEMPOS DE RESPUESTA</option>
                                                    <option value="envio_documento">ESTADO DE DOCUMENTOS</option>
                                                    <option value="estado_documentos">ESTADOS DEL DOCUMENTO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="filterType" class="form-label">Tipo de Filtro</label>
                                                <select class="form-control" id="filterType" name="filter_type"
                                                        required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="date_range">RANGO DE FECHAS</option>
                                                    <option value="year">AÑO</option>

                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3 year-field" style="display: none;">
                                                <label for="year" class="form-label">Año</label>
                                                <input type="number" class="form-control" id="year" name="year"
                                                       min="2000" max="2099">
                                            </div>
                                            <div class="col-md-4 mb-3 date-range-field" style="display: none;">
                                                <label for="startDate" class="form-label">Fecha de Inicio</label>
                                                <input type="date" class="form-control" id="startDate"
                                                       name="start_date">
                                            </div>
                                            <div class="col-md-4 mb-3 date-range-field" style="display: none;">
                                                <label for="endDate" class="form-label">Fecha de Fin</label>
                                                <input type="date" class="form-control" id="endDate" name="end_date">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="status" class="form-label">Estado (opcional)</label>
                                                <select class="form-control" id="status_id" name="status_id">
                                                    <option value="">Todos</option>
                                                    <option value="RECIBIDA">Recibida</option>
                                                    <option value="PROCESANDO">Procesando</option>
                                                    <option value="CONTESTADO">Contestado</option>
                                                    <option value="ARCHIVADO">Archivado</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="department_id" class="form-label">Departamento
                                                    (opcional)</label>
                                                <select class="form-control" id="department_id" name="department_id">
                                                    <option value="">Todos</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="office_id" class="form-label">Oficina (opcional)</label>
                                                <select class="form-control" id="office_id" name="office_id">
                                                    <option value="">Todas</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-warning rounded-pill btn-tool me-2"
                                                    id="generatePdfBtn">Generate PDF
                                            </button>
                                            <a href="/dashboard" class="btn btn-primary rounded-pill btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success rounded-pill"
                                                    id="submitButton">
                                                <i class="fas fa-check me-1"></i> Generar Reporte
                                            </button>
                                        </div>
                                    </form>


                                    <div id="customReportResult" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';

        const ReportComponent = (() => {
            // Private variables
            let isGenerating = false;
            let isSubmitting = false;

            const elements = {
                filterType: document.getElementById('filterType'),
                yearField: document.querySelector('.year-field'),
                dateRangeFields: document.querySelectorAll('.date-range-field'),
                departmentSelect: document.getElementById('department_id'),
                officeSelect: document.getElementById('office_id'),
                reportForm: '#reportForm',
                customReportResult: document.getElementById('customReportResult'),
                generatePdfBtn: document.getElementById('generatePdfBtn')
            };

            // Private methods
            const fetchDepartments = async () => {
                try {
                    const result = await HTTPService.get('/api/dashboard/departments');
                    result.data.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.id;
                        option.textContent = department.name;
                        elements.departmentSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error al cargar departamentos:', error);
                }
            };

            const fetchOffices = async (departmentId) => {
                try {
                    const data = await HTTPService.get(`/api/dashboard/office/show/offices/${departmentId}`);
                    elements.officeSelect.innerHTML = '<option value="">Todas</option>';
                    data.forEach(office => {
                        const option = document.createElement('option');
                        option.value = office.id;
                        option.textContent = office.name;
                        elements.officeSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error al cargar oficinas:', error);
                }
            };

            const generateReport = async () => {
                event.preventDefault();

                if (isSubmitting) {
                    return;
                }

                isSubmitting = true;

                const form = document.querySelector(elements.reportForm);
                const formData = new FormData(form);
                const formDataObject = Object.fromEntries(formData);

                try {
                    const response = await HTTPService.post('/api/single-window/reports/generate', formDataObject);
                    console.log(response)

                    displayReportResult(response);
                } catch (error) {
                    console.error('Error:', error);
                    elements.customReportResult.innerHTML = `<p class="text-danger">Error al generar el reporte: ${error.message}</p>`;
                } finally {
                    isSubmitting = false;
                }


            };

            const generatePdf = async () => {
                if (isSubmitting) {
                    return;
                }

                isSubmitting = true;

                const form = document.querySelector(elements.reportForm);
                const formData = new FormData(form);
                const formDataObject = Object.fromEntries(formData);

                try {
                    const response = await HTTPService.postPdf('/api/single-window/reports/generate-pdf', formDataObject, {
                        responseType: 'blob' // Indica que esperamos una respuesta binaria
                    });

                    // Crear un Blob con la respuesta
                    const blob = new Blob([response], {type: 'application/pdf'});

                    // Crear una URL del blob
                    const url = URL.createObjectURL(blob);

                    // Abrir el PDF en una nueva pestaña
                    const newTab = window.open(url, '_blank');

                    // Si el navegador bloquea la apertura de la pestaña, mostrar una alerta
                    if (!newTab || newTab.closed || typeof newTab.closed === 'undefined') {
                        alert('La nueva pestaña fue bloqueada. Permite las ventanas emergentes para ver el PDF.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    let errorMessage = 'Error generating PDF';
                    if (error.message) {
                        errorMessage += ': ' + error.message;
                    }
                    alert(errorMessage);
                } finally {
                    isSubmitting = false;
                }
            };

            /* const createTable = (headers, data) => {
                 const table = document.createElement('table');
                 table.className = 'table table-striped dt-responsive nowrap w-100';

                 const thead = document.createElement('thead');
                 const headerRow = document.createElement('tr');
                 headers.forEach(header => {
                     const th = document.createElement('th');
                     th.textContent = header;
                     headerRow.appendChild(th);
                 });
                 thead.appendChild(headerRow);
                 table.appendChild(thead);

                 const tbody = document.createElement('tbody');
                 data.forEach(row => {
                     const tr = document.createElement('tr');
                     row.forEach((cell, index) => {
                         const td = document.createElement('td');
                         if (index === 3 && Array.isArray(cell)) { // Detalles de Documentos
                             const detailsTable = createDetailsTable(cell);
                             td.appendChild(detailsTable);
                         } else {
                             td.textContent = cell;
                         }
                         tr.appendChild(td);
                     });
                     tbody.appendChild(tr);
                 });
                 table.appendChild(tbody);

                 return table;
             };

             const createDetailsTable = (details) => {
                 const table = document.createElement('table');
                 table.className = 'table table-sm';

                 const thead = document.createElement('thead');
                 const headerRow = document.createElement('tr');
                 ['Código', 'Fecha Recibido', 'Remitente', 'Asunto'].forEach(header => {
                     const th = document.createElement('th');
                     th.textContent = header;
                     headerRow.appendChild(th);
                 });
                 thead.appendChild(headerRow);
                 table.appendChild(thead);

                 const tbody = document.createElement('tbody');
                 details.forEach(doc => {
                     const tr = document.createElement('tr');
                     ['reference_code', 'received_date', 'sender_name', 'subject'].forEach(key => {
                         const td = document.createElement('td');
                         td.textContent = doc[key] || 'N/A';
                         tr.appendChild(td);
                     });
                     tbody.appendChild(tr);
                 });
                 table.appendChild(tbody);

                 return table;
             };

             const displayGeneralSummary = (data) => {
                 const summaryTable = document.createElement('table');
                 summaryTable.className = 'table table-bordered';

                 for (const [key, value] of Object.entries(data)) {
                     const row = summaryTable.insertRow();
                     const keyCell = row.insertCell(0);
                     keyCell.textContent = key;
                     keyCell.style.fontWeight = 'bold';

                     const valueCell = row.insertCell(1);
                     if (key === "Documentos por estado" || key === "Ultimos documentos") {
                         const subTable = document.createElement('table');
                         subTable.className = 'table table-sm';

                         if (Array.isArray(value)) {
                             // Para "Ultimos documentos"
                             value.forEach(item => {
                                 const subRow = subTable.insertRow();
                                 subRow.insertCell(0).textContent = item.status;
                                 subRow.insertCell(1).textContent = item.count;
                             });
                         } else {
                             // Para "Documentos por estado"
                             for (const [subKey, subValue] of Object.entries(value)) {
                                 const subRow = subTable.insertRow();
                                 subRow.insertCell(0).textContent = subKey;
                                 subRow.insertCell(1).textContent = subValue;
                             }
                         }
                         valueCell.appendChild(subTable);
                     } else {
                         valueCell.textContent = value !== null && value !== undefined ? value : 'No disponible';
                     }
                 }

                 elements.customReportResult.appendChild(summaryTable);
             };

             const displayReportResult = (data) => {
                 elements.customReportResult.innerHTML = '<h3>Resultado del Reporte:</h3>';
                 if (data.error) {
                     elements.customReportResult.innerHTML += `<p class="text-danger">${data.error}</p>`;
                     if (data.trace) {
                         console.error('Error trace:', data.trace);
                     }
                 } else if (data.data) {
                     elements.customReportResult.innerHTML += `<h4>${data.report_type}</h4>`;
                     if (Array.isArray(data.data)) {
                         const table = createTable(data.headers, data.data);
                         elements.customReportResult.appendChild(table);
                     } else if (typeof data.data === 'object') {
                         displayGeneralSummary(data.data);
                     } else {
                         elements.customReportResult.innerHTML += '<p>No se pudo generar el reporte. Formato de datos inesperado.</p>';
                     }
                 } else {
                     elements.customReportResult.innerHTML += '<p>No se recibieron datos para el reporte.</p>';
                 }
             };*/

            // Crear una única instancia de la modal
            const createPdfModal = () => {
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'pdfModal';
                modal.setAttribute('tabindex', '-1');
                modal.setAttribute('role', 'dialog');
                modal.setAttribute('aria-labelledby', 'pdfModalLabel');
                modal.setAttribute('aria-hidden', 'true');

                modal.innerHTML = `
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Ver PDF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfIframe" width="100%" height="500px" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    `;

                document.body.appendChild(modal);
                return modal;
            };

// Crear la instancia de la modal una vez
            const pdfModal = createPdfModal();

// Función para mostrar la modal con un PDF específico
            const showPdfModal = (pdfUrl) => {
                const iframe = document.getElementById('pdfIframe');
                iframe.src = pdfUrl;
                $(pdfModal).modal('show');
            };

// Función para cerrar la modal
            const closePdfModal = () => {
                $(pdfModal).modal('hide');
            };

            const createTable = (headers, data) => {
                const table = document.createElement('table');
                table.className = 'table table-striped dt-responsive nowrap w-100';

                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    row.forEach((cell, index) => {
                        const td = document.createElement('td');
                        if (index === 3 && Array.isArray(cell)) { // Detalles de Documentos
                            const detailsTable = createDetailsTable(cell);
                            td.appendChild(detailsTable);
                        } else if (typeof cell === 'string' && cell.toLowerCase().endsWith('.pdf')) {
                            const button = document.createElement('button');
                            button.textContent = 'Ver';
                            button.className = 'btn btn-sm btn-outline-primary';
                            button.onclick = () => showPdfModal(cell);
                            td.appendChild(button);
                        } else {
                            td.textContent = cell;
                        }
                        tr.appendChild(td);
                    });
                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);

                return table;
            };

            const createDetailsTable = (details) => {
                const table = document.createElement('table');
                table.className = 'table table-sm';

                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                ['Código', 'Fecha Recibido', 'Remitente', 'Asunto'].forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                details.forEach(doc => {
                    const tr = document.createElement('tr');
                    ['reference_code', 'received_date', 'sender_name', 'subject'].forEach(key => {
                        const td = document.createElement('td');
                        if (key === 'subject' && doc[key].toLowerCase().endsWith('.pdf')) {
                            const button = document.createElement('button');
                            button.textContent = 'Ver';
                            button.className = 'btn btn-sm btn-outline-primary';
                            button.onclick = () => showPdfModal(doc[key]);
                            td.appendChild(button);
                        } else {
                            td.textContent = doc[key] || 'N/A';
                        }
                        tr.appendChild(td);
                    });
                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);

                return table;
            };

            const displayGeneralSummary = (data) => {
                const summaryTable = document.createElement('table');
                summaryTable.className = 'table table-bordered';

                for (const [key, value] of Object.entries(data)) {
                    const row = summaryTable.insertRow();
                    const keyCell = row.insertCell(0);
                    keyCell.textContent = key;
                    keyCell.style.fontWeight = 'bold';

                    const valueCell = row.insertCell(1);
                    if (key === "Documentos por estado" || key === "Ultimos documentos") {
                        const subTable = document.createElement('table');
                        subTable.className = 'table table-sm';

                        if (Array.isArray(value)) {
                            // Para "Ultimos documentos"
                            value.forEach(item => {
                                const subRow = subTable.insertRow();
                                subRow.insertCell(0).textContent = item.status;
                                subRow.insertCell(1).textContent = item.count;
                            });
                        } else {
                            // Para "Documentos por estado"
                            for (const [subKey, subValue] of Object.entries(value)) {
                                const subRow = subTable.insertRow();
                                subRow.insertCell(0).textContent = subKey;
                                subRow.insertCell(1).textContent = subValue;
                            }
                        }
                        valueCell.appendChild(subTable);
                    } else if (typeof value === 'string' && value.toLowerCase().endsWith('.pdf')) {
                        const button = document.createElement('button');
                        button.textContent = 'Ver';
                        button.className = 'btn btn-sm btn-outline-primary';
                        button.onclick = () => showPdfModal(value);
                        valueCell.appendChild(button);
                    } else {
                        valueCell.textContent = value !== null && value !== undefined ? value : 'No disponible';
                    }
                }

                elements.customReportResult.appendChild(summaryTable);
            };

            const displayReportResult = (data) => {
                elements.customReportResult.innerHTML = '<h3>Resultado del Reporte:</h3>';
                if (data.error) {
                    elements.customReportResult.innerHTML += `<p class="text-danger">${data.error}</p>`;
                    if (data.trace) {
                        console.error('Error trace:', data.trace);
                    }
                } else if (data.data) {
                    elements.customReportResult.innerHTML += `<h4>${data.report_type}</h4>`;
                    if (Array.isArray(data.data)) {
                        const table = createTable(data.headers, data.data);
                        elements.customReportResult.appendChild(table);
                    } else if (typeof data.data === 'object') {
                        displayGeneralSummary(data.data);
                    } else {
                        elements.customReportResult.innerHTML += '<p>No se pudo generar el reporte. Formato de datos inesperado.</p>';
                    }
                } else {
                    elements.customReportResult.innerHTML += '<p>No se recibieron datos para el reporte.</p>';
                }
            };

// Agregar un botón para cerrar la modal manualmente si es necesario
            const addCloseModalButton = () => {
                const closeButton = document.createElement('button');
                closeButton.textContent = 'Cerrar PDF';
                closeButton.className = 'btn btn-secondary mt-3';
                closeButton.onclick = closePdfModal;
                elements.customReportResult.appendChild(closeButton);
            };

// Llamar a esta función después de displayReportResult si se desea agregar el botón de cierre
// addCloseModalButton();

            // Initialize when DOM is ready
            $(document).ready(() => {
                fetchDepartments();
                // Set up event listeners
                elements.filterType.addEventListener('change', function () {
                    const isYearFilter = this.value === 'year';
                    elements.yearField.style.display = isYearFilter ? 'block' : 'none';
                    elements.dateRangeFields.forEach(field => field.style.display = isYearFilter ? 'none' : 'block');
                });

                elements.departmentSelect.addEventListener('change', function () {
                    if (this.value) {
                        fetchOffices(this.value);
                    } else {
                        elements.officeSelect.innerHTML = '<option value="">Todas</option>';
                    }
                });

                document.querySelector(elements.reportForm).addEventListener('submit', generateReport);


                elements.generatePdfBtn.addEventListener('click', function () {
                    generatePdf();
                });
            });

            // Create public API
            const publicApi = {
                generateReport,
                generatePdf,
            };

            // Expose to window object for inline event handlers
            window.ReportComponent = publicApi;

            // Return public API for module imports
            return publicApi;
        })();

    </script>
@endsection
