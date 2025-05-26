@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="dashboard">
        <div data-permissions="dashboard.page" class="visible">
            <div class="row pt-3">
                <div class="col-12">
                    <div class="card widget-inline">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-sm-6 col-lg-3">
                                    <div class="card rounded-0 shadow-none m-0">
                                        <div class="card-body text-center">
                                            <i class="ri-file-list-line text-muted font-24"></i>
                                            <h3><span id="totalDocuments">0</span></h3>
                                            <p class="text-muted font-15 mb-0">Total Documentos</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3">
                                    <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                        <div class="card-body text-center">
                                            <i class="ri-inbox-archive-line text-muted font-24"></i>
                                            <h3><span id="totalReceptions">0</span></h3>
                                            <p class="text-muted font-15 mb-0">Recepciones</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3">
                                    <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                        <div class="card-body text-center">
                                            <i class="ri-exchange-line text-muted font-24"></i>
                                            <h3><span id="totalTransfers">0</span></h3>
                                            <p class="text-muted font-15 mb-0">Transferencias</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3">
                                    <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                        <div class="card-body text-center">
                                            <i class="ri-reply-line text-muted font-24"></i>
                                            <h3><span id="totalResponses">0</span></h3>
                                            <p class="text-muted font-15 mb-0">Respuestas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-3">
                <div class="col-xl-4 col-lg-4">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Productividad</h4>
                        </div>
                        <div class="card-body text-center pt-2">
                            <div id="productivityGauge"></div>
                            <h3 class="mb-0"><span id="productivity">0</span>%</h3>
                            <p class="text-muted mb-0">Tasa de Productividad</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-8">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Estado de Documentos</h4>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="documentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-3">
                <div class="col-xl-12 col-lg-12">

                    <div class="card card-h-100">
                        <div class="card-body pt-0">
                            <div id="documentProcessCalendar"></div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->


                </div> <!-- end col -->
            </div>
            <div class="row pt-3">
                <div class="col-xl-12 col-lg-12">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Proyecciones vs Actuales - <span id="yearSpan"></span></h4>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="projectionsVsActualsChart"></canvas>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->

                </div> <!-- end col -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/gauge-chart@0.5.3/dist/bundle.js"></script>
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';

        const DashboardComponent = (() => {
            const elements = {
                totalDocuments: '#totalDocuments',
                totalReceptions: '#totalReceptions',
                totalTransfers: '#totalTransfers',
                totalResponses: '#totalResponses',
                productivity: '#productivity',
                productivityGauge: '#productivityGauge',
                documentStatusChart: '#documentStatusChart'
            };

            let statusChart = null;
            let gaugeChart = null;

            const getElement = (selector) => document.querySelector(selector);

            const initGaugeChart = () => {
                const gaugeElement = getElement(elements.productivityGauge);
                gaugeChart = GaugeChart.gaugeChart(gaugeElement, {
                    hasNeedle: true,
                    needleColor: '#87ceeb',
                    needleUpdateSpeed: 1000,
                    arcColors: ['#ff0000', '#ffa500', '#ffff00', '#008000'],
                    arcDelimiters: [25, 50, 75],
                    rangeLabel: ['0', '100'],
                    centralLabel: '%',
                });
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

            const updateStats = (data) => {
                getElement(elements.totalDocuments).textContent = data.totalDocuments;
                getElement(elements.totalReceptions).textContent = data.receptionCount;
                getElement(elements.totalTransfers).textContent = data.transferCount;
                getElement(elements.totalResponses).textContent = data.responseCount;
                getElement(elements.productivity).textContent = data.productivityPercentage;

                if (gaugeChart) {
                    gaugeChart.updateNeedle(data.productivityPercentage);
                }
            };

            const updateCharts = (data) => {
                if (statusChart) {
                    statusChart.destroy();
                }

                const ctx = getElement(elements.documentStatusChart).getContext('2d');
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
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            };

            const init = () => {
                initGaugeChart();
                fetchStats();
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
