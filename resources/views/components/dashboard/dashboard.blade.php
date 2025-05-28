@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="dashboard">
        <div data-permissions="dashboard.page" class="visible">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <form class="d-flex">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-light" id="dash-daterange">
                                    <span class="input-group-text bg-primary border-primary text-white">
                                        <i class="mdi mdi-calendar-range font-13"></i>
                                    </span>
                                </div>
                                <a href="javascript: void(0);" class="btn btn-primary ms-2">
                                    <i class="mdi mdi-autorenew"></i>
                                </a>
                                <a href="javascript: void(0);" class="btn btn-primary ms-1">
                                    <i class="mdi mdi-filter-variant"></i>
                                </a>
                            </form>
                        </div>
                        <h4 class="page-title">Dashboard</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-5 col-lg-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-file-document widget-icon"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Documentos registrados">Documentos
                                        registrados
                                    </h5>
                                    <h3 class="mt-3 mb-3"><span id="totalDocuments">0</span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-email-receive widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Documentos recepcionados">Documentos
                                        recepcionados</h5>
                                    <h3 class="mt-3 mb-3"><span id="receptionCount">0</span></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-email-send widget-icon bg-warning-lighten text-warning"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Documentos transferidos">Documentos
                                        transferidos</h5>
                                    <h3 class="mt-3 mb-3"><span id="transferCount">0</span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-email-fast widget-icon bg-info-lighten text-info"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Respuestas">Respuestas</h5>
                                    <h3 class="mt-3 mb-3"><span id="responseCount">0</span></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-book-arrow-up widget-icon bg-danger-lighten text-danger"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Préstamos Activos">Préstamos Activos</h5>
                                    <h3 class="mt-3 mb-3"><span id="activeLoans">0</span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="card widget-flat">
                                <div class="card-body">
                                    <div class="float-end">
                                        <i class="mdi mdi-book-arrow-down widget-icon bg-success-lighten text-success"></i>
                                    </div>
                                    <h5 class="text-muted fw-normal mt-0" title="Devoluciones">Devoluciones</h5>
                                    <h3 class="mt-3 mb-3"><span id="returnedLoans">0</span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7 col-lg-6">
                    <div class="card card-h-50">
                        <div class="card-body pt-0">
                            <div dir="ltr">
                                <canvas id="documentStatusChart" class="apex-charts"
                                    data-colors="#727cf5,#91a6bd40"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title">Actividades recientes</h4>
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Informe semanal</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Informe mensual</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Informe diario</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div class="table-responsive">

                                <table class="table table-centered table-nowrap table-hover mb-0"
                                    id="recentActivitiesTable">
                                    <tbody>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->

                <div class="col-xl-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title">Your Calendar</h4>
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Weekly Report</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Monthly Report</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Settings</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-7">
                                    <div data-provide="datepicker-inline" data-date-today-highlight="true"
                                        class="calendar-widget"></div>
                                </div> <!-- end col-->
                                <div class="col-md-5">
                                    <ul class="list-unstyled mt-1" id="recentActivitiesList">

                                    </ul>
                                </div> <!-- end col -->
                            </div>
                            <!-- end row -->

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';

        const DashboardComponent = (() => {
            const elements = {
                totalDocuments: '#totalDocuments',
                receptionCount: '#receptionCount',
                transferCount: '#transferCount',
                responseCount: '#responseCount',
                activeLoans: '#activeLoans',
                returnedLoans: '#returnedLoans',
                productivityPercentage: '#productivityPercentage',
                documentStatusChart: '#documentStatusChart',
                recentActivitiesTable: '#recentActivitiesTable',
                recentActivitiesList: '#recentActivitiesList',
            };

            let statusChart = null;
            let gaugeChart = null;

            const getElement = (selector) => document.querySelector(selector);

            const initGaugeChart = () => {
                const gaugeElement = getElement(elements.productivityGauge);
                if (gaugeElement) {
                    gaugeChart = GaugeChart.gaugeChart(gaugeElement, {
                        hasNeedle: true,
                        needleColor: '#87ceeb',
                        needleUpdateSpeed: 1000,
                        arcColors: ['#ff0000', '#ffa500', '#ffff00', '#008000'],
                        arcDelimiters: [25, 50, 75],
                        rangeLabel: ['0', '100'],
                        centralLabel: '%',
                    });
                }
            };

            const fetchStats = async () => {
                try {
                    const response = await HTTPService.get('/api/document-statistics');
                    updateStats(response);
                    updateCharts(response);
                } catch (error) {
                    console.error('Error al obtener las estadísticas:', error);
                }
            };

            const getRecentActivities = async () => {
                try {
                    const response = await HTTPService.get('/api/reports/recent-activities');
                    updateRecentActivitiesTable(response);
                    console.log('Actividades recientes actualizadas:', response);
                } catch (error) {
                    console.error('Error al obtener las actividades recientes:', error);
                }
            };

            const updateRecentActivitiesTable = (data) => {
                const tableBody = document.getElementById('recentActivitiesTable');
                tableBody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevas filas
                data.central_archives.forEach(activity => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <h6 class="mt-0 mb-1">${activity.document_reference}</h6>
                                        <span class="badge badge-outline-primary font-11">Archivo Central</span> <span class="badge badge-outline-info font-11">Referencia documental: ${activity.filed}</span>
                                        <small class="fw-normal ms-1">${new Date(activity.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>
                        `;
                    tableBody.appendChild(row);
                });

                data.historic_files.forEach(activity => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <h6 class="mt-0 mb-1">${activity.document_reference}</h6>
                                        <span class="badge badge-outline-primary font-11">Archivo Histórico</span> <span class="badge badge-outline-info font-11">Referencia documental: ${activity.filed}</span>
                                        <small class="fw-normal ms-1">${new Date(activity.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>
                        `;
                    tableBody.appendChild(row);
                });

                data.document_sendings.forEach(activity => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <h6 class="mt-0 mb-1">${activity.subject}</h6>
                                        <span class="badge badge-outline-primary font-11">Envío de Documento</span> <span class="badge badge-outline-info font-11">Destinatario: ${activity.sender}</span> 
                                        <small class="fw-normal ms-1">${new Date(activity.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>                           
                        `;
                    tableBody.appendChild(row);
                });

                data.correspondence_transfers.forEach(activity => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <h6 class="mt-0 mb-1">${activity.document.subject}</h6>
                                        <span class="badge badge-outline-primary font-11">Transferencia</span> <span class="badge badge-outline-info font-11">Destinatario: ${activity.office.name}</span>
                                        <small class="fw-normal ms-1">${new Date(activity.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>                       
                        `;
                    tableBody.appendChild(row);
                });

                data.receptions.forEach(activity => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <h5 class="mt-0 mb-1">${activity.document.subject}</h5>
                                        <span class="badge badge-outline-primary font-11">Recepción</span>
                                        <span class="badge badge-outline-primary font-11">Recepción: ${activity.document.reference_code}</span>
                                        <span class="badge badge-outline-info font-11">Remitente: ${activity.document.sender_name}</span> 
                                        <small class="fw-normal ms-1">${new Date(activity.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>                                                     
                        `;
                    tableBody.appendChild(row);
                });

                data.request_responses.forEach(activity => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <h6 class="mt-0 mb-1">${activity.response_content}</h6>
                                        <span class="badge badge-outline-primary font-11">Respuesta</span> 
                                        <small class="fw-normal ms-1">${new Date(activity.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>`;
                    tableBody.appendChild(row);
                });

                data.document_loans.forEach(activity => {
                    const row = document.createElement('tr');
                    let documentType = '';
                    let documentReference = '';
                    let loanStatus = '';

                    loanStatus = activity.state === 1 ? 'PRESTADO' : 'DEVUELTO';

                    if (activity.type_of_document_borrowed === 1) {
                        documentType = 'Archivo Histórico';
                        documentReference = activity.central_archive_loans.central_archive
                            .document_reference

                    } else if (activity.type_of_document_borrowed === 2) {
                        documentType = 'Archivo Central';
                        documentReference = activity.historical_archive_loans.historic_file
                            .document_reference
                    }
                    row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <h6 class="mt-0 mb-1">${documentReference}</h6>
                                        <span class="badge badge-outline-primary font-11">Préstamo</span> <span class="badge badge-outline-info font-11">Estado: ${loanStatus}</span> 
                                        <span class="badge badge-outline-info font-11">Tipo: ${documentType}</span> 
                                        <small class="fw-normal ms-1">${new Date(activity.created_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            </td>                                                    
                        `;
                    tableBody.appendChild(row);
                });


            };


            const updateStats = (data) => {
                const totalDocumentsElement = getElement(elements.totalDocuments);
                const receptionCountElement = getElement(elements.receptionCount);
                const transferCountElement = getElement(elements.transferCount);
                const responseCountElement = getElement(elements.responseCount);
                const activeLoansElement = getElement(elements.activeLoans);
                const returnedLoansElement = getElement(elements.returnedLoans);
                const productivityElement = getElement(elements.productivityPercentage);

                if (totalDocumentsElement) totalDocumentsElement.textContent = data.totalDocuments;
                if (receptionCountElement) receptionCountElement.textContent = data.receptionCount;
                if (transferCountElement) transferCountElement.textContent = data.transferCount;
                if (responseCountElement) responseCountElement.textContent = data.responseCount;
                if (activeLoansElement) activeLoansElement.textContent = data.activeLoans;
                if (returnedLoansElement) returnedLoansElement.textContent = data.returnedLoans;
                if (productivityElement) productivityElement.textContent = data.productivityPercentage;

                /* if (gaugeChart) {
                    gaugeChart.updateNeedle(data.productivityPercentage);
                } */
            };


            const updateCharts = (data) => {
                const ctx = getElement(elements.documentStatusChart);
                if (ctx) {
                    if (statusChart) {
                        statusChart.destroy();
                    }

                    statusChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Recibidos', 'En Proceso', 'Completados'],
                            datasets: [{
                                data: [
                                    data.receivedDocuments,
                                    data.processingTransfers,
                                    data.completedResponses
                                ],
                                backgroundColor: ['#727cf5', '#fa5c7c', '#0acf97'],
                                borderColor: ['#727cf5', '#fa5c7c', '#0acf97'],
                                borderWidth: 1
                            }]
                        },

                        options: {
                            maintainAspectRatio: true,
                            responsive: true,
                            layout: {
                                padding: {
                                    left: 10,
                                    right: 10,
                                    top: 10,
                                    bottom: 5
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        padding: 5 // Reduce espacio entre ejes y barras
                                    }
                                },
                                y: {
                                    grid: {
                                        drawBorder: false
                                    },
                                    ticks: {
                                        padding: 5
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    align: 'start', // Alinea la leyenda a la izquierda
                                    position: 'top',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 8
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'PROJECTIONS VS ACTUALS',
                                    font: {
                                        size: 16
                                    }
                                },
                            }
                        }
                    });
                }
            };


            const getRecentActivitiesCalendar = async (date) => {
                try {
                    const response = await HTTPService.post('/api/reports/recent-activities-calendar', {
                        date
                    });

                    renderRecentActivities(response);
                    console.log(response);
                    
                } catch (error) {
                    console.error('Error al obtener las actividades recientes:', error);
                }
            };

            const renderRecentActivities = (data) => {
                const listBody = getElement(elements.recentActivitiesList);
                listBody.innerHTML = ''; // Limpiar la lista antes de agregar nuevas filas

                data.central_archives.forEach(activity => {
                    const row = document.createElement('li');
                    row.className = 'mb-4';
                    row.innerHTML = `
                        <p class="text-muted mb-1 font-13">
                            <i class="mdi mdi-calendar"></i> ${new Date(activity.created_at).toLocaleString()}
                        </p>
                        <h6 class="mt-0 mb-1">${activity.document_reference}</h6>
                        <span class="badge badge-outline-primary font-11">Archivo Central</span>
                        <span class="badge badge-outline-info font-11">Referencia documental: ${activity.filed}</span>
                    `;
                    listBody.appendChild(row);
                });

                data.historic_files.forEach(activity => {
                    const row = document.createElement('li');
                    row.className = 'mb-4';
                    row.innerHTML = `
                        <p class="text-muted mb-1 font-13">
                            <i class="mdi mdi-calendar"></i> ${new Date(activity.created_at).toLocaleString()}
                        </p>
                        <h6 class="mt-0 mb-1">${activity.document_reference}</h6>
                        <span class="badge badge-outline-primary font-11">Archivo Histórico</span>
                        <span class="badge badge-outline-info font-11">Referencia documental: ${activity.filed}</span>
                    `;
                    listBody.appendChild(row);
                });

                data.document_sendings.forEach(activity => {
                    const row = document.createElement('li');
                    row.className = 'mb-4';
                    row.innerHTML = `
                        <p class="text-muted mb-1 font-13">
                            <i class="mdi mdi-calendar"></i> ${new Date(activity.created_at).toLocaleString()}
                        </p>
                        <h6 class="mt-0 mb-1">${activity.subject}</h6>
                        <span class="badge badge-outline-primary font-11">Envío de Documento</span>
                        <span class="badge badge-outline-info font-11">Destinatario: ${activity.sender}</span>
                    `;
                    listBody.appendChild(row);
                });

                data.correspondence_transfers.forEach(activity => {
                    const row = document.createElement('li');
                    row.className = 'mb-4';
                    row.innerHTML = `
                        <p class="text-muted mb-1 font-13">
                            <i class="mdi mdi-calendar"></i> ${new Date(activity.created_at).toLocaleString()}
                        </p>
                        <h6 class="mt-0 mb-1">${activity.document.subject}</h6>
                        <span class="badge badge-outline-primary font-11">Transferencia</span>
                        <span class="badge badge-outline-info font-11">Destinatario: ${activity.office.name}</span>
                    `;
                    listBody.appendChild(row);
                });

                data.receptions.forEach(activity => {
                    const row = document.createElement('li');
                    row.className = 'mb-4';
                    row.innerHTML = `
                        <p class="text-muted mb-1 font-13">
                            <i class="mdi mdi-calendar"></i> ${new Date(activity.created_at).toLocaleString()}
                        </p>
                        <h5 class="mt-0 mb-1">${activity.document.subject}</h5>
                        <span class="badge badge-outline-primary font-11">Recepción</span>
                        <span class="badge badge-outline-primary font-11">Recepción: ${activity.document.reference_code}</span>
                        <span class="badge badge-outline-info font-11">Remitente: ${activity.document.sender_name}</span>
                    `;
                    listBody.appendChild(row);
                });

                data.request_responses.forEach(activity => {
                    const row = document.createElement('li');
                    row.className = 'mb-4';
                    row.innerHTML = `
                        <p class="text-muted mb-1 font-13">
                            <i class="mdi mdi-calendar"></i> ${new Date(activity.created_at).toLocaleString()}
                        </p>
                        <h6 class="mt-0 mb-1">${activity.response_content}</h6>
                        <span class="badge badge-outline-primary font-11">Respuesta</span>
                    `;
                    listBody.appendChild(row);
                });

                data.document_loans.forEach(activity => {
                    const row = document.createElement('li');
                    row.className = 'mb-4';
                    let documentType = '';
                    let documentReference = '';
                    let loanStatus = '';

                    loanStatus = activity.state === 1 ? 'PRESTADO' : 'DEVUELTO';

                    if (activity.type_of_document_borrowed === 1) {
                        documentType = 'Archivo Histórico';
                        documentReference = activity.central_archive_loans.central_archive
                            .document_reference;
                    } else if (activity.type_of_document_borrowed === 2) {
                        documentType = 'Archivo Central';
                        documentReference = activity.historical_archive_loans.historic_file
                            .document_reference;
                    }
                    row.innerHTML = `
                        <p class="text-muted mb-1 font-13">
                            <i class="mdi mdi-calendar"></i> ${new Date(activity.created_at).toLocaleString()}
                        </p>
                        <h6 class="mt-0 mb-1">${documentReference}</h6>
                        <span class="badge badge-outline-primary font-11">Préstamo</span>
                        <span class="badge badge-outline-info font-11">Estado: ${loanStatus}</span>
                        <span class="badge badge-outline-info font-11">Tipo: ${documentType}</span>
                    `;
                    listBody.appendChild(row);
                });
            };

            const init = () => {
                initGaugeChart();
                fetchStats();
                getRecentActivities();

                // Inicializa el datepicker y vincula el evento de cambio de fecha
                $(document).ready(function() {
                    $('.calendar-widget').datepicker({
                        format: 'yyyy-mm-dd',
                        todayHighlight: true
                    }).on('changeDate', function(e) {
                        const selectedDate = e.format();
                        getRecentActivitiesCalendar(selectedDate);
                    });
                });

                // Actualizar stats cada 5 minutos
                setInterval(fetchStats, 5 * 60 * 1000);
            };

            // API pública
            return {
                init
            };
        })();

        // Inicializar el componente cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            DashboardComponent.init();
        });
    </script>
@endsection
