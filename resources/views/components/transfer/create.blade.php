@extends('layouts.app')

@section('title', 'Transferir Correspondencia')

@section('content')
    <div class="dashboard">
        <div data-permissions="api.correspondence.transfer.store">
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
                                                <input type="text" class="form-control" id="subject"
                                                       name="subject" readonly>
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
                                                       name="response_time" placeholder="Ej: 5d" required>
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
                                                <input type="text" class="form-control" id="name"
                                                       name="name" readonly>
                                            </div>
                                            <div class="col-md-6 col-lg-5 form-group">
                                                <label for="email">Correo electrónico</label>
                                                <input type="text" class="form-control" id="email"
                                                       name="email" readonly>
                                            </div>
                                            <div class="col-md-6 col-lg-3 form-group">
                                                <label for="phone">Teléfono</label>
                                                <input type="text" class="form-control" id="phone"
                                                       name="phone" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-lg-12 form-group">
                                                <label for="job_type">Tipo de Trabajo:</label>
                                                <textarea class="form-control" id="job_type" name="job_type" rows="3"
                                                          required></textarea>
                                            </div>
                                        </div>
                                        <div class="alert alert-primary" role="alert" id="urlPath">


                                        </div>
                                        <input type="hidden" class="form-control" id="path" name="path" readonly>
                                        <input type="hidden" class="form-control" id="file_path" name="file_path"
                                               readonly>
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard/business" class="btn btn-primary btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success" id="submitButton">
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

        const TransferComponent = HTTPService.createComponent({
            data: () => ({
                formattedDateTime: '',
                office: [],
                error: null,
                selectedOffice: null,
                dueDate: '',
                isSubmitting: false,
            }),

            methods: {
                updateDateTime() {
                    const now = new Date();
                    this.formattedDateTime = now.toISOString().slice(0, 16);
                    this.elements.transferDatetime.value = this.formattedDateTime;
                },

                calculateDueDate() {
                    const days = parseInt(this.elements.daysInput.value);
                    if (isNaN(days)) {
                        this.dueDate = '';
                        this.elements.responsedeadline.value = '';
                        return;
                    }
                    const currentDate = new Date();
                    currentDate.setDate(currentDate.getDate() + days);

                    this.dueDate = currentDate.toISOString().split('T')[0];
                    this.elements.responsedeadline.value = this.dueDate;
                },

                async handleManagerChange(event) {
                    const officeId = event.target.value;
                    this.setData({selectedOffice: officeId});
                    if (officeId) {
                        await this.fetchOfficeUser(officeId);
                    } else {
                        console.warn('Error: no se seleccionó una oficina válida');
                    }
                },

                async fetchOfficeUser(officeId) {
                    const {name, email, phone} = this.elements;
                    try {
                        const response = await HTTPService.get(`/api/dashboard/office/manager/${officeId}`);
                        if (response && response.data) {
                            name.value = response.data.user.name || '';
                            email.value = response.data.user.email || '';
                            phone.value = response.data.user.phone || '';
                        }
                    } catch (error) {
                        console.error('Error al obtener datos del usuario de la oficina', error);
                        this.showError('Error al obtener datos del usuario de la oficina');
                    }
                },

                async fetchOfficeId() {
                    const {
                        referenceCode, senderName, subject, filePath, UrlPath, documentId, Codepath, status
                    } = this.elements;

                    try {
                        let id = Helpers.getAllGetParams(2);
                        const response = await HTTPService.get(`/api/dashboard/reception/show/${id}`);
                        if (response && response.data) {
                            // Document data
                            referenceCode.value = response.data.reference_code || '';
                            senderName.value = response.data.sender_name || '';
                            subject.value = response.data.subject || '';
                            documentId.value = response.data.id || '';


                            // File path handling
                            const Path = response.data.file_path;
                            if (Path) {
                                const fileName = Path.split('/').pop();
                                const fileNumbers = fileName.match(/\d+/)[0];

                                UrlPath.innerHTML = `<strong>Ruta documento - </strong> <a href="/dashboard/show-file/${fileNumbers}/${fileName}">${fileName}</a> (${fileNumbers})`;
                                filePath.value = fileName;
                                Codepath.value = fileNumbers;
                            } else {
                                UrlPath.innerHTML = `<strong>Ruta documento - </strong> Documento no encontrado`;
                            }
                        }
                    } catch (error) {
                        console.error('Error al obtener datos del documento', error);
                        this.showError('Error al obtener datos del documento');
                    }
                },

                async fetchOffice() {
                    try {
                        const response = await HTTPService.get('/api/dashboard/offices');
                        if (response && Array.isArray(response.data) && response.data.length) {
                            this.setData({office: response.data, error: null});
                        } else {
                            this.setData({office: [], error: 'No hay oficinas disponibles'});
                        }
                    } catch (err) {
                        console.error('No se pudieron cargar las oficinas', err);
                        this.setData({office: [], error: 'No se pudieron cargar las oficinas'});
                    } finally {
                        this.renderOffice();
                    }
                },

                renderOffice() {
                    const {officeSelect} = this.elements;
                    const currentOptions = Array.from(officeSelect.options).map(option => option.value);

                    if (Array.isArray(this.data.office) && this.data.office.length > 0) {
                        this.data.office.forEach(office => {
                            if (!currentOptions.includes(office.id.toString())) {
                                const option = document.createElement('option');
                                option.value = office.id;
                                option.textContent = office.name;
                                officeSelect.appendChild(option);
                            }
                        });
                    }
                },

                async createTransfer(event) {
                    event.preventDefault();

                    if (this.data.isSubmitting) {
                        return true;
                    }

                    if ($(this.elements.AddForm).valid()) {
                        this.setData({isSubmitting: true});

                        const form = document.querySelector('#AddForm');
                        const formData = new FormData(form);
                        const formDataObject = Object.fromEntries(formData.entries());

                        try {
                            const response = await HTTPService.post('/api/dashboard/correspondence-transfer/store', formDataObject);
                            Helpers.getMessage('Transferencia exitosa', '/dashboard/transferir-correspondencias-recibidas')
                        } catch (error) {
                            console.error('Error al crear la transferencia', error);
                            this.showError('Error al crear la transferencia');
                        } finally {
                            this.setData({isSubmitting: false});
                        }
                    }
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

                    $(this.elements.AddForm).validate({
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
                            this.createTransfer(event);
                        }
                    });
                },

                showError(message) {
                    const errorContainer = document.getElementById('error-container');
                    errorContainer.textContent = message;
                    errorContainer.style.display = 'block';

                    setTimeout(() => {
                        errorContainer.style.display = 'none';
                    }, 5000);

                    errorContainer.scrollIntoView({behavior: 'smooth'});
                },
            },

            elements: {
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
                errorContainer: '#error-container',
            },

            created() {
                this.updateDateTime();
                this.fetchOffice();
                this.fetchOfficeId();
                if (!this.daysInputListenerAdded) {
                    this.elements.daysInput.addEventListener('input', this.calculateDueDate.bind(this));
                    this.daysInputListenerAdded = true;
                }
            },

            render() {
                if (!this.officeSelectListenerAdded) {
                    this.elements.officeSelect.addEventListener('change', this.handleManagerChange.bind(this));
                    this.officeSelectListenerAdded = true;
                    this.setupValidation();
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            TransferComponent.init();

        });

    </script>
@endsection
