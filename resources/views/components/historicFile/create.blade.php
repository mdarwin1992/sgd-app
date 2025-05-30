@extends('layouts.app')

@section('title', 'Crear Archivo Histórico')

@section('content')
    <div data-permissions="historic.create" class="visible">
        <div class="row pt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Archivo histórico</h4>
                        <p class="text-muted font-14"> Un archivo histórico organiza y conserva documentos de una
                            entidad de manera sistemática, asegurando su preservación y accesibilidad a lo largo del
                            tiempo para fines de investigación, memoria y patrimonio cultural. </p>

                        <form id="AddForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <!-- Entity ID hidden input -->
                            <input type="hidden" id="entity_id" name="entity_id" readonly>
                            <input type="hidden" id="system_code" name="system_code" readonly>
                            <input type="hidden" id="filed" name="filed" readonly>
                            <input type="hidden" id="response_file" name="response_file" readonly>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="office_id" class="form-label">Oficina</label>
                                    <select class="form-select" id="office_id" name="office_id" required>
                                        <option value="">Seleccione una oficina</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, seleccione una oficina.
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="series_id" class="form-label">Serie</label>
                                    <select class="form-select" id="series_id" name="series_id" required>
                                        <option value="">Seleccione una serie</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, seleccione una serie.
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="subseries_id" class="form-label">Sub Serie</label>
                                    <select class="form-select" id="subseries_id" name="subseries_id" required>
                                        <option value="">Seleccione una sub serie</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, seleccione una sub serie.
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="shelf_number" class="form-label">N° Estante</label>
                                    <input type="text" class="form-control" id="shelf_number" name="shelf_number"
                                        autocomplete="off" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="tray" class="form-label">Bandeja</label>
                                    <select class="form-select" id="tray" name="tray">
                                        <option selected>Seleccionar...</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                        <option value="F">F</option>
                                        <option value="G">G</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="box_number" class="form-label">N° Caja</label>
                                    <input type="text" class="form-control" id="box_number" name="box_number"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-5 col-lg-6">
                                    <p class="h3">Medio Conservación Principal</p>
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="main_conservation_medium_caja"
                                                name="main_conservation_medium" value="CAJA" class="form-check-input"
                                                required>
                                            <label class="form-check-label" for="main_conservation_medium_caja">Caja</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="main_conservation_medium_carpeta"
                                                name="main_conservation_medium" value="CARPETA" class="form-check-input">
                                            <label class="form-check-label"
                                                for="main_conservation_medium_carpeta">Carpeta</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="main_conservation_medium_legado"
                                                name="main_conservation_medium" value="LEGADO" class="form-check-input">
                                            <label class="form-check-label"
                                                for="main_conservation_medium_legado">Legado</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="main_conservation_medium_tomo"
                                                name="main_conservation_medium" value="TOMO" class="form-check-input">
                                            <label class="form-check-label"
                                                for="main_conservation_medium_tomo">Tomo</label>
                                        </div>
                                    </div>
                                    <p class="h3">Conservado En:</p>
                                    <div class="mb-1">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="preserved_in_carpeta" name="preserved_in"
                                                value="CARPETA" class="form-check-input" required>
                                            <label class="form-check-label" for="preserved_in_carpeta">Carpeta</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="preserved_in_libro" name="preserved_in"
                                                value="LIBRO" class="form-check-input">
                                            <label class="form-check-label" for="preserved_in_libro">Libro</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <input type="text" class="form-control" id="ord_number"
                                                    name="ord_number" placeholder="No. Ord">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 col-lg-6">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="folio_number" class="form-label">Numero Folio</label>
                                            <input type="text" class="form-control" id="folio_number"
                                                name="folio_number" required autocomplete="off">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="folder_year" class="form-label">Año Carpeta</label>
                                            <input type="number" class="form-control" id="folder_year"
                                                name="folder_year" required autocomplete="off">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="support" class="form-label">Soporte</label>
                                            <input type="text" class="form-control" id="support" name="support"
                                                value="" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label">Fecha de Inicio</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date"
                                                required autocomplete="off">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label">Fecha Final</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                required autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="document_reference" class="form-label">Referencia documental</label>
                                    <input type="text" class="form-control" id="document_reference"
                                        name="document_reference" required autocomplete="off">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="third_parties" class="form-label">Terceros</label>
                                    <input type="text" class="form-control" id="third_parties" name="third_parties"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="object_observations" class="form-label">Objeto/Observaciones</label>
                                    <textarea rows="3" class="form-control" id="object_observations" name="object_observations"></textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="filepath">Ruta de Origen (Copiar Desde):</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="filepath" name="filepath">
                                    <input type="hidden" class="form-control" id="file_path" name="file_path"
                                        autocomplete="off" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <a href="/dashboard/central-archives" class="btn btn-primary btn-tool rounded-pill me-2">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>

                                <a href="/dashboard/archivo-historico" data-permissions="historic.index"
                                    class="btn btn-warning btn-tool rounded-pill me-2">
                                    <i class="far fa-search"></i> Buscar
                                </a>
                                <button type="submit" class="btn btn-success rounded-pill" id="submitButton">
                                    <i class="fas fa-check me-1"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const HistoricalFileComponent = (() => {
            // Private variables
            let isSubmitting = false;
            let counter = null;
            let filePath = '';

            const elements = {
                HistoricalFileForm: '#AddForm',
                submitButton: '#submitButton',
                office_id: '#office_id',
                series_id: '#series_id',
                subseries_id: '#subseries_id',
                filed: '#filed',
                shelf_number: '#shelf_number',
                tray: '#tray',
                box_number: '#box_number',
                ord_number: '#ord_number',
                folio_number: '#folio_number',
                folder_year: '#folder_year',
                support: '#support',
                start_date: '#start_date',
                end_date: '#end_date',
                ac_end_date: '#ac_end_date',
                document_reference: '#document_reference',
                third_parties: '#third_parties',
                object_observations: '#object_observations',
                file_path: '#file_path',
                entity_id: '#entity_id',
                system_code: '#system_code',
                response_file: '#response_file',
                main_conservation_medium: 'input[name="main_conservation_medium"]',
                preserved_in: 'input[name="preserved_in"]',
                fileInput: 'input[name="filepath"]',
                main_conservation_medium_caja: '#main_conservation_medium_caja'
            };

            // Private methods
            const loadOffices = async () => {
                try {
                    const officesResponse = await HTTPService.get('/api/dashboard/offices');
                    const officeSelect = document.querySelector(elements.office_id);
                    officeSelect.innerHTML = '<option value="">Seleccione una oficina</option>';
                    officesResponse.data.forEach(office => {
                        const option = document.createElement('option');
                        option.value = office.id;
                        option.textContent = office.name;
                        officeSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error al cargar las oficinas:', error);
                }
            };

            const loadCounter = async () => {
                try {

                    const userData = HTTPService.getUserData();

                    const counterResponse = await HTTPService.get(
                        `/api/entity/${userData.entity_id}/counter/2`);
                    counter = counterResponse;
                    document.querySelector(elements.system_code).value = counterResponse.system_code || '';
                    document.querySelector(elements.entity_id).value = counterResponse.entity_id || '';
                } catch (error) {
                    console.error('Error al cargar el contador:', error);
                }
            };

            const loadSeries = async (officeId) => {
                try {
                    const seriesResponse = await HTTPService.get(
                        `/api/dashboard/offices/${officeId}/series`);
                    const seriesSelect = document.querySelector(elements.series_id);
                    seriesSelect.innerHTML = '<option value="">Seleccione una serie</option>';
                    seriesResponse.data.forEach(serie => {
                        const option = document.createElement('option');
                        option.value = serie.id;
                        option.textContent = serie.series_name;
                        seriesSelect.appendChild(option);
                    });
                    seriesSelect.disabled = false;
                } catch (error) {
                    console.error('Error al cargar las series:', error);
                }
            };

            const loadSubseries = async (seriesId) => {
                try {
                    const subseriesResponse = await HTTPService.get(
                        `/api/dashboard/series/${seriesId}/subseries`);
                    const subseriesSelect = document.querySelector(elements.subseries_id);
                    subseriesSelect.innerHTML = '<option value="">Seleccione una subserie</option>';
                    subseriesResponse.data.forEach(subserie => {
                        const option = document.createElement('option');
                        option.value = subserie.id;
                        option.textContent = subserie.subseries_name;
                        subseriesSelect.appendChild(option);
                    });
                    subseriesSelect.disabled = false;
                } catch (error) {
                    console.error('Error al cargar las subseries:', error);
                }
            };

            const handleOfficeChange = (event) => {
                const selectedOfficeId = event.target.value;
                const seriesSelect = document.querySelector(elements.series_id);
                const subseriesSelect = document.querySelector(elements.subseries_id);

                if (selectedOfficeId) {
                    loadSeries(selectedOfficeId);
                } else {
                    seriesSelect.innerHTML = '<option value="">Seleccione una serie</option>';
                    seriesSelect.disabled = true;
                    subseriesSelect.innerHTML = '<option value="">Seleccione una subserie</option>';
                    subseriesSelect.disabled = true;
                }
            };

            const handleSeriesChange = (event) => {
                const selectedSeriesId = event.target.value;
                const subseriesSelect = document.querySelector(elements.subseries_id);
                if (selectedSeriesId) {
                    loadSubseries(selectedSeriesId);
                } else {
                    subseriesSelect.innerHTML = '<option value="">Seleccione una subserie</option>';
                    subseriesSelect.disabled = true;
                }
            };

            const handleSubseriesChange = (event) => {
                const selectedSubseriesId = event.target.value;
                const officeId = document.querySelector(elements.office_id).value;
                const seriesId = document.querySelector(elements.series_id).value;
                const systemCode = document.querySelector(elements.system_code).value;
                const filed = document.querySelector(elements.filed);
                const responseFile = document.querySelector(elements.response_file);

                if (selectedSubseriesId) {
                    filed.value = `${systemCode}-${officeId}-${seriesId}-${selectedSubseriesId}`;
                    responseFile.value = `${systemCode}${officeId}${seriesId}${selectedSubseriesId}`;
                }
            };

            const createHistoricalFile = async (event) => {
                event.preventDefault();

                if (isSubmitting) {
                    return;
                }

                if ($(elements.HistoricalFileForm).valid()) {
                    isSubmitting = true;

                    const form = document.querySelector(elements.HistoricalFileForm);
                    const formData = new FormData(form);
                    const formDataObject = Object.fromEntries(formData);

                    try {
                        const response = await HTTPService.post('/api/dashboard/historical-archive/store',
                            formDataObject);
                        console.log(response);
                        Helpers.getMessage('Se ha guardado correctamente',
                            '/dashboard/archivo-historico/crear');
                    } catch (error) {
                        console.error('Error al crear el archivo central:', error);
                    } finally {
                        isSubmitting = false;
                    }
                }
            };

            const handleFileChange = async (event) => {
                const url = '/api/dashboard/historical-file/upload';
                try {
                    const uploadedData = await HTTPService.upload(url, event);
                    filePath = uploadedData;
                    document.querySelector(elements.file_path).value = uploadedData;
                } catch (error) {
                    console.error('Error al cargar el archivo:', error.message);
                }
            };

            const setupValidation = () => {
                $.validator.setDefaults({
                    errorClass: 'is-invalid',
                    validClass: 'is-valid',
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass(errorClass).removeClass(validClass);
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass(errorClass).addClass(validClass);
                    }
                });

                $(elements.HistoricalFileForm).validate({
                    rules: {
                        office_id: "required",
                        series_id: "required",
                        subseries_id: "required",
                        shelf_number: "required",
                        main_conservation_medium: "required",
                        folio_number: "required",
                        folder_year: {
                            required: true,
                            number: true
                        },
                        start_date: {
                            required: true,
                            date: true
                        },
                        end_date: {
                            required: true,
                            date: true
                        },
                        document_reference: "required",
                        file_path: "required"
                    },
                    messages: {
                        office_id: "Por favor, seleccione una oficina",
                        series_id: "Por favor, seleccione una serie",
                        subseries_id: "Por favor, seleccione una subserie",
                        shelf_number: "Por favor, ingrese el número de estante",
                        main_conservation_medium: "Por favor, seleccione el medio de conservación principal",
                        folio_number: "Por favor, ingrese el número de folio",
                        folder_year: {
                            required: "Por favor, ingrese el año de la carpeta",
                            number: "Por favor, ingrese un año válido"
                        },
                        start_date: {
                            required: "Por favor, ingrese la fecha de inicio",
                            date: "Por favor, ingrese una fecha válida"
                        },
                        end_date: {
                            required: "Por favor, ingrese la fecha final",
                            date: "Por favor, ingrese una fecha válida"
                        },
                        document_reference: "Por favor, ingrese la referencia documental",
                        file_path: "Por favor, seleccione un archivo"
                    },
                    submitHandler: (form, event) => {
                        event.preventDefault();
                        createHistoricalFile(event);
                    }
                });
            };

            const calculateAcEndDate = () => {
                const startDate = document.querySelector(elements.start_date).value;
                const endDate = document.querySelector(elements.end_date).value;
                const acEndDateInput = document.querySelector(elements.ac_end_date);

                if (startDate && endDate) {
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    const yearDiff = end.getFullYear() - start.getFullYear();

                    const acEndDate = new Date(end);
                    acEndDate.setFullYear(acEndDate.getFullYear() + yearDiff);

                    const formattedDate = acEndDate.toISOString().split('T')[0];
                    acEndDateInput.value = formattedDate;
                } else {
                    acEndDateInput.value = '';
                }
            };

            const handleConservationMediumChange = () => {
                // Get all the relevant elements
                const cajaRadio = document.querySelector(elements.main_conservation_medium_caja);
                const conservadoEnRadios = document.querySelectorAll(elements.preserved_in);
                const ordNumberInput = document.querySelector(elements.ord_number);

                // Check if required elements exist
                if (!cajaRadio || !ordNumberInput) {
                    console.warn('Required elements not found for conservation medium handling');
                    return;
                }

                const isCajaSelected = cajaRadio.checked;

                // Enable/disable preserved_in radio buttons
                conservadoEnRadios.forEach(radio => {
                    radio.disabled = !isCajaSelected;
                    if (!isCajaSelected) {
                        radio.checked = false; // Clear selection when disabled
                    }
                });

                // Enable/disable order number input
                ordNumberInput.disabled = !isCajaSelected;
                if (!isCajaSelected) {
                    ordNumberInput.value = ''; // Clear value when disabled
                }

                /* // Enable/disable box number input
                boxNumberInput.disabled = !isCajaSelected;
                if (!isCajaSelected) {
                    boxNumberInput.value = ''; // Clear value when disabled
                } */
            };

            // Initialize when DOM is ready
            $(document).ready(() => {
                setupValidation();
                loadOffices();
                loadCounter();

                document.querySelector(elements.fileInput).addEventListener('change', handleFileChange);
                document.querySelector(elements.office_id).addEventListener('change', handleOfficeChange);
                document.querySelector(elements.series_id).addEventListener('change', handleSeriesChange);
                document.querySelector(elements.subseries_id).addEventListener('change',
                    handleSubseriesChange);
                document.querySelector(elements.start_date).addEventListener('change', calculateAcEndDate);
                document.querySelector(elements.end_date).addEventListener('change', calculateAcEndDate);
                document.querySelector(elements.HistoricalFileForm).addEventListener('submit',
                    createHistoricalFile);

                // Event listeners for conservation medium handling
                const cajaRadio = document.querySelector(elements.main_conservation_medium_caja);
                cajaRadio.addEventListener('change', handleConservationMediumChange);

                document.querySelectorAll(elements.main_conservation_medium).forEach(radio => {
                    radio.addEventListener('change', handleConservationMediumChange);
                });

                // Initialize conservation medium state
                handleConservationMediumChange();
            });

            // Create public API
            const publicApi = {
                loadOffices,
                loadCounter,
                loadSeries,
                loadSubseries,
                handleOfficeChange,
                handleSeriesChange,
                handleSubseriesChange,
                createHistoricalFile,
                handleFileChange,
                setupValidation,
                calculateAcEndDate,
                handleConservationMediumChange
            };

            // Expose to window object for inline event handlers
            window.DocumentManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
