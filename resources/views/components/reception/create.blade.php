@extends('layouts.app')

@section('title', 'Recepción')

@section('content')
    <div class="dashboard">
        <div data-permissions="reception.create">
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
                                    <form name="AddForm" id="AddForm">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="transfer_status">Estado:</label>
                                                <input type="text" class="form-control" id="transfer_status"
                                                    name="transfer_status" value="RECIBIDA" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="reference_code">Nº Radicado:</label>
                                                <input type="text" class="form-control" id="reference_code"
                                                    name="reference_code" value="" readonly>
                                                <input type="hidden" class="form-control" id="system_code"
                                                    name="system_code" value="" readonly>
                                                <input type="hidden" class="form-control" id="entity_id" name="entity_id"
                                                    value="" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="reference_code">Nº Radicado:</label>
                                                <input type="date" class="form-control" id="received_date"
                                                    name="received_date" value="{{ date('Y-m-d') }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="origin">Procedencia:</label>
                                                <input type="text" class="form-control" id="origin" name="origin"
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="sender_name">Nombre del Remitente:</label>
                                                <input type="text" class="form-control" id="sender_name"
                                                    name="sender_name" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="subject">Asunto:</label>
                                            <input type="text" class="form-control" id="subject" name="subject"
                                                autocomplete="off">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="has_attachments">Anexos:</label>
                                                <select class="form-control" id="has_attachments" name="has_attachments">
                                                    <option value="">Seleccione</option>
                                                    <option value="SI">Sí</option>
                                                    <option value="NO">No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="page_count">Nº Folios:</label>
                                                <input type="number" class="form-control" id="page_count"
                                                    name="page_count">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="filepath">Ruta de Origen (Copiar Desde):</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="filepath" name="filepath">
                                                <input type="hidden" class="form-control" id="file_path" name="file_path"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard" class="btn btn-primary btn-tool rounded-pill me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success rounded-pill" id="submitButton">
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
@endsection
@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const ReceptionComponent = (() => {
            // Private variables
            let isSubmitting = false;
            let counter = null;
            let filePath = '';

            const elements = {
                AddForm: '#AddForm',
                submitButton: '#submitButton',
                referenceCode: '#reference_code',
                systemCode: '#system_code',
                receivedDate: '#received_date',
                origin: '#origin',
                senderName: '#sender_name',
                subject: '#subject',
                hasAttachments: '#has_attachments',
                pageCount: '#page_count',
                fileInput: 'input[name="filepath"]',
                filePath: '#file_path',
                entity_id: '#entity_id'
            };

            // Private methods
            const loadCounter = async () => {
                const entity = HTTPService.getUserData('entity_id')
                if (!entity.entity_id) {
                    console.error('No se encontró el entity_id en localStorage.');
                    return;
                }
                // console.log('Cargando contador para entity_id:', entity.entity_id);

                try {
                    const counterResponse = await HTTPService.get(
                        `/api/entity/${entity.entity_id}/counter/1`);
                    counter = counterResponse;
                    document.querySelector(elements.referenceCode).value = counterResponse.reference_code ||
                        '';
                    document.querySelector(elements.systemCode).value = counterResponse.system_code || '';
                    document.querySelector(elements.entity_id).value = counterResponse.entity_id || '';
                } catch (error) {
                    console.error('Error al cargar el contador:', error);
                }
            };

            const createReception = async (event) => {
                event.preventDefault();

                if (isSubmitting) {
                    return;
                }

                if ($(elements.AddForm).valid()) {
                    isSubmitting = true;

                    const form = document.querySelector(elements.AddForm);
                    const formData = new FormData(form);
                    const formDataObject = Object.fromEntries(formData);

                    try {

                        const userData = HTTPService.getUserData();
                        const response = await HTTPService.post('/api/dashboard/reception/store',
                            formDataObject);


                        Helpers.getMessage('Se ha guardado correctamente',
                            '/dashboard/ventanilla-unica/recepcion');
                        window.open(
                            `/dashboard/ticket/${response.data.reference_code}/${userData.entity_id}`,
                            '_blank');
                    } catch (error) {
                        console.error('Error al crear la recepción:', error);
                    } finally {
                        isSubmitting = false;
                    }
                }
            };

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

                $(elements.AddForm).validate({
                    rules: {
                        reference_code: "required",
                        system_code: "required",
                        received_date: {
                            required: true,
                            date: true
                        },
                        origin: "required",
                        sender_name: "required",
                        subject: "required",
                        has_attachments: "required",
                        page_count: {
                            required: true,
                            number: true,
                            min: 1
                        },
                        file_path: "required"
                    },
                    messages: {
                        reference_code: "Por favor, ingrese un código de referencia",
                        system_code: "Por favor, ingrese un código de sistema",
                        received_date: {
                            required: "Por favor, ingrese la fecha de recepción",
                            date: "Por favor, ingrese una fecha válida"
                        },
                        origin: "Por favor, ingrese el origen",
                        sender_name: "Por favor, ingrese el nombre del remitente",
                        subject: "Por favor, ingrese el asunto",
                        has_attachments: "Por favor, indique si tiene adjuntos",
                        page_count: {
                            required: "Por favor, ingrese el número de páginas",
                            number: "Por favor, ingrese un número válido",
                            min: "El número de páginas debe ser al menos 1"
                        },
                        file_path: "Por favor, seleccione un archivo"
                    },
                    submitHandler: (form, event) => {
                        event.preventDefault();
                        createReception(event);
                    }
                });
            };

            // Initialize when DOM is ready
            $(document).ready(() => {
                setupValidation();
                loadCounter();

                // Set up event listeners
                document.querySelector(elements.fileInput).addEventListener('change', handleFileChange);
                document.querySelector(elements.AddForm).addEventListener('submit', createReception);
            });

            // Create public API
            const publicApi = {
                loadCounter,
                createReception,
                handleFileChange,
                setupValidation
            };

            // Expose to window object for inline event handlers
            window.DocumentManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();
    </script>
@endsection
