@extends('layouts.app')
@section('title', 'Crear Series y Subseries Documentales')
@section('content')
    <div class="dashboard">
        {{-- The 'trd.create' permission may need adjustment if permission logic changes --}}
        <div data-permissions="trd.create">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="card mb-0 h-100">
                        <div class="card-body">
                            <h4 class="header-title">Crear Serie y Subseries Documentales</h4>
                            <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate">
                                Cree nuevas subseries para una serie documental existente o cree una nueva serie.
                            </h5>
                            <div id="error-container" class="alert alert-danger" style="display: none;"></div>

                            ---

                            {{-- Main Form to add Subseries to an existing Series --}}
                            <form id="seriesForm" method="POST">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label for="series_entity_id" class="form-label">Nombre de la Serie</label>
                                        <select name="series_entity_id" id="series_entity_id" class="form-control" required>
                                            <option value="">Cargando series...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="series_code" class="form-label">Código de la Serie</label>
                                        <input type="text" class="form-control" id="series_code" name="series_code"
                                            required maxlength="10" autocomplete="off" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        {{-- Button to open the modal and create a NEW Series --}}
                                        <button type="button" data-permissions="entityseries.create"
                                            class="btn btn-secondary rounded-pill btn-sm mt-3" data-bs-toggle="modal"
                                            data-bs-target="#createSeriesModal">
                                            <i class="fas fa-plus me-1"></i> Crear Nueva Serie
                                        </button>
                                    </div>
                                </div>

                                <hr>

                                <h6>Añadir Subseries</h6>
                                <div class="row">
                                    {{-- Initial row for the first subseries --}}
                                    <div class="col-12 col-md-8 mb-2"> {{-- Added mb-2 for spacing below --}}
                                        <input type="text" class="form-control" name="subseries[0][name]"
                                            id="subseries_name_0" placeholder="Nombre de la Subserie" maxlength="100"
                                            required autocomplete="off">
                                    </div>
                                    <div class="col-12 col-md-2 mb-2"> {{-- Added mb-2 for spacing below --}}
                                        <input type="text" class="form-control" name="subseries[0][code]"
                                            id="subseries_code_0" placeholder="Código" maxlength="10" required
                                            autocomplete="off" readonly>
                                    </div>
                                    <div class="col-12 col-md-2 mb-2"> {{-- Added mb-2 for spacing below --}}
                                        <button type="button" id="add-subseries"
                                            class="btn btn-info rounded-pill btn-sm mt-0">
                                            <i class="fas fa-plus me-1"></i> Agregar Fila
                                        </button>
                                    </div>
                                </div>

                                {{-- Container for additional subseries --}}
                                <div id="subseries-container">
                                    {{-- New subseries rows will be added here dynamically --}}
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ url()->previous() }}" class="btn btn-light rounded-pill me-2">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success rounded-pill" id="submitButton">
                                        <i class="fas fa-check me-1"></i> Guardar Subseries
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3"> {{-- Added mt-3 for spacing from the form above --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Series y Subseries Documentales</h5>

                    {{-- Accordion Container --}}
                    <div class="accordion" id="seriesAccordion">
                        {{-- Initial loading message --}}
                        <div id="loading-message" class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando datos...</p>
                        </div>

                        {{-- Message for when no data is found --}}
                        <div id="no-data-message" class="alert alert-info text-center" style="display: none;">
                            No se encontraron series documentales para esta entidad.
                        </div>

                        {{-- Accordion content will be generated here with JavaScript --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div id="createSeriesModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="createSeriesModalLabel">Crear Nueva Serie</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form name="formCreateSeries" id="formCreateSeries" method="POST">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="series_name" class="form-label">Nombre de la Serie</label>
                            <input type="hidden" name="entity_id" id="entity_id">
                            <input type="text" class="form-control" name="series_name" id="series_name"
                                placeholder="Escriba el nombre de la nueva serie" required autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnSaveNewSeries" class="btn btn-primary rounded-pill">Guardar
                            Serie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSubseriesModal" tabindex="-1" aria-labelledby="editSubseriesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubseriesModalLabel">Editar Subserie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSubseriesForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_subseries_id" name="subseries_id">
                        <div class="mb-3">
                            <label for="edit_subseries_name" class="form-label">Nombre de la Subserie</label>
                            <input type="text" class="form-control" id="edit_subseries_name" name="subseries_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_subseries_code" class="form-label">Código</label>
                            <input type="text" class="form-control" id="edit_subseries_code" name="subseries_code"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        async function loadSeries() {
            try {
                const userData = HTTPService.getUserData();

                const response = await HTTPService.get(`/api/dashboard/series/${userData.entity_id}`);
                const seriesEntity = response.data;

                const select = document.getElementById('series_entity_id');
                select.innerHTML = '<option value="">Seleccione...</option>';

                seriesEntity.forEach(seriesEntity => {
                    select.innerHTML +=
                        `<option value="${seriesEntity.id}" data-code="${seriesEntity.id}">${seriesEntity.series_name}</option>`;
                });
            } catch (error) {
                console.error('Error al obtener las series', error);
            }
        }

        async function loadCounters(seriesId) {
            const seriesCodeEl = document.getElementById('series_code');
            const subseriesCodeEl = document.getElementById('subseries_code_0');

            try {
                const response = await HTTPService.get(`/api/get-all-counters/${seriesId}`);
                const counters = response.data;

                if (!counters || counters.length === 0) {
                    const selectedOption = document.querySelector('#series_entity_id option:checked');
                    if (selectedOption) {
                        seriesCodeEl.value = selectedOption.dataset.code || '';
                    }
                    if (subseriesCodeEl) {
                        subseriesCodeEl.value = '1';
                    }
                    return;
                }

                const firstCounter = counters[0];

                if (firstCounter.parent_count === null || firstCounter.max_child === null) {
                    const selectedOption = document.querySelector('#series_entity_id option:checked');
                    if (selectedOption) {
                        seriesCodeEl.value = selectedOption.dataset.code || '';
                    }
                    if (subseriesCodeEl) {
                        subseriesCodeEl.value = '1';
                    }
                } else {
                    seriesCodeEl.value = firstCounter.parent_count?.toString() || '';
                    if (subseriesCodeEl) {
                        subseriesCodeEl.value = (parseInt(firstCounter.max_child) + 1).toString();
                    }
                }

            } catch (error) {
                console.error('Error al cargar los contadores:', error);
                if (seriesCodeEl) {
                    seriesCodeEl.value = 'Error';
                }
                if (subseriesCodeEl) {
                    subseriesCodeEl.value = 'Error';
                }
            }
        }

        async function addSubseriesRow() {
            const container = document.querySelector('#subseries-container');
            const lastCodeInput = container.lastElementChild ?
                container.lastElementChild.querySelector('input[name$="[code]"]') :
                document.querySelector('#subseries_code_0');

            const nextIndex = document.querySelectorAll('input[name^="subseries["][name$="][name]"]').length;
            const nextCode = lastCodeInput && !isNaN(parseInt(lastCodeInput.value)) ? parseInt(lastCodeInput.value) +
                1 : 1;

            const newItem = document.createElement('div');
            newItem.className = 'subseries-item row pt-3';
            newItem.innerHTML = `
                <div class="col-12 col-md-8">
                    <input type="text" class="form-control" name="subseries[${nextIndex}][name]" placeholder="Nombre de la Subserie" maxlength="100" required autocomplete="off">
                </div>
                <div class="col-12 col-md-2">
                    <input type="text" class="form-control" name="subseries[${nextIndex}][code]" value="${nextCode}" placeholder="Código" maxlength="10" required readonly autocomplete="off">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger rounded-pill remove-subseries">Eliminar</button>
                </div>
            `;
            container.appendChild(newItem);
            newItem.querySelector('.remove-subseries').addEventListener('click', () =>
                removeSubseriesRow(newItem));
        }

        async function removeSubseriesRow(item) {
            item.remove();
            await updateSubseriesIndexes();
        }

        async function updateSubseriesIndexes() {
            const initialSubseriesCodeValue = parseInt(document.querySelector('#subseries_code_0').value);
            const container = document.querySelector('#subseries-container');
            const dynamicSubseriesItems = container.querySelectorAll('.subseries-item');

            dynamicSubseriesItems.forEach((item, index) => {
                const nameInput = item.querySelector('input[name$="[name]"]');
                const codeInput = item.querySelector('input[name$="[code]"]');
                const newArrayIndex = index + 1;

                if (nameInput) {
                    nameInput.name = `subseries[${newArrayIndex}][name]`;
                }
                if (codeInput) {
                    codeInput.name = `subseries[${newArrayIndex}][code]`;
                    codeInput.value = (initialSubseriesCodeValue + newArrayIndex).toString();
                }
            });
        }

        async function collectMainFormData() {
            const form = document.querySelector('#seriesForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const subseries = Array.from(form.querySelectorAll('input[name^="subseries"]'))
                .reduce((acc, input) => {
                    const match = input.name.match(/subseries\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const [, index, key] = match;
                        if (!acc[index]) acc[index] = {};
                        acc[index][key] = input.value;
                    }
                    return acc;
                }, []).filter(Boolean);

            return {
                series_entity_id: data.series_entity_id,
                series_code: data.series_code,
                subseries: subseries
            };
        }

        const seriesSelect = document.getElementById('series_entity_id');

        seriesSelect.addEventListener('change', async (event) => {
            const selectedSeriesId = event.target.value;
            await loadCounters(selectedSeriesId);
        });

        const addSubseriesBtn = document.querySelector('#add-subseries');
        addSubseriesBtn.addEventListener('click', async (event) => {
            addSubseriesRow();
        });

        async function getSubseriesValues() {
            const subseriesInputs = document.querySelectorAll(
                'input[name^="subseries["][name$="][name]"], input[name^="subseries["][name$="][code]"]');
            const subseriesData = {};

            subseriesInputs.forEach(input => {
                const match = input.name.match(/subseries\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    const [, index, key] = match;
                    if (!subseriesData[index]) {
                        subseriesData[index] = {};
                    }
                    subseriesData[index][key] = input.value;
                }
            });

            return Object.values(subseriesData);
        }

        async function save() {
            const formData = await collectMainFormData();
            try {
                const response = await HTTPService.post('/api/dashboard/subseries/store-multiple', formData);

                Helpers.getMessage('Subserie creada exitosamente', '/dashboard/series');

               // console.log('Datos de la serie y subseries:', formData);

            } catch (error) {
                console.error('Error al almacenar la subserie', error);
                alert("Hubo un error al registrar la subserie.");
            }
        }

        async function saveNewSeries(event) {
            event.preventDefault();

            if (!$('#formCreateSeries').valid()) {
                return;
            }

            const form = document.getElementById('formCreateSeries');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const createSeriesModal = bootstrap.Modal.getInstance(document.getElementById('createSeriesModal'));

            try {
                const response = await HTTPService.post('/api/dashboard/series/store', data);
                if (createSeriesModal) {
                    createSeriesModal.hide();
                }
                Helpers.getMessage('Serie creada exitosamente', '/dashboard/series');
            } catch (error) {
                console.error('Error al crear la serie:', error);
                const errorMsg = error.response?.data?.message || 'Ocurrió un error al crear la serie.';
                alert(errorMsg);
            }
        }

        $(document).ready(function() {
            $("#seriesForm").validate({
                rules: {
                    series_entity_id: "required",
                    series_code: "required",
                    "subseries[0][name]": "required",
                    "subseries[0][code]": "required",
                },
                messages: {
                    series_entity_id: "Por favor, seleccione una serie",
                    series_code: "El código de serie es requerido",
                    "subseries[0][name]": "El nombre de la subserie es requerido",
                    "subseries[0][code]": "El código de la subserie es requerido",
                },
                submitHandler: function(form) {
                    save();
                }
            });

            $("#formCreateSeries").validate({
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
                    saveNewSeries(event);
                }
            });
        });

        function createSeriesAccordionItem(series) {
            const hasSubseries = series.subseries && series.subseries.length > 0;

            const subseriesContent = hasSubseries ?
                `<ul class="list-group list-group-flush subseries-list">
                    ${series.subseries.map(sub => `
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="text-muted me-3">Cód: ${sub.subseries_code}</span>
                                                    <span>${sub.subseries_name}</span>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-primary py-0 px-1 edit-btn"
                                                            title="Editar Subserie"
                                                            data-id="${sub.id}"
                                                            data-name="${sub.subseries_name}"
                                                            data-code="${sub.subseries_code}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger py-0 px-1 delete-btn"
                                                            title="Eliminar Subserie"
                                                            data-id="${sub.id}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        `).join('')}
                </ul>` :
                `<div class="p-3 text-center text-muted fst-italic">Esta serie no tiene subseries registradas.</div>`;

            return `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-${series.id}">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${series.id}" aria-expanded="false" aria-controls="collapse-${series.id}">
                            <span class="badge bg-secondary me-2">SERIE: ${series.id}</span>
                            ${series.series_name}
                            <span class="badge bg-primary rounded-pill ms-auto me-3">${series.subseries.length} Subseries</span>
                        </button>
                    </h2>
                    <div id="collapse-${series.id}" class="accordion-collapse collapse" aria-labelledby="heading-${series.id}" data-bs-parent="#seriesAccordion">
                        <div class="accordion-body">
                            ${subseriesContent}
                        </div>
                    </div>
                </div>
            `;
        }

        async function loadAndDisplaySeries() {
            const accordionContainer = document.getElementById('seriesAccordion');
            const loadingMessage = document.getElementById('loading-message');
            const noDataMessage = document.getElementById('no-data-message');

            try {
                const userData = HTTPService.getUserData();
                if (!userData || !userData.entity_id) {
                    throw new Error('No se pudo obtener la información del usuario o la entidad.');
                }
                document.getElementById('entity_id').value = userData.entity_id
                const response = await HTTPService.get(`/api/dashboard/series/with-subseries/${userData.entity_id}`);
                const seriesList = response.data;

                loadingMessage.style.display = 'none';

                if (!seriesList || seriesList.length === 0) {
                    noDataMessage.style.display = 'block';
                } else {
                    const allItemsHtml = seriesList.map(createSeriesAccordionItem).join('');
                    accordionContainer.innerHTML = allItemsHtml;
                }
            } catch (error) {
                console.error('Error al cargar la Tabla de Retención Documental:', error);
                loadingMessage.style.display = 'none';
                accordionContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <strong>Error:</strong> No se pudo cargar la información. Por favor, intente de nuevo más tarde.
                </div>
            `;
            }
        }

        const editSubseriesModal = new bootstrap.Modal(document.getElementById('editSubseriesModal'));

        function openEditModal(button) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const code = button.dataset.code;

            document.getElementById('edit_subseries_id').value = id;
            document.getElementById('edit_subseries_name').value = name;
            document.getElementById('edit_subseries_code').value = code;

            editSubseriesModal.show();
        }

        async function handleEditFormSubmit(event) {
            event.preventDefault();

            const form = event.target;
            const subseriesId = form.querySelector('#edit_subseries_id').value;
            const formData = {
                subseries_name: form.querySelector('#edit_subseries_name').value,
                subseries_code: form.querySelector('#edit_subseries_code').value,
            };

            try {
                await HTTPService.put(`/api/dashboard/subseries/${subseriesId}`, formData);

                editSubseriesModal.hide();
                Helpers.getMessage('Subserie actualizada exitosamente', '/dashboard/series');

            } catch (error) {
                console.error('Error al actualizar la subserie:', error);
                alert('Ocurrió un error al guardar los cambios.');
            }
        }

        document.getElementById('editSubseriesForm').addEventListener('submit', handleEditFormSubmit);

        document.getElementById('seriesAccordion').addEventListener('click', function(event) {
            const editButton = event.target.closest('.edit-btn');
            if (editButton) {
                openEditModal(editButton);
            }

            const deleteButton = event.target.closest('.delete-btn');
            if (deleteButton) {
                // Aquí puedes poner la lógica para eliminar, por ejemplo con SweetAlert2
                console.log("Se hizo clic en eliminar la subserie con ID:", deleteButton.dataset.id);
            }
        });

        loadSeries();
        loadAndDisplaySeries();
    </script>
@endsection
