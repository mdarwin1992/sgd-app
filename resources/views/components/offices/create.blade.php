@extends('layouts.app')

@section('title', 'Transferir Correspondencia')

@section('content')
    <div class="dashboard">
        <div data-permissions="api.office.store">
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
                                    <form id="createOfficeForm" class="needs-validation" novalidate>
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
                                            <a href="/dashboard/business" class="btn btn-primary btn-tool me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success" id="submitButton">
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

        const OfficeCreateComponent = HTTPService.createComponent({
            data: () => ({
                isSubmitting: false,
                departments: [],
                users: [],
            }),

            methods: {
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
                        const response = await HTTPService.get('/api/dashboard/users/list');
                        this.setData({users: response.data});
                        this.populateUserSelect();
                    } catch (error) {
                        console.error('Error al cargar los usuarios:', error);
                    }
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

                async createOffice(event) {
                    event.preventDefault();

                    if (this.data.isSubmitting) {
                        return;
                    }

                    if ($(this.elements.createOfficeForm).valid()) {
                        this.setData({isSubmitting: true});

                        const form = this.elements.createOfficeForm;
                        const formData = new FormData(form);
                        const formDataObject = Object.fromEntries(formData.entries());

                        try {
                            const response = await HTTPService.post('/api/dashboard/office/store', formDataObject);
                            Helpers.getMessage('Oficina creada exitosamente', '/dashboard/oficinas');
                        } catch (error) {
                            console.error('Error al crear la oficina:', error);
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

                    $(this.elements.createOfficeForm).validate({
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
                            this.createOffice(event);
                        }
                    });
                }
            },

            elements: {
                createOfficeForm: '#createOfficeForm',
                submitButton: '#submitButton',
                code: '#code',
                name: '#name',
                department: '#department',
                user: '#user',
                status: '#status',
            },

            created() {
                this.loadDepartments();
                this.loadUsers();
            },

            render() {
                this.setupValidation();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            OfficeCreateComponent.init();
        });
    </script>
@endsection
