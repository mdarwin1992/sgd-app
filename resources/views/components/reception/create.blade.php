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
                const entityId = localStorage.getItem('entity_id');
                if (!entityId) {
                    console.error('No se encontró el entity_id en localStorage.');
                    return;
                }

                try {
                    const counterResponse = await HTTPService.get(`/api/entity/${entityId}/counter/1`);
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
                        const response = await HTTPService.post('/api/dashboard/reception/store',
                            formDataObject);
                        Helpers.getMessage('Se ha guardado correctamente',
                            '/dashboard/ventanilla-unica/recepcion');
                        window.open(
                            `/dashboard/ticket/${response.data.reference_code}/${localStorage.getItem('entity_id')}`,
                            '_blank');
                    } catch (error) {
                        console.error('Error al crear la recepción:', error);
                    } finally {
                        isSubmitting = false;
                    }
                }
            };

            const handleFileChange = async (event) => {
                const url = '/api/dashboard/upload';
                try {
                    const uploadedData = await HTTPService.upload(url, event);
                    filePath = uploadedData;
                    document.querySelector(elements.filePath).value = uploadedData;
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
