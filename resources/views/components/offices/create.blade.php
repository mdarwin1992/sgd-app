@extends('layouts.app')

@section('title', 'Crear oficinas')

@section('content')
    <div class="dashboard">
        <div data-permissions="offices.create">
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
                                                <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
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
                                               class="btn btn-primary rounded-pill btn-tool me-2">
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
@endsection
@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const OfficeCreateComponent = (() => {
            // Private variables
            let isSubmitting = false;
            let departments = [];
            let users = [];

            const elements = {
                createOfficeForm: '#createOfficeForm',
                submitButton: '#submitButton',
                code: '#code',
                name: '#name',
                department: '#department',
                user: '#user',
                status: '#status',
            };

            // Private methods
            const loadDepartments = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/departments');
                    departments = response.data;
                    populateDepartmentSelect();
                } catch (error) {
                    console.error('Error al cargar los departamentos:', error);
                }
            };

            const loadUsers = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/users/list');
                    users = response.data;
                    populateUserSelect();
                } catch (error) {
                    console.error('Error al cargar los usuarios:', error);
                }
            };

            const populateDepartmentSelect = () => {
                const departmentSelect = document.querySelector(elements.department);
                departmentSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
                departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = department.name;
                    departmentSelect.appendChild(option);
                });
            };

            const populateUserSelect = () => {
                const userSelect = document.querySelector(elements.user);
                userSelect.innerHTML = '<option value="">Seleccione un responsable</option>';
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    userSelect.appendChild(option);
                });
            };

            const createOffice = async (event) => {
                event.preventDefault();

                if (isSubmitting) {
                    return;
                }

                if ($(elements.createOfficeForm).valid()) {
                    isSubmitting = true;

                    const form = document.querySelector(elements.createOfficeForm);
                    const formData = new FormData(form);
                    const formDataObject = Object.fromEntries(formData);

                    try {
                        const response = await HTTPService.post('/api/dashboard/office/store', formDataObject);
                        Helpers.getMessage('Oficina creada exitosamente', '/dashboard/oficinas');
                    } catch (error) {
                        console.error('Error al crear la oficina:', error);
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

                $(elements.createOfficeForm).validate({
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
                        createOffice(event);
                    }
                });
            };

            // Initialize when DOM is ready
            $(document).ready(() => {
                loadDepartments();
                loadUsers();
                setupValidation();

                // Set up event listeners
                document.querySelector(elements.createOfficeForm).addEventListener('submit', createOffice);
            });

            // Create public API
            const publicApi = {
                loadDepartments,
                loadUsers,
                createOffice,
                setupValidation
            };

            // Expose to window object for inline event handlers
            window.OfficeManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();

        // Export the component
        export default OfficeCreateComponent;
    </script>
@endsection
