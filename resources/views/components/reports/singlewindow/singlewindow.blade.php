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
                                    <h4 class="header-title">Reportes de Documentos y Préstamos</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Resumen de todas las solicitudes recibidas, procesadas y préstamos de documentos.
                                    </h5>
                                    <form id="reportForm">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="reportType" class="form-label">Tipo de Reporte</label>
                                                <select class="form-control" id="reportType" name="report_type" required>
                                                    <option value="">Seleccione...</option>
                                                    <optgroup label="Documentos">
                                                        <option value="resumen_general">RESUMEN GENERAL</option>
                                                        <option value="flujo_documentos">RECEPCIÓN</option>
                                                        <option value="tiempos_respuesta">TRANSFERENCIA</option>
                                                        <option value="transferencias_correspondencia">BUZON</option>
                                                        <option value="actividad_entidad">ENVÍO DE DOCUMENTO</option>
                                                        <option value="recepcion">FLUJO DE DOCUMENTOS</option>
                                                        <option value="respuesta_solicitud">TIEMPOS DE RESPUESTA</option>
                                                        <option value="estado_documentos">ESTADOS DEL DOCUMENTO</option>
                                                    </optgroup>
                                                    <optgroup label="Préstamos">
                                                        <option value="prestamos_activos">PRÉSTAMOS ACTIVOS</option>
                                                        <option value="prestamos_vencidos">PRÉSTAMOS VENCIDOS</option>
                                                        <option value="prestamos_por_fecha">PRÉSTAMOS POR FECHA</option>
                                                        <option value="prestamos_por_oficina">PRÉSTAMOS POR OFICINA</option>
                                                        <option value="prestamos_por_usuario">PRÉSTAMOS POR USUARIO</option>
                                                        <option value="devoluciones">DEVOLUCIONES</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="filterType" class="form-label">Tipo de Filtro</label>
                                                <select class="form-control" id="filterType" name="filter_type" required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="date_range">RANGO DE FECHAS</option>
                                                    {{--  <option value="year">AÑO</option> --}}
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3 year-field" style="display: none;">
                                                <label for="year" class="form-label">Año</label>
                                                <input type="number" class="form-control" id="year"
                                                    name="selected_year" min="2000" max="2099">
                                            </div>
                                            <div class="col-md-4 mb-3 date-range-field" style="display: none;">
                                                <label for="startDate" class="form-label">Fecha de Inicio</label>
                                                <input type="date" class="form-control" id="startDate" name="start_date">
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
                                                    <option value="recibida">Recibida</option>
                                                    <option value="procesando">Procesando</option>
                                                    <option value="contestado">Contestado</option>
                                                    <option value="archivado">Archivado</option>
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

@section('styles')
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-body {
            position: relative;
            padding: 1rem;
        }

        .loading-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
        }

        .btn-tool {
            margin-right: 0.5rem;
        }
    </style>
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        // Asumo que Helpers.js existe y contiene getMessage, si no, puedes eliminar la importación o simularla.
        // import Helpers from '/services/httpService/Helpers.js';

        const ReportComponent = (() => {
            let isGenerating = false;

            const elements = {};

            const getDOMElements = () => {
                elements.filterType = document.getElementById('filterType');
                elements.yearField = document.querySelector('.year-field');
                elements.dateRangeFields = document.querySelectorAll('.date-range-field');
                elements.departmentSelect = document.getElementById('department_id');
                elements.officeSelect = document.getElementById('office_id');
                elements.reportForm = document.getElementById('reportForm');
                elements.generateReportBtn = document.getElementById('generateReportBtn');
                elements.generatePdfBtn = document.getElementById('generatePdfBtn');
                elements.customReportResult = document.getElementById('customReportResult');
                elements.reportType = document.getElementById('reportType');
                elements.loadingIndicator = null;
            };

            /**
             * Determina el color del badge según el valor (con outline).
             * @param {string} value - El texto del valor a categorizar.
             * @returns {string} La clase CSS para el badge.
             */
            const getBadgeColor = (value) => {
                if (!value) return 'badge-outline-secondary';

                const lowerValue = value.toString().toLowerCase();

                // Para Tipo de Documento
                if (lowerValue.includes('oficio')) return 'badge-outline-primary';
                if (lowerValue.includes('memorandum') || lowerValue.includes('memorándum'))
                    return 'badge-outline-info';
                if (lowerValue.includes('circular')) return 'badge-outline-warning';
                if (lowerValue.includes('resolución')) return 'badge-outline-success';
                if (lowerValue.includes('nota')) return 'badge-outline-dark';

                // Para Estado
                if (lowerValue.includes('pendiente')) return 'badge-outline-warning';
                if (lowerValue.includes('aprobado') || lowerValue.includes('completado') || lowerValue.includes(
                        'finalizado')) return 'badge-outline-success';
                if (lowerValue.includes('rechazado') || lowerValue.includes('cancelado') || lowerValue.includes(
                        'anulado')) return 'badge-outline-danger';
                if (lowerValue.includes('en proceso') || lowerValue.includes('revisión'))
                    return 'badge-outline-info';
                if (lowerValue.includes('archivado')) return 'badge-outline-secondary';
                if (lowerValue.includes('urgente')) return 'badge-outline-danger';

                return 'badge-outline-primary';
            };

            /**
             * Muestra el indicador de carga y deshabilita los botones de acción.
             * @param {string} message - Mensaje a mostrar en el indicador de carga.
             */
            const showLoading = (message = 'Generando...') => {
                isGenerating = true;
                if (!elements.loadingIndicator) {
                    elements.loadingIndicator = document.createElement('div');
                    elements.loadingIndicator.className =
                        'loading-indicator text-center py-4 text-muted fs-5'; // Agregado estilos Bootstrap
                    elements.customReportResult.innerHTML = '';
                    elements.customReportResult.appendChild(elements.loadingIndicator);
                }
                elements.loadingIndicator.textContent = message;
                elements.loadingIndicator.style.display = 'block';

                if (elements.generateReportBtn) elements.generateReportBtn.disabled = true;
                if (elements.generatePdfBtn) elements.generatePdfBtn.disabled = true;
            };

            /**
             * Oculta el indicador de carga y habilita los botones de acción.
             */
            const hideLoading = () => {
                isGenerating = false;
                if (elements.loadingIndicator) {
                    elements.loadingIndicator.style.display = 'none';
                }
                if (elements.generateReportBtn) elements.generateReportBtn.disabled = false;
                if (elements.generatePdfBtn) elements.generatePdfBtn.disabled = false;
            };

            /**
             * Carga los departamentos y los popula en el selector.
             */
            const fetchDepartments = async () => {
                if (!elements.departmentSelect) return;
                try {
                    const result = await HTTPService.get('/api/dashboard/departments');
                    elements.departmentSelect.innerHTML =
                        '<option value="">Seleccione un Departamento</option>';
                    result.data.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.id;
                        option.textContent = department.name;
                        elements.departmentSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error al cargar departamentos:', error);
                    elements.customReportResult.innerHTML =
                        '<p class="text-danger">No se pudieron cargar los departamentos.</p>';
                }
            };

            /**
             * Carga las oficinas según el departamento seleccionado y las popula.
             * @param {string} departmentId - ID del departamento.
             */
            const fetchOffices = async (departmentId) => {
                if (!elements.officeSelect) return;
                try {
                    const offices = await HTTPService.get(
                        `/api/dashboard/office/show/offices/${departmentId}`);
                    elements.officeSelect.innerHTML = '<option value="">Todas</option>';
                    offices.forEach(office => {
                        const option = document.createElement('option');
                        option.value = office.id;
                        option.textContent = office.name;
                        elements.officeSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error al cargar oficinas:', error);
                    elements.officeSelect.innerHTML = '<option value="">Error al cargar oficinas</option>';
                }
            };

            /**
             * Maneja el envío del formulario para generar un reporte.
             * @param {Event} event - El evento de envío del formulario.
             */
            const generateReport = async (event) => {
                event.preventDefault();

                if (isGenerating || !elements.reportForm) {
                    return;
                }

                showLoading('Generando reporte...');

                const formData = new FormData(elements.reportForm);
                const formDataObject = Object.fromEntries(formData.entries());

                try {
                    let endpoint = '/api/single-window/reports/generate';


                    if (formDataObject.report_type && formDataObject.report_type.startsWith('prestamos_')) {
                        endpoint = '/api/reports/loans/generate';
                    }

                    const response = await HTTPService.post(endpoint, formDataObject);
                    console.log(response);

                    displayReportResult(response);
                } catch (error) {
                    console.error('Error al generar el reporte:', error);
                    elements.customReportResult.innerHTML =
                        `<p class="text-danger">Error al generar el reporte: ${error.message || 'Error desconocido'}.</p>`;
                } finally {
                    hideLoading();
                }
            };

            /**
             * Maneja la generación de un PDF.
             */
            const generatePdf = async () => {
                if (isGenerating || !elements.reportForm) {
                    return;
                }

                showLoading('Generando PDF...');

                const formData = new FormData(elements.reportForm);
                const formDataObject = Object.fromEntries(formData.entries());

                try {
                    let endpoint = '/api/single-window/reports/generate-pdf';

                    if (formDataObject.report_type && formDataObject.report_type.startsWith('prestamos_')) {
                        endpoint = '/api/reports/loans/generate-pdf';
                    }

                    const response = await HTTPService.postPdf(endpoint, formDataObject, {
                        responseType: 'blob'
                    });

                    const blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    const url = URL.createObjectURL(blob);
                    const newTab = window.open(url, '_blank');

                    if (!newTab || newTab.closed || typeof newTab.closed === 'undefined') {
                        alert(
                            'La nueva pestaña fue bloqueada por el navegador. Por favor, permite las ventanas emergentes para ver el PDF.'
                            );
                    }
                    setTimeout(() => URL.revokeObjectURL(url), 60000);
                } catch (error) {
                    console.error('Error al generar el PDF:', error);
                    let errorMessage = 'Error al generar el PDF';
                    if (error instanceof Error) {
                        errorMessage += `: ${error.message}`;
                    } else if (error.status && error.statusText) {
                        errorMessage += `: ${error.status} ${error.statusText}`;
                    }
                    alert(errorMessage);
                } finally {
                    hideLoading();
                }
            };

            // Se declara aquí para que sea accesible en showPdfModal
            let pdfModalInstance;

            /**
             * Crea y añade el modal de PDF al DOM si no existe, utilizando el HTML proporcionado.
             * @returns {HTMLElement} El elemento modal del PDF.
             */
            const createPdfModal = () => {
                let modal = document.getElementById(
                    'bs-example-modal-lg'); // Usamos el ID del modal proporcionado
                if (modal) return modal;

                modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'bs-example-modal-lg'; // ID del modal proporcionado
                modal.setAttribute('tabindex', '-1');
                modal.setAttribute('role', 'dialog');
                modal.setAttribute('aria-labelledby', 'myLargeModalLabel'); // Label del modal proporcionado
                modal.setAttribute('aria-hidden', 'true');

                // Insertamos el HTML del modal proporcionado, asegurándonos de tener un iframe dentro del body
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myLargeModalLabel">Visualizador de PDF</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                            </div>
                            <div class="modal-body">
                                <iframe id="pdfIframeContent" width="100%" height="500px" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                return modal;
            };


            /**
             * Muestra el modal con el PDF.
             * @param {string} pdfUrl - URL del PDF a mostrar.
             */
            const showPdfModal = (pdfUrl) => {
                if (!pdfModalInstance) {
                    pdfModalInstance = createPdfModal(); // Crea el modal la primera vez que se necesita
                }

                // El iframe ahora tiene el ID 'pdfIframeContent' dentro del modal 'bs-example-modal-lg'
                const iframe = pdfModalInstance.querySelector('#pdfIframeContent');
                if (iframe) {
                    iframe.src = pdfUrl.startsWith('/') ? pdfUrl : `/${pdfUrl}`;
                    // Usa la API de Bootstrap 5 para mostrar el modal
                    const modal = new bootstrap.Modal(pdfModalInstance);
                    modal.show();
                } else {
                    console.error('El iframe de contenido del PDF no se encontró dentro del modal.');
                }
            };

            /**
             * Crea una tabla HTML a partir de encabezados y datos.
             * @param {Array<string>} headers - Array de encabezados de la tabla.
             * @param {Array<Array<any>>} data - Array de filas de datos.
             * @returns {HTMLElement} El div contenedor de la tabla HTML creada.
             */
            const createTable = (headers, data) => {
                const table = document.createElement('table');
                // Añadido table-hover para interactividad. Las clases dt-responsive, nowrap, w-100 son comunes con DataTables.
                // Si DataTables se inicializa por separado, estas clases pueden ser añadidas por DataTables.
                table.className = 'table table-striped table-bordered table-hover w-100';

                const thead = document.createElement('thead');
                thead.classList.add(
                    'table-light'); // Fondo claro para el encabezado para mejor separación visual
                const headerRow = document.createElement('tr');
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    th.classList.add('text-center',
                        'align-middle'); // Centrar texto y alinear verticalmente
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    row.forEach((cell, index) => {
                        const td = document.createElement('td');
                        td.classList.add(
                            'align-middle'
                            ); // Alinear verticalmente el contenido de todas las celdas
                        const headerText = headers[index].toLowerCase();

                        if (headerText.includes('tipo') || headerText.includes('estado') ||
                            headerText.includes('status') || headerText.includes('state')) {
                            const badge = document.createElement('span');
                            const colorClass = getBadgeColor(cell);
                            badge.className = `badge ${colorClass}`;
                            badge.textContent = cell || 'N/A';
                            td.appendChild(badge);
                            td.classList.add('text-center'); // Centrar los badges
                        } else if (typeof cell === 'string' && cell.toLowerCase().endsWith(
                                '.pdf')) {
                            const button = document.createElement('button');
                            button.textContent = 'Ver PDF';
                            button.className = 'btn btn-sm btn-outline-primary';
                            button.onclick = () => showPdfModal(cell);
                            td.appendChild(button);
                            td.classList.add('text-center'); // Centrar el botón
                        } else if (Array.isArray(cell) && (headerText.includes('documentos') ||
                                headerText.includes('préstamos') || headerText.includes(
                                    'historial'))) {
                            const detailsTable = headerText.includes('préstamos') ?
                                createLoanDetailsTable(cell) : createDetailsTable(cell);
                            td.appendChild(detailsTable);
                        } else {
                            // Si la celda es null/undefined/cadena vacía, mostrar 'N/A' con texto atenuado e itálico
                            if (cell === null || cell === undefined || cell === '') {
                                const span = document.createElement('span');
                                span.textContent = 'N/A';
                                span.classList.add('text-muted', 'fst-italic');
                                td.appendChild(span);
                            } else {
                                td.textContent = cell;
                            }
                            // Alineación específica para ciertas columnas
                            if (headerText.includes('código')) {
                                td.classList.add('text-center');
                            } else if (headerText.includes('fecha')) {
                                td.classList.add('text-center');
                            }
                        }
                        tr.appendChild(td);
                    });
                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);

                // Envolver la tabla en un div responsivo
                const tableContainer = document.createElement('div');
                tableContainer.classList.add(
                    'table-responsive'); // Hace la tabla desplazable horizontalmente en pantallas pequeñas
                tableContainer.appendChild(table);

                return tableContainer; // Retornar el contenedor de la tabla
            };

            /**
             * Crea una tabla de detalles de documentos anidados.
             * @param {Array<Object>} details - Array de objetos de documentos.
             * @returns {HTMLTableElement} La tabla de detalles.
             */
            const createDetailsTable = (details) => {
                const table = document.createElement('table');
                table.className =
                    'table table-sm table-nested table-borderless my-0'; // Mejorado: sm, nested, borderless, no margin

                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Estado'].forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                details.forEach(doc => {
                    const tr = document.createElement('tr');
                    ['reference_code', 'received_date', 'sender_name', 'subject', 'status'].forEach(
                        key => {
                            const td = document.createElement('td');
                            if (key === 'subject' && doc[key] && typeof doc[key] === 'string' &&
                                doc[key].toLowerCase().endsWith('.pdf')) {
                                const button = document.createElement('button');
                                button.textContent = 'Ver PDF';
                                button.className = 'btn btn-sm btn-outline-primary';
                                button.onclick = () => showPdfModal(doc[key]);
                                td.appendChild(button);
                            } else if (key === 'status' && doc[key]) {
                                const badge = document.createElement('span');
                                const colorClass = getBadgeColor(doc[key]);
                                badge.className = `badge ${colorClass}`;
                                badge.textContent = doc[key] || 'N/A';
                                td.appendChild(badge);
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

            /**
             * Crea una tabla de detalles de préstamos anidados.
             * @param {Array<Object>} details - Array de objetos de préstamos.
             * @returns {HTMLTableElement} La tabla de detalles de préstamos.
             */
            const createLoanDetailsTable = (details) => {
                const table = document.createElement('table');
                table.className =
                    'table table-sm table-nested table-borderless my-0'; // Mejorado: sm, nested, borderless, no margin

                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                ['Tipo', 'Código', 'Descripción', 'Estado'].forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                details.forEach(item => {
                    const tr = document.createElement('tr');

                    const typeTd = document.createElement('td');
                    const typeBadge = document.createElement('span');
                    const typeColorClass = getBadgeColor(item.type);
                    typeBadge.className = `badge ${typeColorClass}`;
                    typeBadge.textContent = item.type || 'N/A';
                    typeTd.appendChild(typeBadge);
                    tr.appendChild(typeTd);

                    const codeTd = document.createElement('td');
                    codeTd.textContent = item.code || 'N/A';
                    tr.appendChild(codeTd);

                    const descTd = document.createElement('td');
                    descTd.textContent = item.description || 'N/A';
                    tr.appendChild(descTd);

                    const statusTd = document.createElement('td');
                    const statusBadge = document.createElement('span');
                    const statusColorClass = getBadgeColor(item.status);
                    statusBadge.className = `badge ${statusColorClass}`;
                    statusBadge.textContent = item.status || 'N/A';
                    statusTd.appendChild(statusBadge);
                    tr.appendChild(statusTd);

                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);

                return table;
            };

            /**
             * Muestra el resultado del reporte en el contenedor designado.
             * @param {Object} data - Los datos recibidos del reporte.
             */
            const displayReportResult = (data) => {
                if (!elements.customReportResult) return;

                // Limpia el contenido previo y establece la estructura de la tarjeta
                elements.customReportResult.innerHTML = `
                    <div class="card shadow-sm mt-4"> <div class="card-header bg-light"> <h3 class="card-title mb-0 fs-5">Resultado del Reporte:</h3> </div>
                        <div class="card-body p-4" id="reportCardBody"> </div>
                    </div>
                `;
                const reportCardBody = document.getElementById(
                    'reportCardBody'); // Obtener el elemento del cuerpo de la tarjeta recién creado

                if (data.error) {
                    reportCardBody.innerHTML +=
                        `<p class="alert alert-danger mb-0">Error: ${data.error}</p>`; // Usar alerta para errores
                    if (data.trace) {
                        console.error('Error trace:', data.trace);
                    }
                } else if (data.data) {
                    reportCardBody.innerHTML +=
                        `<h4 class="mb-4 text-primary">${data.report_type || data.tipo_reporte || 'Resumen General'}</h4>`; // Título del tipo de reporte con estilo
                    if (Array.isArray(data.data)) {
                        const tableContainer = createTable(data.headers || data.encabezados || [], data.data);
                        // table.classList.add('table-hover'); // Esta clase ya está dentro de createTable
                        reportCardBody.appendChild(
                            tableContainer); // Asegurarse de que el contenedor de la tabla se adjunte
                        // $(table).DataTable(); // Si usas DataTables, descomenta e inicializa aquí
                    } else if (typeof data.data === 'object') {
                        displayGeneralSummary(data.data,
                            reportCardBody); // Pasar el cuerpo de la tarjeta para adjuntar
                    } else {
                        reportCardBody.innerHTML +=
                            '<p class="text-muted">No se pudo generar el reporte. Formato de datos inesperado.</p>';
                    }
                } else {
                    reportCardBody.innerHTML +=
                        '<p class="text-muted">No se recibieron datos para el reporte.</p>';
                }
            };

            /**
             * Muestra el resumen general del reporte (tipo clave-valor).
             * @param {Object} data - El objeto de resumen.
             * @param {HTMLElement} targetElement - El elemento donde se añadirá el resumen.
             */
            const displayGeneralSummary = (data, targetElement) => {
                const summaryTable = document.createElement('table');
                summaryTable.className =
                    'table table-bordered table-striped mt-3'; // Agregado table-striped y margen superior

                for (const [key, value] of Object.entries(data)) {
                    const row = summaryTable.insertRow();
                    const keyCell = row.insertCell(0);
                    keyCell.textContent = key;
                    keyCell.classList.add('fw-bold', 'col-md-4'); // Texto clave en negrita y ancho relativo
                    keyCell.style.verticalAlign = 'middle'; // Alineación vertical

                    const valueCell = row.insertCell(1);
                    valueCell.classList.add('col-md-8'); // Celdas de valor con ancho relativo
                    valueCell.style.verticalAlign = 'middle'; // Alineación vertical

                    if (Array.isArray(value) && (key.includes("Documentos por estado") || key.includes(
                            "Ultimos documentos") || key.includes("Préstamos por estado") || key.includes(
                            "Últimos préstamos") || key.includes("Tipos de documentos"))) {
                        // Verifica si todos los elementos del array están vacíos o son N/A
                        const allItemsAreEmpty = value.length === 0 || value.every(item => {
                            if (typeof item !== 'object' || item === null) return true;
                            const relevantValues = Object.values(item).filter(val => typeof val ===
                                'string' || typeof val === 'number');
                            return relevantValues.length === 0 || relevantValues.every(val => val ===
                                null || val === undefined || val === '' || val === 'N/A' || (
                                    typeof val === 'number' && val === 0));
                        });

                        if (key.includes("Ultimos documentos") && allItemsAreEmpty) {
                            valueCell.innerHTML =
                                '<span class="text-muted fst-italic">No hay documentos recientes.</span>';
                        } else if (key.includes("Documentos por estado") || key.includes(
                                "Préstamos por estado") || key.includes("Tipos de documentos")) {
                            if (allItemsAreEmpty) {
                                valueCell.innerHTML =
                                    '<span class="text-muted fst-italic">No hay datos disponibles para esta categoría.</span>';
                            } else {
                                // Para "Documentos por estado", etc., usar una lista no ordenada para una mejor presentación
                                const subList = document.createElement('ul');
                                subList.className =
                                    'list-unstyled mb-0'; // Lista sin estilo por defecto y sin margen inferior
                                value.forEach(item => {
                                    const listItem = document.createElement('li');
                                    let itemText = 'N/A';
                                    const statusOrType = item.status || item.estado || item.type || item
                                        .tipo;
                                    const countOrQuantity = item.count || item.cantidad;

                                    if (statusOrType) {
                                        const colorClass = getBadgeColor(statusOrType);
                                        const badgeHtml =
                                            `<span class="badge ${colorClass} me-2">${statusOrType}</span>`; // Margen a la derecha para el badge
                                        if (countOrQuantity !== undefined && countOrQuantity !== null) {
                                            itemText =
                                                `${badgeHtml} <span class="fw-bold">${countOrQuantity}</span>`;
                                        } else {
                                            itemText = badgeHtml;
                                        }
                                    } else {
                                        itemText = JSON.stringify(
                                            item); // Fallback para elementos de array desconocidos
                                    }
                                    listItem.innerHTML = itemText;
                                    subList.appendChild(listItem);
                                });
                                valueCell.appendChild(subList);
                            }
                        } else {
                            // Para otros arrays donde una tabla anidada podría ser deseada
                            const subTable = document.createElement('table');
                            subTable.className = 'table table-sm table-borderless table-nested my-0';
                            value.forEach(item => {
                                const tr = subTable.insertRow();
                                Object.entries(item).forEach(([propKey, propValue]) => {
                                    const td = tr.insertCell();
                                    if (typeof propValue === 'string' && propValue.toLowerCase()
                                        .endsWith('.pdf')) {
                                        const button = document.createElement('button');
                                        button.textContent = 'Ver PDF';
                                        button.className = 'btn btn-sm btn-outline-primary';
                                        button.onclick = () => showPdfModal(propValue);
                                        td.appendChild(button);
                                    } else if (propKey.toLowerCase().includes('status') ||
                                        propKey.toLowerCase().includes('estado') || propKey
                                        .toLowerCase().includes('type') || propKey.toLowerCase()
                                        .includes('tipo')) {
                                        const badge = document.createElement('span');
                                        const colorClass = getBadgeColor(propValue);
                                        badge.className = `badge ${colorClass}`;
                                        badge.textContent = propValue || 'N/A';
                                        td.appendChild(badge);
                                    } else {
                                        td.textContent = propValue !== null && propValue !==
                                            undefined ? propValue : 'N/A';
                                    }
                                });
                            });
                            if (value.length > 0) {
                                valueCell.appendChild(subTable);
                            } else {
                                valueCell.innerHTML =
                                    '<span class="text-muted fst-italic">No hay detalles disponibles.</span>';
                            }
                        }

                    } else if (typeof value === 'object' && value !== null) {
                        // Para objetos que no son arrays pero son pares clave-valor
                        const subTable = document.createElement('table');
                        subTable.className = 'table table-sm table-borderless table-nested my-0';
                        for (const [subKey, subValue] of Object.entries(value)) {
                            const subRow = subTable.insertRow();
                            const subKeyCell = subRow.insertCell(0);
                            const subValueCell = subRow.insertCell(1);

                            const statusBadge = document.createElement('span');
                            const statusColorClass = getBadgeColor(subKey);
                            statusBadge.className = `badge ${statusColorClass} me-2`;
                            statusBadge.textContent = subKey;
                            subKeyCell.appendChild(statusBadge);
                            subValueCell.textContent = subValue !== null && subValue !== undefined ? subValue :
                                'N/A';
                        }
                        valueCell.appendChild(subTable);
                    } else if (typeof value === 'string' && value.toLowerCase().endsWith('.pdf')) {
                        const button = document.createElement('button');
                        button.textContent = 'Ver PDF';
                        button.className = 'btn btn-sm btn-outline-primary';
                        button.onclick = () => showPdfModal(value);
                        valueCell.appendChild(button);
                    } else {
                        // Texto o valor único, aplicar badge si es un estado
                        if (typeof value === 'string' && (value.toLowerCase().includes('pendiente') || value
                                .toLowerCase().includes('aprobado') || value.toLowerCase().includes(
                                    'rechazado') || value.toLowerCase().includes('completado') || value
                                .toLowerCase().includes('en proceso') || value.toLowerCase().includes(
                                    'archivado') || value.toLowerCase().includes('urgente'))) {
                            const badge = document.createElement('span');
                            const colorClass = getBadgeColor(value);
                            badge.className = `badge ${colorClass}`;
                            badge.textContent = value;
                            valueCell.appendChild(badge);
                        } else {
                            valueCell.textContent = value !== null && value !== undefined && value !== '' ?
                                value : 'No disponible';
                        }
                    }
                }

                targetElement.appendChild(summaryTable);
            };

            /**
             * Inicializa los listeners de eventos para los elementos del formulario.
             */
            const addEventListeners = () => {
                if (elements.filterType) {
                    elements.filterType.addEventListener('change', function() {
                        const isYearFilter = this.value === 'year';
                        if (elements.yearField) {
                            elements.yearField.style.display = isYearFilter ? 'block' : 'none';
                        }
                        elements.dateRangeFields.forEach(field => field.style.display = isYearFilter ?
                            'none' : 'block');
                    });
                    elements.filterType.dispatchEvent(new Event('change'));
                }

                if (elements.departmentSelect) {
                    elements.departmentSelect.addEventListener('change', function() {
                        if (this.value) {
                            fetchOffices(this.value);
                        } else {
                            if (elements.officeSelect) {
                                elements.officeSelect.innerHTML = '<option value="">Todas</option>';
                            }
                        }
                    });
                }

                if (elements.reportForm) {
                    elements.reportForm.addEventListener('submit', generateReport);
                }
                if (elements.generatePdfBtn) {
                    elements.generatePdfBtn.addEventListener('click', generatePdf);
                }
            };

            const init = () => {
                getDOMElements();
                // Inicializa el modal PDF aquí para que siempre esté en el DOM desde el principio.
                // Aunque no se muestre, su estructura ya estará disponible para ser usada.
                pdfModalInstance = createPdfModal();
                fetchDepartments();
                addEventListeners();
            };

            return {
                init: init
            };
        })();

        document.addEventListener('DOMContentLoaded', () => {
            ReportComponent.init();
        });
    </script>
@endsection
