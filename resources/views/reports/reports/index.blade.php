@extends('layouts.app')

@section('title', 'Generar Reporte')

@section('content')
    <div class="dashboard">
        <div data-permissions="dashboard.page" class="visible">
            <div class="row pt-3">
                <div class="col-xl-12 col-lg-12">
                    <div class="card card-h-100">
                        <div class="card-body pt-0">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h2>Generar Reporte Personalizado</h2>
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
                                            <div class="col-md-3 mb-3">
                                                <label for="filterType" class="form-label">Tipo de Filtro</label>
                                                <select class="form-control" id="filterType" name="filter_type"
                                                        required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="date_range">RANGO DE FECHAS</option>
                                                    <option value="year">AÑO</option>

                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-3 year-field" style="display: none;">
                                                <label for="year" class="form-label">Año</label>
                                                <input type="number" class="form-control" id="year" name="year"
                                                       min="2000" max="2099">
                                            </div>
                                            <div class="col-md-2 mb-3 date-range-field" style="display: none;">
                                                <label for="startDate" class="form-label">Fecha de Inicio</label>
                                                <input type="date" class="form-control" id="startDate"
                                                       name="start_date">
                                            </div>
                                            <div class="col-md-2 mb-3 date-range-field" style="display: none;">
                                                <label for="endDate" class="form-label">Fecha de Fin</label>
                                                <input type="date" class="form-control" id="endDate" name="end_date">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="status" class="form-label">Estado (opcional)</label>
                                                <select class="form-control" id="status_id" name="status_id">
                                                    <option value="">Todos</option>
                                                    <option value="RECIBIDA">Recibida</option>
                                                    <option value="PROCESANDO">Procesando</option>
                                                    <option value="CONTESTADO">Contestado</option>
                                                    <option value="ARCHIVADO">Archivado</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="department_id" class="form-label">Departamento
                                                    (opcional)</label>
                                                <select class="form-control" id="department_id" name="department_id">
                                                    <option value="">Todos</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="office_id" class="form-label">Oficina (opcional)</label>
                                                <select class="form-control" id="office_id" name="office_id">
                                                    <option value="">Todas</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard" class="btn btn-primary btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success" id="submitButton">
                                                <i class="fas fa-check me-1"></i> Generar Reporte
                                            </button>
                                        </div>
                                    </form>
                                    <button id="generatePdfBtn">Generate PDF</button>

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

        document.addEventListener('DOMContentLoaded', function () {
            const filterType = document.getElementById('filterType');
            const yearField = document.querySelector('.year-field');
            const dateRangeFields = document.querySelectorAll('.date-range-field');
            const departmentSelect = document.getElementById('department_id');
            const officeSelect = document.getElementById('office_id');
            const reportForm = document.getElementById('reportForm');
            const customReportResult = document.getElementById('customReportResult');

            filterType.addEventListener('change', function () {
                if (this.value === 'year') {
                    yearField.style.display = 'block';
                    dateRangeFields.forEach(field => field.style.display = 'none');
                } else if (this.value === 'date_range') {
                    yearField.style.display = 'none';
                    dateRangeFields.forEach(field => field.style.display = 'block');
                }
            });

            fetch('/api/departments')
                .then(response => response.json())
                .then(data => {
                    data.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.id;
                        option.textContent = department.name;
                        departmentSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar departamentos:', error));

            departmentSelect.addEventListener('change', function () {
                officeSelect.innerHTML = '<option value="">Todas</option>';
                if (this.value) {
                    fetch(`/api/offices?department_id=${this.value}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(office => {
                                const option = document.createElement('option');
                                option.value = office.id;
                                option.textContent = office.name;
                                officeSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error al cargar oficinas:', error));
                }
            });

            reportForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                customReportResult.innerHTML = '<p>Generando reporte...</p>';

                fetch('/api/reports/generate', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        customReportResult.innerHTML = '<h3>Resultado del Reporte:</h3>';
                        if (data.error) {
                            customReportResult.innerHTML += `<p class="text-danger">${data.error}</p>`;
                            if (data.trace) {
                                console.error('Error trace:', data.trace);
                            }
                        } else if (data.data) {
                            customReportResult.innerHTML += `<h4>${data.report_type}</h4>`;
                            if (Array.isArray(data.data)) {
                                const table = createTable(data.headers, data.data);
                                customReportResult.appendChild(table);
                            } else if (typeof data.data === 'object') {
                                displayGeneralSummary(data.data);
                            } else {
                                customReportResult.innerHTML += '<p>No se pudo generar el reporte. Formato de datos inesperado.</p>';
                            }
                        } else {
                            customReportResult.innerHTML += '<p>No se recibieron datos para el reporte.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        customReportResult.innerHTML = `<p class="text-danger">Error al generar el reporte: ${error.message}</p>`;
                    });
            });


            function createTable(headers, data) {
                const table = document.createElement('table');
                table.className = 'table table-striped table-bordered';

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
            }

            function createDetailsTable(details) {
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
            }

            function displayGeneralSummary(data) {
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

                customReportResult.appendChild(summaryTable);
            }
        });

        document.getElementById('generatePdfBtn').addEventListener('click', function () {
            const formData = new FormData(reportForm);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/reports/generate-pdf', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        });
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Crear una URL del blob
                    const url = URL.createObjectURL(blob);

                    // Abrir el PDF en una nueva pestaña
                    const newTab = window.open(url, '_blank');

                    // Si el navegador bloquea la apertura de la pestaña, mostrar una alerta
                    if (!newTab || newTab.closed || typeof newTab.closed === 'undefined') {
                        alert('La nueva pestaña fue bloqueada. Permite las ventanas emergentes para ver el PDF.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMessage = 'Error generating PDF';
                    if (error.message) {
                        errorMessage += ': ' + error.message;
                    }
                    if (error.errors) {
                        errorMessage += '\n' + Object.values(error.errors).join('\n');
                    }
                    alert(errorMessage);
                });
        });


    </script>
@endsection
