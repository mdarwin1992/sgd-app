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
                generatePdfBtn: document.getElementById('generatePdfBtn'),
                reportType: document.getElementById('reportType')
            };

            // Función para determinar el color del badge según el valor (con outline)
            const getBadgeColor = (value) => {
                if (!value) return 'outline-secondary';

                const lowerValue = value.toString().toLowerCase();

                // Para Tipo de Documento
                if (lowerValue.includes('oficio')) return 'outline-primary';
                if (lowerValue.includes('memorandum') || lowerValue.includes('memorándum'))
                    return 'outline-info';
                if (lowerValue.includes('circular')) return 'outline-warning';
                if (lowerValue.includes('resolución')) return 'outline-success';
                if (lowerValue.includes('nota')) return 'outline-dark';

                // Para Estado
                if (lowerValue.includes('pendiente')) return 'outline-warning';
                if (lowerValue.includes('aprobado') || lowerValue.includes('completado') || lowerValue.includes(
                        'finalizado')) return 'outline-success';
                if (lowerValue.includes('rechazado') || lowerValue.includes('cancelado') || lowerValue.includes(
                        'anulado')) return 'outline-danger';
                if (lowerValue.includes('en proceso') || lowerValue.includes('revisión')) return 'outline-info';
                if (lowerValue.includes('archivado')) return 'outline-secondary';
                if (lowerValue.includes('urgente')) return 'outline-danger';

                // Valor por defecto
                return 'badge-outline-primary';
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
                    const data = await HTTPService.get(
                        `/api/dashboard/office/show/offices/${departmentId}`);
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

            const generateReport = async (event) => {
                event.preventDefault();

                if (isSubmitting) {
                    return;
                }

                isSubmitting = true;
                elements.customReportResult.innerHTML =
                    '<div class="loading-indicator">Generando reporte...</div>';

                const form = document.querySelector(elements.reportForm);
                const formData = new FormData(form);
                const formDataObject = Object.fromEntries(formData);

                try {
                    let endpoint = '/api/single-window/reports/generate';

                    // Cambiar el endpoint según el tipo de reporte
                    if (formDataObject.report_type.startsWith('prestamos_')) {
                        endpoint = '/api/reports/loans/generate';
                    }

                    const response = await HTTPService.post(endpoint, formDataObject);
                    displayReportResult(response);
                } catch (error) {
                    console.error('Error:', error);
                    elements.customReportResult.innerHTML =
                        `<p class="text-danger">Error al generar el reporte: ${error.message}</p>`;
                } finally {
                    isSubmitting = false;
                }
            };

            const generatePdf = async () => {
                if (isSubmitting) {
                    return;
                }

                isSubmitting = true;
                elements.customReportResult.innerHTML =
                    '<div class="loading-indicator">Generando PDF...</div>';

                const form = document.querySelector(elements.reportForm);
                const formData = new FormData(form);
                const formDataObject = Object.fromEntries(formData);

                try {
                    let endpoint = '/api/single-window/reports/generate-pdf';

                    // Cambiar el endpoint según el tipo de reporte
                    if (formDataObject.report_type.startsWith('prestamos_')) {
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
                            'La nueva pestaña fue bloqueada. Permite las ventanas emergentes para ver el PDF.'
                        );
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

            const pdfModal = createPdfModal();

            const showPdfModal = (pdfUrl) => {
                const iframe = document.getElementById('pdfIframe');
                iframe.src = pdfUrl;
                $(pdfModal).modal('show');
            };

            const closePdfModal = () => {
                $(pdfModal).modal('hide');
            };

            const createTable = (headers, data) => {
                const table = document.createElement('table');
                table.className = 'table table-striped table-bordered dt-responsive nowrap w-100';

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

                        // Aplicar badge para columnas de Tipo o Estado
                        const headerText = headers[index].toLowerCase();
                        if (headerText.includes('tipo') || headerText.includes('estado') ||
                            headerText.includes('status') || headerText.includes('state')) {
                            const badge = document.createElement('span');
                            const colorClass = getBadgeColor(cell);
                            badge.className = `badge ${colorClass}`;
                            badge.textContent = cell || 'N/A';
                            td.appendChild(badge);
                        } else if (index === 3 && Array.isArray(cell)) {
                            const detailsTable = createDetailsTable(cell);
                            td.appendChild(detailsTable);
                        } else if (typeof cell === 'string' && cell.toLowerCase().endsWith(
                                '.pdf')) {
                            const button = document.createElement('button');
                            button.textContent = 'Ver';
                            button.className = 'btn btn-sm btn-outline-primary';
                            button.onclick = () => showPdfModal(cell);
                            td.appendChild(button);
                        } else {
                            td.textContent = cell || 'N/A';
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
                            if (key === 'subject' && doc[key] && doc[key].toLowerCase().endsWith(
                                    '.pdf')) {
                                const button = document.createElement('button');
                                button.textContent = 'Ver';
                                button.className = 'btn btn-sm btn-outline-primary';
                                button.onclick = () => showPdfModal(doc[key]);
                                td.appendChild(button);
                            } else if ((key === 'status' || key === 'state') && doc[key]) {
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

            const createLoanDetailsTable = (details) => {
                const table = document.createElement('table');
                table.className = 'table table-sm';

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

                    // Tipo de documento
                    const typeTd = document.createElement('td');
                    const typeBadge = document.createElement('span');
                    const typeColorClass = getBadgeColor(item.type);
                    typeBadge.className = `badge ${typeColorClass}`;
                    typeBadge.textContent = item.type || 'N/A';
                    typeTd.appendChild(typeBadge);
                    tr.appendChild(typeTd);

                    // Código
                    const codeTd = document.createElement('td');
                    codeTd.textContent = item.code || 'N/A';
                    tr.appendChild(codeTd);

                    // Descripción
                    const descTd = document.createElement('td');
                    descTd.textContent = item.description || 'N/A';
                    tr.appendChild(descTd);

                    // Estado
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

            const displayGeneralSummary = (data) => {
                const summaryTable = document.createElement('table');
                summaryTable.className = 'table table-bordered';

                for (const [key, value] of Object.entries(data)) {
                    const row = summaryTable.insertRow();
                    const keyCell = row.insertCell(0);
                    keyCell.textContent = key;
                    keyCell.style.fontWeight = 'bold';

                    const valueCell = row.insertCell(1);
                    if (key === "Documentos por estado" || key === "Ultimos documentos" ||
                        key === "Préstamos por estado" || key === "Últimos préstamos") {
                        const subTable = document.createElement('table');
                        subTable.className = 'table table-sm';

                        if (Array.isArray(value)) {
                            value.forEach(item => {
                                const subRow = subTable.insertRow();

                                // Celda de estado con badge
                                const statusCell = subRow.insertCell(0);
                                const statusBadge = document.createElement('span');
                                const statusColorClass = getBadgeColor(item.status || item.estado);
                                statusBadge.className = `badge ${statusColorClass}`;
                                statusBadge.textContent = item.status || item.estado || 'N/A';
                                statusCell.appendChild(statusBadge);

                                subRow.insertCell(1).textContent = item.count || item.cantidad || 'N/A';
                            });
                        } else {
                            for (const [subKey, subValue] of Object.entries(value)) {
                                const subRow = subTable.insertRow();

                                // Celda de estado con badge
                                const statusCell = subRow.insertCell(0);
                                const statusBadge = document.createElement('span');
                                const statusColorClass = getBadgeColor(subKey);
                                statusBadge.className = `badge ${statusColorClass}`;
                                statusBadge.textContent = subKey;
                                statusCell.appendChild(statusBadge);

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
                    } else if (key === "Documentos prestados" || key === "Historial de préstamos") {
                        const loanTable = createLoanDetailsTable(value);
                        valueCell.appendChild(loanTable);
                    } else {
                        // Aplicar badge si el valor parece ser un estado
                        if (typeof value === 'string' && (
                                value.toLowerCase().includes('pendiente') ||
                                value.toLowerCase().includes('aprobado') ||
                                value.toLowerCase().includes('rechazado') ||
                                value.toLowerCase().includes('completado'))) {
                            const badge = document.createElement('span');
                            const colorClass = getBadgeColor(value);
                            badge.className = `badge ${colorClass}`;
                            badge.textContent = value;
                            valueCell.appendChild(badge);
                        } else {
                            valueCell.textContent = value !== null && value !== undefined ? value :
                                'No disponible';
                        }
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
                    elements.customReportResult.innerHTML +=
                        `<h4>${data.report_type || data.tipo_reporte}</h4>`;
                    if (Array.isArray(data.data)) {
                        const table = createTable(data.headers || data.encabezados, data.data);
                        elements.customReportResult.appendChild(table);
                    } else if (typeof data.data === 'object') {
                        displayGeneralSummary(data.data);
                    } else {
                        elements.customReportResult.innerHTML +=
                            '<p>No se pudo generar el reporte. Formato de datos inesperado.</p>';
                    }
                } else {
                    elements.customReportResult.innerHTML += '<p>No se recibieron datos para el reporte.</p>';
                }
            };

            // Initialize when DOM is ready
            $(document).ready(() => {
                fetchDepartments();

                elements.filterType.addEventListener('change', function() {
                    const isYearFilter = this.value === 'year';
                    elements.yearField.style.display = isYearFilter ? 'block' : 'none';
                    elements.dateRangeFields.forEach(field => field.style.display = isYearFilter ?
                        'none' : 'block');
                });

                elements.departmentSelect.addEventListener('change', function() {
                    if (this.value) {
                        fetchOffices(this.value);
                    } else {
                        elements.officeSelect.innerHTML = '<option value="">Todas</option>';
                    }
                });

                document.querySelector(elements.reportForm).addEventListener('submit', generateReport);
                elements.generatePdfBtn.addEventListener('click', generatePdf);
            });

            return {
                generateReport,
                generatePdf
            };
        })();
    </script>
@endsection
