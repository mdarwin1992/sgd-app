@extends('layouts.app')
@section('title', 'Crear Series Documentales')
@section('content')
    <div class="dashboard">
        <div data-permissions="dashboard.page">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title">Crear Serie Documental</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate">
                                        Crear una nueva Serie Documental para una oficina específica
                                    </h5>
                                    <div id="error-container" class="alert alert-danger" style="display: none;"></div>
                                    <form id="seriesForm" method="POST">
                                        <div class="row mb-2">
                                            <div class="col-md-4 mb-3">
                                                <label for="office_id">Oficina</label>
                                                <select name="office_id" id="office_id" class="form-control" required>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="series_entity_id">Nombre de la Serie</label>
                                                <select name="series_entity_id" id="series_entity_id"
                                                        class="form-control" required>
                                                </select>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label for="series_code">Código de la Serie</label>
                                                <input type="text" class="form-control" id="series_code"
                                                       name="series_code"
                                                       required maxlength="10" autocomplete="off" readonly>
                                            </div>
                                            <div class="col-md-2">

                                                <button type="button"
                                                        class="btn btn-secondary rounded-pill btn-sm mt-3 ml"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#centermodal">Agregar Series
                                                </button>
                                            </div>
                                        </div>
                                        <h6>Subseries</h6>
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <input type="text" class="form-control" name="subseries[0][name]"
                                                       id="name" placeholder="Nombre de la Subserie" maxlength="100"
                                                       required autocomplete="off">
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <input type="text" class="form-control" name="subseries[0][code]"
                                                       id="code" placeholder="Código de la Subserie" maxlength="10"
                                                       required autocomplete="off" readonly>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <button type="button" id="add-subseries"
                                                        class="btn btn-secondary rounded-pill btn-sm mt-0 ml">
                                                    Agregar
                                                    Subserie
                                                </button>
                                            </div>
                                        </div>
                                        <div id="subseries-container">
                                            <div class="subseries-item">

                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12 col-md-6">
                                                <h6>Retención</h6>
                                                <div class="row">
                                                    <div class="col-md-6 mb-6">
                                                        <label for="administrative_retention">Retención Administrativa
                                                            (en años)</label>
                                                        <input type="text" class="form-control"
                                                               id="administrative_retention"
                                                               name="administrative_retention"
                                                               required maxlength="10" autocomplete="off">
                                                    </div>
                                                    <div class="col-md-6 mb-6">
                                                        <label for="central_retention">Retención Central (en
                                                            años</label>
                                                        <input type="text" class="form-control" id="central_retention"
                                                               name="central_retention"
                                                               required maxlength="10" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <h6>Disposición Final</h6>
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <div class="mt-2">
                                                            <div class="form-check form-check-inline">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="disposition_type" name="disposition_type"
                                                                       value="CT">
                                                                <label class="form-check-label" for="disposition_type">Conservación
                                                                    Total</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="disposition_type" name="disposition_type"
                                                                       value="E">
                                                                <label class="form-check-label" for="disposition_type">Eliminación</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="disposition_type" name="disposition_type"
                                                                       value="S">
                                                                <label class="form-check-label" for="disposition_type">Selección</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="checkbox" class="form-check-input"
                                                                       id="disposition_type" name="disposition_type"
                                                                       value="M">
                                                                <label class="form-check-label" for="disposition_type">Microfilmacion</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="central_retention">Procedimiento de
                                                    Disposición</label>
                                                <textarea class="form-control" id="disposal_procedure" rows="3"
                                                          name="disposal_procedure"></textarea>
                                            </div>
                                        </div>
                                        <h6>Tipos Documentales</h6>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="mt-2">
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input"
                                                               id="documentary_types" name="documentary_types"
                                                               value="P">
                                                        <label class="form-check-label"
                                                               for="documentary_types">Papel</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input"
                                                               id="documentary_types" name="documentary_types"
                                                               value="EL">
                                                        <label class="form-check-label"
                                                               for="documentary_types">Electrónico </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard" class="btn btn-primary rounded-pill btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success rounded-pill"
                                                    id="submitButton">
                                                <i class="fas fa-check me-1"></i> Guardar
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
    <!-- Bottom modal -->
    <div id="centermodal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="topModalLabel">Crear Serie</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form name="formSeries" id="formSeries" method="POST">
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="series_name">Nombre de la Serie</label>
                                <input type="hidden" class="form-control" name="entity_id" id="entity_id" required
                                       autocomplete="off" readonly>
                                <input type="text" class="form-control" name="series_name" id="series_name" required
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Salir</button>
                        <button type="button" id="BtnFormSeries" class="btn btn-primary rounded-pill">Guardar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Center modal -->
@endsection
@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const SeriesCreateComponent = HTTPService.createComponent({
            data: () => ({
                isSubmitting: false,
                offices: [],
                counter: null,
                selectedOffice: null,
            }),

            methods: {
                async loadCounter(id) {
                    const {series_code, code} = this.elements;

                    try {
                        const counters = await HTTPService.get(`/api/get-all-counters/${id}`);


                        if (Array.isArray(counters.data) && counters.data.length === 0) {
                            series_code.value = 1 || '';
                            code.value = 1 || '';
                        } else {
                            const firstCounter = counters.data[0];
                            if (firstCounter.parent_count === null && firstCounter.max_child === null) {
                                series_code.value = counters.lastCounters + 1 || '';
                                code.value = 1 || '';
                            } else {
                                series_code.value = firstCounter.parent_count || '';
                                code.value = firstCounter.max_child || '';
                            }
                        }
                    } catch (error) {
                        console.error('Error al cargar los contadores:', error);
                        Helpers.showErrorAlert('No se pudieron cargar los contadores. Por favor, intente nuevamente.');
                    }
                },

                async loadOffices() {
                    try {
                        const response = await HTTPService.get('/api/dashboard/offices');
                        this.setData({offices: response.data});
                        this.populateOfficeSelect();
                    } catch (error) {
                        console.error('Error al cargar las oficinas:', error);
                        Helpers.showErrorAlert('No se pudieron cargar las oficinas. Por favor, intente nuevamente.');
                    }
                },

                populateOfficeSelect() {
                    const officeSelect = this.elements.office_id;
                    if (!officeSelect) return;

                    officeSelect.innerHTML = '<option value="">Seleccione una oficina</option>';
                    this.data.offices.forEach(office => {
                        const option = document.createElement('option');
                        option.value = office.id;
                        option.textContent = office.name;
                        officeSelect.appendChild(option);
                    });
                },

                addSubseries() {
                    const {code} = this.elements;
                    const container = this.elements.subseriesContainer;
                    const index = container.children.length;
                    const num = parseInt(code.value);
                    const cont = index + num;

                    const newItem = document.createElement('div');
                    newItem.className = 'subseries-item row pt-3';
                    newItem.innerHTML = `
                <div class="col-12 col-md-8">
                    <label for="name${index}" class="visually-hidden">Nombre de la Subserie</label>
                    <input type="text" class="form-control" name="subseries[${index}][name]"
                           id="name${index}" placeholder="Nombre de la Subserie" maxlength="100"
                           required>
                </div>
                <div class="col-12 col-md-2">
                    <label for="code${index}" class="visually-hidden">Código de la Subserie</label>
                    <input type="text" class="form-control" name="subseries[${index}][code]"
                           id="code${index}" placeholder="Código de la Subserie" maxlength="10"
                           required value="${cont}" readonly>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger rounded-pill remove-subseries">Eliminar</button>
                </div>
            `;
                    container.appendChild(newItem);
                    const removeButton = newItem.querySelector('.remove-subseries');
                    removeButton.addEventListener('click', () => this.removeSubseries(newItem));
                },

                removeSubseries(item) {
                    this.elements.subseriesContainer.removeChild(item);
                    this.updateSubseriesIndexes();
                },

                updateSubseriesIndexes() {
                    const items = this.elements.subseriesContainer.children;
                    Array.from(items).forEach((item, index) => {
                        const nameInput = item.querySelector('input[name^="subseries"][name$="[name]"]');
                        const codeInput = item.querySelector('input[name^="subseries"][name$="[code]"]');

                        if (nameInput) {
                            nameInput.name = `subseries[${index}][name]` || '';
                            nameInput.id = `name${index}` || '';
                        }
                        if (codeInput) {
                            codeInput.name = `subseries[${index}][code]` || '';
                            codeInput.id = `code${index}` || '';
                        }
                    });
                },

                async createSerie(event) {
                    event.preventDefault();
                    if (this.data.isSubmitting) return;

                    if ($(this.elements.seriesForm).valid()) {
                        this.setData({isSubmitting: true});
                        const formData = this.collectFormData();
                        try {
                            const response = await HTTPService.post('/api/dashboard/retencion-documental/store', formData);
                            Helpers.getMessage('TRD se creo exitosamente', '/dashboard/tabla-de-retencion-documental');
                        } catch (error) {
                            console.error('Error al guardar la serie:', error);
                            Helpers.showErrorAlert('Error al guardar la serie', error);
                        } finally {
                            this.setData({isSubmitting: false});
                        }
                    }
                },

                async createSeries(event) {
                    event.preventDefault();

                    if (this.data.isSubmitting) {
                        return;
                    }

                    const {
                        entityId, seriesName
                    } = this.elements;

                    if ($(this.elements.formSeries).valid()) {

                        let formData = {
                            'entity_id': entityId.value,
                            'series_name': seriesName.value,
                        }

                        try {
                            const response = await HTTPService.post('/api/dashboard/series/store/series', formData);
                            console.log('Respuesta del servidor:', formData);

                            this.loadSeries();
                            $('#centermodal').modal('hide');
                        } catch (error) {
                            console.error('Error al crear la serie:', error);
                            Helpers.showErrorAlert('Error al crear la serie', error);
                        } finally {
                            this.setData({isSubmitting: false});
                        }
                    }
                },

                collectFormData() {
                    const {
                        office_id,
                        series_entity_id,
                        series_code,
                        administrative_retention,
                        central_retention,
                        disposal_procedure,
                        entityId
                    } = this.elements;

                    const subseriesInputs = document.querySelectorAll('input[name^="subseries"]');
                    const subseries = [];
                    subseriesInputs.forEach(input => {
                        const match = input.name.match(/subseries\[(\d+)\]\[(\w+)\]/);
                        if (match) {
                            const index = parseInt(match[1]);
                            const property = match[2];
                            if (!subseries[index]) subseries[index] = {};
                            subseries[index][property] = input.value;
                        }
                    });

                    const dispositionTypes = Array.from(document.querySelectorAll('input[name="disposition_type"]:checked'))
                        .map(input => input.value);

                    const documentaryTypes = Array.from(document.querySelectorAll('input[name="documentary_types"]:checked'))
                        .map(input => input.value);

                    return {
                        office_id: parseInt(office_id.value),
                        series_entity_id: parseInt(series_entity_id.value),
                        series_code: series_code.value,
                        subseries: subseries.filter(Boolean),
                        disposition_type: dispositionTypes,
                        administrative_retention: parseInt(administrative_retention.value),
                        central_retention: parseInt(central_retention.value),
                        disposal_procedure: disposal_procedure.value,
                        documentary_types: documentaryTypes,
                        entity_id: entityId.value,
                    };
                },

                setupValidation() {
                    $.validator.setDefaults({
                        errorClass: 'is-invalid',
                        validClass: 'is-valid',
                        errorElement: 'div',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass(errorClass).removeClass(validClass);
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass(errorClass).addClass(validClass);
                        }
                    });

                    $(this.elements.seriesForm).validate({
                        rules: {
                            office_id: 'required',
                            series_entity_id: 'required',
                            series_code: {
                                required: true,
                                maxlength: 10
                            },
                            'subseries[0][name]': {
                                required: true,
                                maxlength: 100
                            },
                            'subseries[0][code]': {
                                required: true,
                                maxlength: 10
                            },
                            administrative_retention: {
                                required: true,
                                number: true
                            },
                            central_retention: {
                                required: true,
                                number: true
                            },
                            'disposition_type[]': 'required',
                            'documentary_types[]': 'required'
                        },
                        messages: {
                            office_id: 'Por favor, seleccione una oficina',
                            series_entity_id: 'Por favor, seleccione una serie',
                            series_code: {
                                required: 'Por favor, ingrese el código de la serie',
                                maxlength: 'El código de la serie no debe exceder los 10 caracteres'
                            },
                            'subseries[0][name]': {
                                required: 'Por favor, ingrese el nombre de la subserie',
                                maxlength: 'El nombre de la subserie no debe exceder los 100 caracteres'
                            },
                            'subseries[0][code]': {
                                required: 'Por favor, ingrese el código de la subserie',
                                maxlength: 'El código de la subserie no debe exceder los 10 caracteres'
                            },
                            administrative_retention: {
                                required: 'Por favor, ingrese la retención administrativa',
                                number: 'Por favor, ingrese un número válido'
                            },
                            central_retention: {
                                required: 'Por favor, ingrese la retención central',
                                number: 'Por favor, ingrese un número válido'
                            },
                            'disposition_type[]': 'Por favor, seleccione al menos un tipo de disposición',
                            'documentary_types[]': 'Por favor, seleccione al menos un tipo documental'
                        },
                        submitHandler: (form, event) => {
                            event.preventDefault();
                            this.createSerie(event);
                        }
                    });

                    $(this.elements.formSeries).validate({
                        rules: {
                            series_name: {
                                required: true,
                                maxlength: 100
                            }
                        },
                        messages: {
                            series_name: {
                                required: 'Por favor, ingrese el nombre de la serie',
                                maxlength: 'El nombre de la serie no debe exceder los 100 caracteres'
                            }
                        },
                        submitHandler: (form, event) => {
                            event.preventDefault();
                            this.createSeries(event);
                        }
                    });
                },

                handleOfficeChange(event) {
                    const series_entity_id = event.target.value;
                    console.log(series_entity_id)
                    if (series_entity_id) {
                        this.loadCounter(series_entity_id);
                    }
                },

                async loadSeries() {
                    try {
                        const entityId = localStorage.getItem('entity_id');
                        if (!entityId) {
                            return;
                        }
                        const response = await HTTPService.get(`/api/dashboard/series/${entityId}`);

                        const seriesSelect = this.elements.series_entity_id;
                        seriesSelect.innerHTML = '<option value="">Seleccione una serie</option>';

                        if (Array.isArray(response.data)) {
                            response.data.forEach(series => {
                                seriesSelect.innerHTML += `<option value="${series.id}">${series.series_name}</option>`;
                            });
                        } else {
                            console.error('La respuesta no es un array:', response.data);
                        }
                    } catch (error) {
                        console.error('Error al cargar las series:', error);
                    }
                }
            },

            elements: {
                seriesForm: '#seriesForm',
                formSeries: '#formSeries',
                code: '#code',
                office_id: '#office_id',
                series_entity_id: '#series_entity_id',
                series_code: '#series_code',
                administrative_retention: '#administrative_retention',
                central_retention: '#central_retention',
                subseriesContainer: '#subseries-container',
                disposal_procedure: '#disposal_procedure',
                addSubseriesButton: '#add-subseries',
                entityId: '#entity_id',
                seriesName: '#series_name',
                submitButton: '#submitButton',
                BtnFormSeries: '#BtnFormSeries'
            },

            created() {
                this.loadOffices();
                this.elements.addSubseriesButton.addEventListener('click', () => this.addSubseries());
                this.elements.series_entity_id.addEventListener('change', this.handleOfficeChange.bind(this));
                this.elements.entityId.value = localStorage.getItem('entity_id');
                this.elements.BtnFormSeries.addEventListener('click', this.createSeries.bind(this));

            },

            render() {
                this.setupValidation();
                this.loadSeries();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            SeriesCreateComponent.init();
        });
    </script>
@endsection