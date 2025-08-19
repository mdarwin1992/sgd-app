@extends('layouts.app')
@section('title', 'Crear Serie Documental')
@section('content')
    <div class="dashboard">
        {{-- El permiso se mantiene para controlar el acceso a esta vista --}}
        <div data-permissions="trd.create">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title">Crear Serie Documental</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate">
                                        Asigne retención y disposición a una serie para una oficina específica.
                                    </h5>

                                    {{-- Contenedor para mostrar errores de validación o del servidor --}}
                                    <div id="error-container" class="alert alert-danger" style="display: none;"></div>

                                    <form id="seriesForm" method="POST">
                                        {{-- Fila 1: Oficina, Serie y Código --}}
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="office_id" class="form-label">Oficina Productora</label>
                                                <select name="office_id" id="office_id" class="form-control" required>
                                                    <option value="">Cargando oficinas...</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="series_entity_id" class="form-label">Nombre de la Serie</label>
                                                <select name="series_entity_id" id="series_entity_id" class="form-control"
                                                    required>
                                                    <option value="">Seleccione una serie</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="administrative_retention" class="form-label">Retención
                                                    en Archivo de Gestión (años)</label>
                                                <input type="number" class="form-control" id="administrative_retention"
                                                    name="administrative_retention" required min="0"
                                                    autocomplete="off">
                                                <input type="text" class="form-control" id="series_code"
                                                    name="series_code" required maxlength="10" autocomplete="off" readonly
                                                    placeholder="Se genera al seleccionar la serie">
                                            </div>
                                        </div>

                                        {{-- Fila 2: Retención y Disposición Final --}}
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="central_retention" class="form-label">Tiempos de Retención
                                                    (años)</label>
                                                <input type="number" class="form-control" id="central_retention"
                                                    name="central_retention" required min="0" autocomplete="off">
                                            </div>
                                            <div class="col-12 col-md-5">
                                                <span>Disposición Final</span>
                                                <div class="mt-0 pt-3">
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="disp_ct"
                                                            name="disposition_type[]" value="CT">
                                                        <label class="form-check-label" for="disp_ct">Conservación
                                                            Total</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="disp_e"
                                                            name="disposition_type[]" value="E">
                                                        <label class="form-check-label" for="disp_e">Eliminación</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="disp_s"
                                                            name="disposition_type[]" value="S">
                                                        <label class="form-check-label" for="disp_s">Selección</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="disp_m"
                                                            name="disposition_type[]" value="M">
                                                        <label class="form-check-label"
                                                            for="disp_m">Microfilmación</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <span>Soporte del Documento</span>
                                                <div class="mt-0 pt-3">
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="doc_p"
                                                            name="documentary_types[]" value="P">
                                                        <label class="form-check-label" for="doc_p">Papel</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="doc_el"
                                                            name="documentary_types[]" value="EL">
                                                        <label class="form-check-label" for="doc_el">Electrónico</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Fila 3: Soportes y Observaciones --}}
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="observations" class="form-label">Procedimiento /
                                                    Observaciones</label>
                                                <textarea class="form-control" id="observations" rows="3" name="observations"
                                                    placeholder="Describa el procedimiento de disposición final y otras observaciones."></textarea>
                                            </div>
                                        </div>

                                        {{-- Botones de Acción --}}
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success rounded-pill"
                                                id="submitButton">
                                                <i class="fas fa-check me-1"></i> Guardar Serie Documental
                                            </button>
                                        </div>
                                    </form>
                                </div> <!-- end card-body -->
                            </div> <!-- end card -->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        async function loadOffices(params) {
            try {
                const response = await HTTPService.get('/api/dashboard/offices');
                const officeSelect = document.getElementById('office_id')

                officeSelect.innerHTML = '<option value="">Seleccione una oficina</option>';

                response.data.forEach(office => {
                    officeSelect.innerHTML +=
                        `<option value="${office.id}">${office.name}</option>`;
                });

            } catch (error) {
                console.error('Error al cargar las oficinas:', error);
                Helpers.showError('No se pudieron cargar las oficinas.');
            }
        }

        async function loadSeries(params) {
            try {
                const userData = HTTPService.getUserData();
                if (!userData.entity_id) {
                    return;
                }
                const response = await HTTPService.get(`/api/dashboard/series/${userData.entity_id}`);

                const seriesSelect = document.getElementById('series_entity_id');
                seriesSelect.innerHTML = '<option value="">Seleccione una serie</option>';

                if (Array.isArray(response.data)) {
                    response.data.forEach(series => {
                        seriesSelect.innerHTML +=
                            `<option value="${series.id}">${series.series_name}</option>`;
                    });
                } else {
                    console.error('La respuesta no es un array:', response.data);
                }
            } catch (error) {
                console.error('Error al cargar las series:', error);
            }
        }

        document.getElementById('series_entity_id').addEventListener('change', e =>
            document.getElementById('series_code').value = e.target.value
        );

        async function save() {

            try {
                const userData = HTTPService.getUserData();
                let office_id = document.getElementById("office_id").value;
                let series_entity_id = document.getElementById("series_entity_id").value;
                let administrative_retention = document.getElementById("administrative_retention").value;
                let series_code = document.getElementById("series_code").value;
                let central_retention = document.getElementById("central_retention").value;
                let disposal_procedure = document.getElementById("observations").value;
                let entity_id = userData.entity_id
                const disposition_type = Array.from(document.querySelectorAll(
                        'input[name="disposition_type[]"]:checked'))
                    .map(checkbox => checkbox.value);

                const documentary_types = Array.from(document.querySelectorAll(
                        'input[name="documentary_types[]"]:checked'))
                    .map(checkbox => checkbox.value);

                /* const response = await HTTPService.post('/api/dashboard/retencion-documental/store', {
                    office_id,
                    series_entity_id,
                    administrative_retention,
                    series_code,
                    central_retention,
                    disposition_type,
                    documentary_types,
                    disposal_procedure,
                    entity_id
                }); */

                let data = {
                    office_id,
                    series_entity_id,
                    administrative_retention,
                    series_code,
                    central_retention,
                    disposition_type,
                    documentary_types,
                    disposal_procedure,
                    entity_id
                }

                console.log(data)

                // Helpers.getMessage('TRD se creo exitosamente', '/dashboard/tabla-de-retencion-documental');

            } catch (error) {
                console.error('Error al almacenar el TRD', error);
                alert("Hubo un error al registrar el TRD.");
            }
        }

        $(document).ready(function() {
            $("#seriesForm").validate({ // Changed to seriesForm as per HTML ID
                rules: {
                    office_id: 'required',
                    series_entity_id: 'required',
                    series_code: {
                        required: true,
                        maxlength: 10
                    },
                    administrative_retention: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    central_retention: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    'disposition_type[]': {
                        required: true,
                        minlength: 1
                    },
                    'documentary_types[]': {
                        required: true,
                        minlength: 1
                    }
                },
                messages: {
                    office_id: 'Por favor, seleccione una oficina.',
                    series_entity_id: 'Por favor, seleccione un nombre de serie.',
                    administrative_retention: 'Ingrese un número válido para la retención (ej: 5).',
                    central_retention: 'Ingrese un número válido para la retención (ej: 20).',
                    'disposition_type[]': 'Debe seleccionar al menos un tipo de disposición final.',
                    'documentary_types[]': 'Debe seleccionar al menos un soporte.'
                },
                errorElement: 'div',
                errorPlacement: (error, element) => {
                    error.addClass('invalid-feedback');
                    if (element.is(':checkbox')) {
                        element.closest('.col-12').append(error);
                    } else {
                        element.closest('.col-md-4, .col-md-6').append(error);
                    }
                },
                highlight: (element) => $(element).addClass('is-invalid'),
                unhighlight: (element) => $(element).removeClass('is-invalid'),
                submitHandler: function(form) {
                    save();
                }
            });

        });


        loadOffices();
        loadSeries();
    </script>
@endsection
