@extends('layouts.app')

@section('title', 'Transferir correspondencia recibida')

@section('content')
    <div class="dashboard">
        <div data-permissions="transfer.create">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title"> Transferir Correspondencia</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Transferir Correspondencia Recibidas A Las Diferentes Dependencias De La Entidad
                                    </h5>
                                    <div id="error-container" class="alert alert-danger" style="display: none;"></div>
                                    <form id="AddForm" name="AddForm">
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-lg-2 form-group">
                                                <label for="reference_code">Nº Radicado</label>
                                                <input type="text" class="form-control" id="reference_code"
                                                    name="reference_code" readonly>
                                            </div>
                                            <div class="col-md-6 col-lg-5 form-group">
                                                <label for="subject">Asunto</label>
                                                <input type="text" class="form-control" id="subject" name="subject"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 col-lg-5 form-group">
                                                <label for="sender_name">Nombre del Remitente</label>
                                                <input type="text" class="form-control" id="sender_name"
                                                    name="sender_name" readonly>
                                                <input type="hidden" class="form-control" id="document_id"
                                                    name="document_id" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-lg-4 form-group">
                                                <label for="office_id">Oficina Destino:</label>
                                                <select class="form-control" id="office_id" name="office_id" required>
                                                    <option value="">Seleccione una oficina</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-lg-2 form-group">
                                                <label for="response_time">Tiempo de Respuesta:</label>
                                                <input type="text" class="form-control" id="response_time"
                                                    name="response_time" placeholder="Ej: 5d" required autocomplete="off">
                                            </div>
                                            <div class="col-md-6 col-lg-3 form-group">
                                                <label for="response_deadline">Fecha Límite de Respuesta:</label>
                                                <input type="date" class="form-control" id="response_deadline"
                                                    name="response_deadline" required>
                                            </div>
                                            <div class="col-md-6 col-lg-3 form-group">
                                                <label for="transfer_datetime">Fecha y Hora de Transferencia:</label>
                                                <input type="datetime-local" class="form-control" id="transfer_datetime"
                                                    name="transfer_datetime" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-lg-4 form-group">
                                                <label for="name">Encargado de la oficina</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 col-lg-5 form-group">
                                                <label for="email">Correo electrónico</label>
                                                <input type="text" class="form-control" id="email" name="email"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6 col-lg-3 form-group">
                                                <label for="phone">Teléfono</label>
                                                <input type="text" class="form-control" id="phone" name="phone"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-lg-12 form-group">
                                                <label for="job_type">Tipo de Trabajo:</label>
                                                <textarea class="form-control" id="job_type" name="job_type" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="alert alert-primary" role="alert" id="urlPath">


                                        </div>
                                        <input type="hidden" class="form-control" id="path" name="path" readonly>
                                        <input type="hidden" class="form-control" id="file_path" name="file_path"
                                            readonly>
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard/business"
                                                class="btn btn-primary btm-sm rounded-pill btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btm-sm btn-success rounded-pill"
                                                id="submitButton">
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
    <div class="modal fade" id="bs-example-modal-lg-pdfViewer" tabindex="-1" role="dialog"
        aria-labelledby="pdfViewerModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfViewerModalTitle">Visor de PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body" style="height: 80vh;"> <!-- Altura fija para mejor visualización -->
                    <iframe id="pdfViewer" style="width: 100%; height: 100%; border: none;" allowfullscreen>
                    </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const TransferForm = (() => {
            // Estado privado del módulo

            let formattedDateTime = '';
            let office = [];
            let error = null;
            let selectedOffice = null;
            let dueDate = '';
            let isSubmitting = false;
            let daysInputListenerAdded = false;
            let officeSelectListenerAdded = false


            // Referencias a elementos del DOM
            const elements = {
                AddForm: '#AddForm',
                transferDatetime: '#transfer_datetime',
                officeSelect: '#office_id',
                daysInput: '#response_time',
                responsedeadline: '#response_deadline',
                name: '#name',
                email: '#email',
                phone: '#phone',
                referenceCode: '#reference_code',
                senderName: '#sender_name',
                subject: '#subject',
                filePath: '#file_path',
                UrlPath: '#urlPath',
                documentId: '#document_id',
                Codepath: '#path',
                errorContainer: '#error-container'
            };

            // Funciones privadas
            const updateDateTime = () => {
                const now = new Date();
                formattedDateTime = now.toISOString().slice(0, 16);
                document.querySelector(elements.transferDatetime).value = formattedDateTime;
            };

            const calculateDueDate = () => {
                const days = parseInt(document.querySelector(elements.daysInput).value);
                if (isNaN(days)) {
                    dueDate = '';
                    document.querySelector(elements.responsedeadline).value = '';
                    return;
                }
                const currentDate = new Date();
                currentDate.setDate(currentDate.getDate() + days);

                dueDate = currentDate.toISOString().split('T')[0];
                document.querySelector(elements.responsedeadline).value = dueDate;
            };

            const handleManagerChange = async (event) => {
                const officeId = event.target.value;
                selectedOffice = officeId;
                if (officeId) {
                    await fetchOfficeUser(officeId);
                } else {
                    console.warn('Error: no se seleccionó una oficina válida');
                }
            };

            const fetchOfficeUser = async (officeId) => {
                try {
                    const response = await HTTPService.get(`/api/dashboard/office/manager/${officeId}`);
                    if (response && response.data) {
                        document.querySelector(elements.name).value = response.data.user.name || '';
                        document.querySelector(elements.email).value = response.data.user.email || '';
                        document.querySelector(elements.phone).value = response.data.user.phone || '';
                    }
                } catch (error) {
                    console.error('Error al obtener datos del usuario de la oficina', error);
                    showError('Error al obtener datos del usuario de la oficina');
                }
            };

            const fetchOfficeId = async () => {
                try {
                    let id = Helpers.getAllGetParams(3);
                    const response = await HTTPService.get(`/api/dashboard/reception/show/${id}`);
                    if (response && response.data) {
                        // Document data
                        document.querySelector(elements.referenceCode).value = response.data
                            .reference_code || '';
                        document.querySelector(elements.senderName).value = response.data.sender_name || '';
                        document.querySelector(elements.subject).value = response.data.subject || '';
                        document.querySelector(elements.documentId).value = response.data.id || '';

                        // File path handling
                        const Path = response.data.file_path;
                        if (Path) {
                            const fileName = Path.split('/').pop();
                            const fileNumbers = fileName.match(/\d+/)[0];

                            // Create link with click event handler
                            document.querySelector(elements.UrlPath).innerHTML =
                                `<strong>Ruta documento - </strong> <a href="#" onclick="TransferForm.handlePDFClick('${fileNumbers}', '${fileName}'); return false;">${fileName}</a> (${fileNumbers})`;
                            document.querySelector(elements.filePath).value = fileName;
                            document.querySelector(elements.Codepath).value = fileNumbers;
                        } else {
                            document.querySelector(elements.UrlPath).innerHTML =
                                `<strong>Ruta documento - </strong> Documento no encontrado`;
                        }
                    }
                } catch (error) {
                    console.error('Error al obtener datos del documento', error);
                    showError('Error al obtener datos del documento');
                }
            };

            const fetchOffice = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/offices');
                    if (response && Array.isArray(response.data) && response.data.length) {
                        office = response.data;
                        error = null;
                    } else {
                        office = [];
                        error = 'No hay oficinas disponibles';
                    }
                } catch (err) {
                    console.error('No se pudieron cargar las oficinas', err);
                    office = [];
                    error = 'No se pudieron cargar las oficinas';
                } finally {
                    renderOffice();
                }
            };

            const renderOffice = () => {
                const officeSelect = document.querySelector(elements.officeSelect);
                const currentOptions = Array.from(officeSelect.options).map(option => option.value);

                if (Array.isArray(office) && office.length > 0) {
                    office.forEach(office => {
                        if (!currentOptions.includes(office.id.toString())) {
                            const option = document.createElement('option');
                            option.value = office.id;
                            option.textContent = office.name;
                            officeSelect.appendChild(option);
                        }
                    });
                }
            };

            const createTransfer = async (event) => {
                event.preventDefault();

                if (isSubmitting) {
                    return true;
                }

                if ($(elements.AddForm).valid()) {
                    isSubmitting = true;

                    const form = document.querySelector(elements.AddForm);
                    const formData = new FormData(form);
                    const formDataObject = Object.fromEntries(formData.entries());

                    try {
                        await HTTPService.post('/api/dashboard/correspondence-transfer/store',
                            formDataObject);
                        Helpers.getMessage('Transferencia exitosa',
                            '/dashboard/ventanilla-unica/transferir-correspondencias-recibidas');
                    } catch (error) {
                        console.error('Error al crear la transferencia', error);
                        showError('Error al crear la transferencia');
                    } finally {
                        isSubmitting = false;
                    }
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
                        office_id: "required",
                        response_time: "required",
                        response_deadline: {
                            required: true,
                            date: true
                        },
                        transfer_datetime: "required",
                        job_type: "required",
                    },
                    messages: {
                        office_id: "Por favor, ingrese el ID de la oficina",
                        response_time: "Por favor, ingrese el tiempo de respuesta",
                        response_deadline: {
                            required: "Por favor, ingrese la fecha límite de respuesta",
                            date: "Por favor, ingrese una fecha válida"
                        },
                        transfer_datetime: "Por favor, ingrese la fecha y hora de la transferencia",
                        job_type: "Por favor, ingrese el tipo de trabajo requerido"
                    },
                    submitHandler: (form, event) => {
                        event.preventDefault();
                        createTransfer(event);
                    }
                });
            };

            const showError = (message) => {
                const errorContainer = document.querySelector(elements.errorContainer);
                errorContainer.textContent = message;
                errorContainer.style.display = 'block';

                setTimeout(() => {
                    errorContainer.style.display = 'none';
                }, 5000);

                errorContainer.scrollIntoView({
                    behavior: 'smooth'
                });
            };

            const downloadPDF = async (fileNumbers, fileName) => {
                if (!fileNumbers) {
                    await Swal.fire('Error', 'ID de documento no válido', 'error');
                    return;
                }

                try {
                    $('#bs-example-modal-lg').modal('hide');
                    const pdfViewer = document.getElementById('pdfViewer');
                    pdfViewer.src = `/storage/upload/${fileNumbers}/${fileName}`;
                    $('#bs-example-modal-lg-pdfViewer').modal('show');
                } catch (error) {
                    console.error('Error al descargar el PDF:', error);
                    await Swal.fire('Error', 'Error al descargar el PDF', 'error');
                }
            };

            const handlePDFClick = (fileNumbers, fileName) => {
                Swal.showLoading();
                downloadPDF(fileNumbers, fileName);
                Swal.close();
            };

            // Inicialización
            $(document).ready(() => {
                updateDateTime();
                fetchOffice();
                fetchOfficeId();

                if (!daysInputListenerAdded) {
                    document.querySelector(elements.daysInput).addEventListener('input', calculateDueDate);
                    daysInputListenerAdded = true;
                }

                if (!officeSelectListenerAdded) {
                    document.querySelector(elements.officeSelect).addEventListener('change',
                        handleManagerChange);
                    officeSelectListenerAdded = true;
                    setupValidation();
                }
            });

            // API pública
            const publicApi = {
                updateDateTime,
                calculateDueDate,
                handleManagerChange,
                createTransfer,
                showError,
                handlePDFClick
            };

            // Exponer al objeto window para manejadores de eventos en línea
            window.TransferForm = publicApi;

            // Retornar API pública para importaciones de módulos
            return publicApi;
        })();
    </script>
@endsection
