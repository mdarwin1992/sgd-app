@extends('layouts.app')

@section('title', 'Actualizar Oficina')

@section('content')
    <div class="dashboard">
        <div data-permissions="api.office.store">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <h4 class="header-title"> Actualizar Ofcina</h4>
                                    <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate" title="Campaign Sent">
                                        Transferir Correspondencia Recibidas A Las Diferentes Dependencias De La Entidad
                                    </h5>
                                    <div id="error-container" class="alert alert-danger" style="display: none;"></div>
                                    <form id="updateOfficeForm" class="needs-validation" novalidate>
                                        <input type="hidden" id="officeId" name="id">
                                        <div class="row">
                                            <div class="col-md-2 mb-3">
                                                <label for="code" class="form-label">Código</label>
                                                <input type="text" class="form-control" id="code" name="code" required>
                                                <div class="invalid-feedback">
                                                    Por favor, ingrese un código válido.
                                                </div>
                                            </div>
                                            <div class="col-md-5 mb-3">
                                                <label for="name" class="form-label">Nombre</label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                                <div class="invalid-feedback">
                                                    Por favor, ingrese un nombre válido.
                                                </div>
                                            </div>
                                            <div class="col-md-5 mb-3">
                                                <label for="department" class="form-label">Departamento</label>
                                                <select class="form-control" id="department" name="department_id"
                                                        required>
                                                    <option value="">Seleccione un departamento</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Por favor, seleccione un departamento.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="user" class="form-label">Responsable</label>
                                                <select class="form-control" id="user" name="user_id" required>
                                                    <option value="">Seleccione un responsable</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Por favor, seleccione un responsable.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard/oficinas"
                                               class="btn btn-primary  rounded-pill btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success rounded-pill "
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
@endsection
@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const OfficeUpdateComponent = HTTPService.createComponent({
            data: () => ({
                isSubmitting: false,
                departments: [],
                users: [],
                officeId: null,
            }),

            methods: {
                async loadOfficeData() {
                    const id = Helpers.getAllGetParams(3);
                    try {
                        const response = await HTTPService.get(`/api/dashboard/office/show/${id}`);
                        this.populateForm(response.data);
                    } catch (error) {
                        console.error('Error al cargar los datos de la oficina:', error);
                    }
                },

                async loadDepartments() {
                    try {
                        const response = await HTTPService.get('/api/dashboard/departments');
                        this.setData({departments: response.data});
                        this.populateDepartmentSelect();
                    } catch (error) {
                        console.error('Error al cargar los departamentos:', error);
                    }
                },

                async loadUsers() {
                    try {
                        const response = await HTTPService.get('/api/dashboard/users');
                        this.setData({users: response.data});
                        this.populateUserSelect();
                        return Promise.resolve();  // Devolver promesa cuando finalice la carga
                    } catch (error) {
                        console.error('Error al cargar los usuarios:', error);
                        return Promise.reject();  // Devolver promesa si hay error
                    }
                },

                populateForm(officeData) {
                    // Primero espera a que los departamentos y usuarios estén cargados antes de asignarles los valores.
                    this.loadDepartments().then(() => {
                        this.elements.department.value = officeData.department_id;
                    });

                    this.loadUsers().then(() => {
                        this.elements.user.value = officeData.user_id;
                    });

                    // Asignar los demás valores
                    this.elements.officeId.value = officeData.id;
                    this.elements.code.value = officeData.code;
                    this.elements.name.value = officeData.name;
                    this.elements.status.value = officeData.status;
                },

                populateDepartmentSelect() {
                    const departmentSelect = this.elements.department;
                    departmentSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
                    this.data.departments.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.id;
                        option.textContent = department.name;
                        departmentSelect.appendChild(option);
                    });
                },

                populateUserSelect() {
                    const userSelect = this.elements.user;
                    userSelect.innerHTML = '<option value="">Seleccione un responsable</option>';
                    this.data.users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name;
                        userSelect.appendChild(option);
                    });
                },

                async updateOffice(event) {
                    event.preventDefault();

                    if (this.data.isSubmitting) {
                        return;
                    }

                    if ($(this.elements.updateOfficeForm).valid()) {
                        this.setData({isSubmitting: true});

                        const form = this.elements.updateOfficeForm;
                        const formData = new FormData(form);
                        const formDataObject = Object.fromEntries(formData.entries());
                        const id = Helpers.getAllGetParams(3);
                        try {
                            const response = await HTTPService.patch(`/api/dashboard/office/update/${id}`, formDataObject);
                            Helpers.getMessage('Oficina actualizada exitosamente', '/dashboard/oficinas');
                        } catch (error) {
                            console.error('Error al actualizar la oficina:', error);
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
                            element.closest('.mb-3').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass(errorClass).removeClass(validClass);
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass(errorClass).addClass(validClass);
                        }
                    });

                    $(this.elements.updateOfficeForm).validate({
                        rules: {
                            code: "required",
                            name: "required",
                            department_id: "required",
                            user_id: "required",
                            status: "required"
                        },
                        messages: {
                            code: "Por favor, ingrese un código para la oficina",
                            name: "Por favor, ingrese un nombre para la oficina",
                            department_id: "Por favor, seleccione un departamento",
                            user_id: "Por favor, seleccione un responsable",
                            status: "Por favor, seleccione un estado"
                        },
                        submitHandler: (form, event) => {
                            event.preventDefault();
                            this.updateOffice(event);
                        }
                    });
                }
            },

            elements: {
                updateOfficeForm: '#updateOfficeForm',
                submitButton: '#submitButton',
                officeId: '#officeId',
                code: '#code',
                name: '#name',
                department: '#department',
                user: '#user',
                status: '#status',
            },

            created() {
                this.loadOfficeData();
                this.loadDepartments()
                    .then(() => this.loadUsers())
                    .then(() => this.loadOfficeData());
            },

            render() {
                this.setupValidation();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            OfficeUpdateComponent.init();
        });

    </script>
@endsection
