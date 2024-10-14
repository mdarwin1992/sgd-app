@extends('layouts.app')

@section('title', 'Recepción')

@section('content')
    <div class="dashboard">
        <div data-permissions="api.document.sendings.store" class="visible">
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
                                                       name="reference_code" value="">
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
                                                <input type="number" class="form-control" id="page_count"
                                                       name="page_count" value="">
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
                                                <select class="form-control" id="office_id"
                                                        name="office_id" required>
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

        const SendComponent = HTTPService.createComponent({
            data: () => ({
                isSubmitting: false,
                counter: null,
                filePath: '',
                departments: [],
                selectedDepartment: null,
                lastFetchedDepartment: null,
                office: [],
                selectedOffice: null,
            }),

            methods: {
                async loadCounter() {
                    const {referenceCode} = this.elements;
                    const entityId = localStorage.getItem('entity_id');

                    if (!entityId || !this.data.selectedOffice) {
                        console.error('No se encontró el entity_id en localStorage o no se ha seleccionado una oficina.');
                        return;
                    }

                    try {
                        const counterResponse = await HTTPService.get(`/api/entity/${entityId}/counter`);
                        this.setData({counter: counterResponse});
                        const formattedReferenceCode = this.data.selectedOffice.toString().padStart(3, '0') + (counterResponse.reference_code || '').padStart(7, '0');
                        referenceCode.value = formattedReferenceCode;
                    } catch (error) {
                        console.error('Error al cargar el contador:', error);
                    }
                },

                async getDepartments() {
                    try {
                        const response = await HTTPService.get('/api/dashboard/departments');
                        if (response && Array.isArray(response.data) && response.data.length > 0) {
                            this.setData({departments: response.data, error: null});
                            console.log(response);
                        } else {
                            this.setData({departments: [], error: 'No hay departamentos disponibles.'});
                        }
                    } catch (err) {
                        console.error('Error al obtener los departamentos:', err);
                        this.setData({departments: [], error: 'No se pudieron cargar los departamentos.'});
                    } finally {
                        this.renderDepartments();
                    }
                },

                async fetchOffice(departmentId) {
                    if (this.data.lastFetchedDepartment === departmentId) {
                        return;
                    }
                    this.setData({lastFetchedDepartment: departmentId});

                    try {
                        const response = await HTTPService.get(`/api/dashboard/departments/${departmentId}/offices`);

                        if (response && Array.isArray(response.data)) {
                            this.setData({office: response.data, error: null});
                        } else {
                            this.setData({
                                office: [],
                                error: 'No hay oficinas disponibles para este departamento.'
                            });
                        }
                    } catch (err) {
                        console.error('Error al obtener las oficinas:', err);
                        this.setData({office: [], error: 'No se pudieron cargar las oficinas.'});
                    } finally {
                        this.renderOffice();
                    }
                },

                async handleFileChange(event) {
                    const url = '/api/dashboard/upload';
                    try {
                        const uploadedData = await HTTPService.upload(url, event);
                        this.setData({filePath: uploadedData});
                        this.elements.filePath.value = uploadedData;
                    } catch (error) {
                        console.error('Error al cargar el archivo:', error.message);
                    }
                },

                async createSend(event) {
                    event.preventDefault();

                    if (this.data.isSubmitting) {
                        return;
                    }

                    if ($(this.elements.AddForm).valid()) {
                        this.setData({isSubmitting: true});

                        const form = document.querySelector('#AddForm');
                        const formData = new FormData(form);
                        const formDataObject = {};

                        formData.forEach((value, key) => {
                            formDataObject[key] = value;
                        });

                        try {
                            const response = await HTTPService.post('api/dashboard/document-sendings/store', formDataObject);
                            Helpers.getMessage('Se ha guardado correctamente', '/dashboard/recepcion')
                        } catch (error) {
                            console.error('Error al crear la recepción:', error);
                        } finally {
                            this.setData({isSubmitting: false});
                        }
                    }
                },

                renderDepartments() {
                    const {departmentSelect} = this.elements;
                    departmentSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
                    if (Array.isArray(this.data.departments) && this.data.departments.length > 0) {
                        departmentSelect.innerHTML += this.data.departments
                            .map(department => `<option value="${department.id}" ${this.data.selectedDepartment == department.id ? 'selected' : ''}>${department.name}</option>`)
                            .join('');
                    } else {
                        departmentSelect.innerHTML = '<option>No hay departamentos disponibles</option>';
                    }
                },

                renderOffice() {
                    const {officeSelect} = this.elements;
                    officeSelect.innerHTML = '<option value="">Seleccione una oficina</option>';
                    if (Array.isArray(this.data.office) && this.data.office.length > 0) {
                        officeSelect.innerHTML += this.data.office
                            .map(branches => `<option value="${branches.id}">${branches.name}</option>`)
                            .join('');
                    } else {
                        officeSelect.innerHTML = '<option>No hay oficinas disponibles</option>';
                    }
                },

                handleDepartmentChange(event) {
                    const departmentId = event.target.value;
                    this.setData({selectedDepartment: departmentId});
                    if (departmentId) {
                        this.fetchOffice(departmentId);
                    } else {
                        this.setData({office: [], lastFetchedDepartment: null, selectedOffice: null});
                        this.renderOffice();
                        this.elements.referenceCode.value = '';
                    }
                },

                handleOfficeChange(event) {
                    const officeId = event.target.value;
                    this.setData({selectedOffice: officeId});
                    if (officeId) {
                        this.loadCounter();
                    } else {
                        this.elements.referenceCode.value = '';
                    }
                },

                setupValidation() {
                    $.validator.setDefaults({
                        errorClass: 'is-invalid',
                        validClass: 'is-valid',
                        errorElement: 'div',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            error.insertAfter(element);
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
                            this.createSend(event);
                        }
                    });
                }
            },

            elements: {
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
            },

            created() {
                this.getDepartments();
            },

            render() {
                this.elements.departmentSelect.addEventListener('change', this.handleDepartmentChange.bind(this));
                this.elements.officeSelect.addEventListener('change', this.handleOfficeChange.bind(this));
                this.elements.fileInput.addEventListener('change', this.handleFileChange.bind(this));
                this.setupValidation();
            }
        });
        // Inicializar el componente cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            SendComponent.init();
        });
    </script>
@endsection

