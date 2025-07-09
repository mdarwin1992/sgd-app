@extends('layouts.app')
@section('title', 'Envios')
@section('content')
    <div class="dashboard">
        <div data-permissions="sendings.create" class="visible">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-6 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title"> Recepción</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Recibe y registra documentos
                                    </h5>
                                    <form id="AddForm" name="AddForm">
                                        <div class="row">
                                            <div class="col-md-2 mb-3">
                                                <label for="send_date">Fecha Envío</label>
                                                <input type="date" class="form-control" id="send_date" name="send_date"
                                                    value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="id">Nº Radicado</label>
                                                <input type="text" class="form-control" id="reference_code"
                                                    name="reference_code" value="" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="subject">Asunto</label>
                                                <input type="text" class="form-control" id="subject" name="subject"
                                                    value="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label for="sender">Remitente</label>
                                                <input type="text" class="form-control" id="sender" name="sender"
                                                    value="">
                                            </div>
                                            <div class="col-md-5 mb-3">
                                                <label for="recipient">Destinatario</label>
                                                <input type="email" class="form-control" id="recipient" name="recipient"
                                                    value="">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label for="page_count">N° Folios</label>
                                                <input type="number" class="form-control" id="page_count" name="page_count"
                                                    value="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="department_id">Dependencia</label>
                                                <select class="form-control" id="department_id" name="department_id"
                                                    required>
                                                    <option value="">Seleccione una oficina</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-lg-4 form-group">
                                                <label for="recipient_department_id">Oficina Destino:</label>
                                                <select class="form-control" id="office_id" name="office_id" required>
                                                    <option value="">Seleccione una oficina</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-lg-4 form-group">
                                                <label for="document_path">Ruta de Origen (Copiar Desde)</label>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" id="filepath"
                                                        name="filepath">
                                                    <input type="hidden" class="form-control" id="document_path"
                                                        name="document_path">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard/business"
                                                class="btn btn-primary rounded-pill btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
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
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const SendCreate = (() => {
            let isSubmitting = false;
            let counter = null;
            let filePath = '';
            let departments = [];
            let selectedDepartment = null;
            let lastFetchedDepartment = null;
            let office = [];
            let selectedOffice = null;

            const elements = {
                AddForm: '#AddForm',
                submitButton: '#submitButton',
                departmentSelect: '#department_id',
                officeSelect: '#office_id',
                referenceCode: '#reference_code',
                fileInput: 'input[name="filepath"]',
                filePath: '#document_path',
                send_date: '#send_date',
                subject: '#subject',
                sender: '#sender',
                recipient: '#recipient',
                page_count: '#page_count',
                department_id: '#department_id',
                office_id: '#office_id',
            };

            const getElement = (selector) => document.querySelector(selector);

            const loadCounter = async () => {
                const referenceCode = getElement(elements.referenceCode);
                const entity = HTTPService.getUserData('entity_id')

                if (!entity.entity_id || !selectedOffice) {
                    console.error(
                        'No se encontró el entity_id en localStorage o no se ha seleccionado una oficina.'
                    );
                    return;
                }

                try {
                    const counterResponse = await HTTPService.get(
                        `/api/entity/${entity.entity_id}/counter/2`);
                    counter = counterResponse;
                    const formattedReferenceCode = selectedOffice.toString().padStart(3, '0') + (
                        counterResponse.reference_code || '').padStart(7, '0');
                    referenceCode.value = formattedReferenceCode;
                } catch (error) {
                    console.error('Error al cargar el contador:', error);
                }
            };

            const getDepartments = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/departments');
                    if (response && Array.isArray(response.data) && response.data.length > 0) {
                        departments = response.data;
                        console.log(response);
                    } else {
                        departments = [];
                        console.error('No hay departamentos disponibles.');
                    }
                } catch (err) {
                    console.error('Error al obtener los departamentos:', err);
                    departments = [];
                } finally {
                    renderDepartments();
                }
            };

            const fetchOffice = async (departmentId) => {
                if (lastFetchedDepartment === departmentId) {
                    return;
                }
                lastFetchedDepartment = departmentId;

                try {
                    const response = await HTTPService.get(
                        `/api/dashboard/departments/${departmentId}/offices`);

                    if (response && Array.isArray(response.data)) {
                        office = response.data;
                    } else {
                        office = [];
                        console.error('No hay oficinas disponibles para este departamento.');
                    }
                } catch (err) {
                    console.error('Error al obtener las oficinas:', err);
                    office = [];
                } finally {
                    renderOffice();
                }
            };

            /*  const handleFileChange = async (event) => {
                 const url = '/api/dashboard/upload';
                 try {
                     const uploadedData = await HTTPService.upload(url, event);
                     filePath = uploadedData;
                     getElement(elements.filePath).value = uploadedData;
                 } catch (error) {
                     console.error('Error al cargar el archivo:', error.message);
                 }
             }; */

            const handleFileChange = async (event) => {
                // 1. Obtener el archivo desde el evento.
                // El objeto 'File' está en `event.target.files[0]`.
                const fileToUpload = event.target.files[0];

                // 2. (RECOMENDADO) Añadir una validación para asegurarse de que se seleccionó un archivo.
                if (!fileToUpload) {
                    console.log("No se seleccionó ningún archivo. La operación se ha cancelado.");
                    return; // Detener la ejecución si no hay archivo
                }

                const systemCode = $('#reference_code').val();
                if (!systemCode) {
                    //console.error("El código del sistema no está definido. Por favor, asegúrate de que el campo 'reference_code' tenga un valor.");
                    return; // Detener la ejecución si no hay código del sistema
                } else {
                    console.log("Código del sistema:", systemCode);
                }

                // 3. Definir el endpoint y los datos adicionales si los necesitas.
                // He cambiado la URL para que coincida con el controlador de Laravel que hicimos.
                // Si tu ruta es diferente, ajústala aquí.
                const url = '/api/dashboard/upload'; // O la ruta correcta a tu controlador

                // ¡IMPORTANTE! Si tu endpoint 'upload' necesita datos adicionales,
                // como 'reference_code', debes añadirlos aquí.
                const additionalData = {
                    reference_code: systemCode // Sustituye esto por el valor real
                };

                try {
                    //console.log("Iniciando subida para el archivo:", fileToUpload.name);

                    // 4. Llamar al servicio de subida con los parámetros CORRECTOS:
                    //    - url
                    //    - el objeto File
                    //    - los datos adicionales
                    const response = await HTTPService.upload(url, fileToUpload, additionalData);

                    //console.log('Archivo subido con éxito. Respuesta del servidor:', response);

                    // El backend ahora devuelve: { data: { url: '...', path: '...' }, message: '...' }
                    // La ruta del archivo para guardar está en `response.data.path`.
                    // La URL pública está en `response.data.url`.

                    // Decide qué valor quieres guardar. `response.data.path` es usualmente
                    // lo que se almacena en la base de datos.
                    const filePathFromServer = response.data;


                    // 5. Actualizar la UI con la ruta obtenida del servidor.
                    const filePathInput = document.querySelector(elements.filePath);
                    if (filePathInput) {
                        filePathInput.value = filePathFromServer;
                    } else {
                        console.warn("No se encontró el elemento para mostrar la ruta del archivo.");
                    }

                } catch (error) {
                    // HTTPService ya maneja bien los errores, aquí solo los mostramos.
                    console.error('Error durante la carga del archivo:', error.message);
                    // Podrías mostrar este error al usuario en un elemento del DOM.
                    // ej. document.getElementById('error-message').textContent = error.message;
                }
            };

            const createSend = async (event) => {
                event.preventDefault();

                if (isSubmitting) {
                    return;
                }

                if ($(elements.AddForm).valid()) {
                    isSubmitting = true;

                    const form = getElement(elements.AddForm);
                    const formData = new FormData(form);
                    const formDataObject = Object.fromEntries(formData.entries());

                    try {
                        const response = await HTTPService.post('api/dashboard/document-sendings/store',
                            formDataObject);
                        Helpers.getMessage('Se ha guardado correctamente',
                            '/dashboard/ventanilla-unica/recepcion');
                    } catch (error) {
                        console.error('Error al crear la recepción:', error);
                    } finally {
                        isSubmitting = false;
                    }
                }
            };

            const renderDepartments = () => {
                const departmentSelect = getElement(elements.departmentSelect);
                departmentSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
                if (Array.isArray(departments) && departments.length > 0) {
                    departmentSelect.innerHTML += departments
                        .map(department =>
                            `<option value="${department.id}" ${selectedDepartment == department.id ? 'selected' : ''}>${department.name}</option>`
                        )
                        .join('');
                } else {
                    departmentSelect.innerHTML = '<option>No hay departamentos disponibles</option>';
                }
            };

            const renderOffice = () => {
                const officeSelect = getElement(elements.officeSelect);
                officeSelect.innerHTML = '<option value="">Seleccione una oficina</option>';
                if (Array.isArray(office) && office.length > 0) {
                    officeSelect.innerHTML += office
                        .map(branches => `<option value="${branches.id}">${branches.name}</option>`)
                        .join('');
                } else {
                    officeSelect.innerHTML = '<option>No hay oficinas disponibles</option>';
                }
            };

            const handleDepartmentChange = (event) => {
                const departmentId = event.target.value;
                selectedDepartment = departmentId;
                if (departmentId) {
                    fetchOffice(departmentId);
                } else {
                    office = [];
                    lastFetchedDepartment = null;
                    selectedOffice = null;
                    renderOffice();
                    getElement(elements.referenceCode).value = '';
                }
            };

            const handleOfficeChange = (event) => {
                const officeId = event.target.value;
                selectedOffice = officeId;
                if (officeId) {
                    loadCounter();
                } else {
                    getElement(elements.referenceCode).value = '';
                }
            };

            const setupValidation = () => {
                $.validator.setDefaults({
                    errorClass: 'is-invalid',
                    validClass: 'is-valid',
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        error.insertAfter(element);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass(errorClass).removeClass(validClass);
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass(errorClass).addClass(validClass);
                    }
                });

                $(elements.AddForm).validate({
                    rules: {
                        send_date: {
                            required: true,
                            date: true
                        },
                        subject: "required",
                        sender: "required",
                        recipient: "required",
                        page_count: {
                            required: true,
                            number: true,
                            min: 1
                        },
                        department_id: {
                            required: true,
                            number: true
                        },
                        office_id: {
                            required: true,
                            number: true
                        },
                        document_path: "required"
                    },
                    messages: {
                        send_date: {
                            required: "Por favor, ingrese la fecha de envío",
                            date: "Por favor, ingrese una fecha válida"
                        },
                        subject: "Por favor, ingrese el asunto del documento",
                        sender: "Por favor, ingrese el nombre del remitente",
                        recipient: "Por favor, ingrese el nombre del destinatario",
                        page_count: {
                            required: "Por favor, ingrese el número de páginas",
                            number: "Por favor, ingrese un número válido",
                            min: "El número de páginas debe ser al menos 1"
                        },
                        department_id: {
                            required: "Por favor, seleccione un departamento",
                            number: "Por favor, seleccione un departamento válido"
                        },
                        office_id: {
                            required: "Por favor, seleccione una oficina",
                            number: "Por favor, seleccione una oficina válida"
                        },
                        document_path: "Por favor, ingrese la ruta del documento"
                    },
                    submitHandler: (form, event) => {
                        event.preventDefault();
                        createSend(event);
                    }
                });
            };

            const init = () => {
                getDepartments();
                getElement(elements.departmentSelect).addEventListener('change', handleDepartmentChange);
                getElement(elements.officeSelect).addEventListener('change', handleOfficeChange);
                getElement(elements.fileInput).addEventListener('change', handleFileChange);
                setupValidation();
            }

            // Initialize when the DOM is ready
            $(document).ready(init);

            // Public API
            const publicApi = {
                handleFileChange,
                createSend
            };

            // Expose to window object for inline event handlers
            window.SendCreate = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
