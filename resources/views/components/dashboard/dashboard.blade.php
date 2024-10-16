@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="dashboard">
        <div data-permissions="dashboard.page" class="visible">

            <div class="row pt-3">
                <div class="col-xl-6 col-lg-6">

                    <div class="card card-h-100">
                        <div class="card-body pt-0">
                            <div id="documentProcessCalendar"></div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->


                </div> <!-- end col -->
                <div class="col-xl-6 col-lg-6">
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

    <script type="module">

        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('documentProcessCalendar');

            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día'
                    },
                    height: 'auto',
                    dayMaxEvents: 1,  // Limitar a 3 eventos por día
                    eventClick: function (info) {
                        // Mostramos un mensaje con información del evento
                        alert('Evento: ' + info.event.title + '\n' +
                            'Fecha de inicio: ' + info.event.start.toLocaleString() + '\n' +
                            'Descripción: ' + info.event.extendedProps.description);
                    },
                    moreLinkText: 'más',  // Texto que aparece para ver más eventos
                    moreLinkClick: 'popover',  // Mostrar los eventos restantes en un popover
                    events: '/api/reports/document-process-timeline',
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false  // Formato 24 horas
                    },
                    eventDisplay: 'block',
                    displayEventTime: true,
                    eventTextColor: '#000000',  // Texto en negro
                    eventDidMount: function (info) {
                        info.el.style.backgroundColor = getEventColor(info.event.extendedProps.type);
                        info.el.style.borderRadius = '4px';
                        info.el.style.padding = '2px 5px';
                        info.el.style.marginBottom = '2px';
                        info.el.style.whiteSpace = 'normal';  // Ajustar texto
                    },
                    eventContent: function (arg) {
                        return {
                            html: '<div class="fc-event-description" style="font-size: 0.8em;">' + arg.event.extendedProps.referenceCode + '</div>'
                        };
                    }
                });

                calendar.render();
            } else {
                console.error("El elemento con id 'documentProcessCalendar' no se encontró.");
            }

            function getEventColor(type) {
                const colors = {
                    'reception': '#4CAF50',  // Verde para recepción
                    'transfer': '#2196F3',   // Azul para transferencia
                    'response': '#FFC107',   // Amarillo para respuesta
                    'status': '#9C27B0'      // Púrpura para cambios de estado
                };
                return colors[type] || '#4fc3f7'; // Color por defecto
            }


            const ctx = document.getElementById('projectionsVsActualsChart').getContext('2d');
            const yearSpan = document.getElementById('yearSpan');

            fetch('/api/reports/projections-vs-actuals')
                .then(response => response.json())
                .then(result => {
                    const data = result.data;
                    yearSpan.textContent = result.year;

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => item.month),
                            datasets: [
                                {
                                    label: 'Realizadas',
                                    data: data.map(item => item.projections),
                                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Recibida',
                                    data: data.map(item => item.recibida),
                                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Procesando',
                                    data: data.map(item => item.procesando),
                                    backgroundColor: 'rgba(255, 206, 86, 0.5)',
                                    borderColor: 'rgba(255, 206, 86, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Contestado',
                                    data: data.map(item => item.contestado),
                                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Archivado',
                                    data: data.map(item => item.archivado),
                                    backgroundColor: 'rgba(153, 102, 255, 0.5)',
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Número de documentos'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Mes'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Proyecciones vs Actuales por Estado'
                                },
                                legend: {
                                    position: 'top',
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error:', error));

        });

    </script>
@endsection
