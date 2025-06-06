@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="dashboard">
        <div data-permissions="users.create">
            <div class="row pt-3">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-sm-12 col-xl-12 mb-3">
                            <div class="card mb-0 h-100">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-5">
                                            <h4 class="header-title">Listado de Usuarios</h4>
                                            <h5 class="text-muted fw-normal mt-0 mb-3 text-truncate">
                                                Lista todos los usuarios registrados con sus roles y permisos
                                            </h5>
                                        </div>
                                    </div>
                                    <form id="userCreateForm" class="needs-validation" novalidate>
                                        <!-- Información Personal -->
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h6 class="mb-0">Información Personal</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="name" class="form-label">Nombre Completo</label>
                                                        <input type="text" class="form-control" id="name"
                                                               name="name" required
                                                               title="El nombre debe contener entre 3 y 50 caracteres, solo letras y espacios">
                                                        <div class="invalid-feedback">
                                                            Por favor ingrese un nombre válido
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label for="email" class="form-label">Correo
                                                            Electrónico</label>
                                                        <input type="email" class="form-control" id="email"
                                                               name="email" required>
                                                        <div class="invalid-feedback">
                                                            Por favor ingrese un correo electrónico válido
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="phone" class="form-label">Teléfono</label>
                                                        <input type="tel" class="form-control" id="phone"
                                                               name="phone"
                                                               title="Ingrese un número de teléfono válido de 10 dígitos">
                                                        <div class="invalid-feedback">
                                                            Por favor ingrese un número de teléfono válido
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Credenciales -->
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h6 class="mb-0">Credenciales</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="password" class="form-label">Contraseña</label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control"
                                                                   id="password" name="password" required>
                                                            <button class="btn btn-outline-secondary"
                                                                    type="button"
                                                                    id="togglePassword">
                                                                <i class="mdi mdi-eye"></i>
                                                            </button>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            La contraseña debe tener al menos 8 caracteres
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="password_confirmation" class="form-label">Confirmar
                                                            contraseña</label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control"
                                                                   id="password_confirmation"
                                                                   name="password_confirmation" required>
                                                            <button class="btn btn-outline-secondary"
                                                                    type="button"
                                                                    id="togglePasswordConfirmation">
                                                                <i class="mdi mdi-eye"></i>
                                                            </button>
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            Las contraseñas deben coincidir
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Roles y Permisos -->
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Roles y Permisos</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="roles" class="form-label">Roles *</label>
                                                        <select class="js-example-basic-multiple"
                                                                id="roles"
                                                                name="roles"
                                                                required>
                                                            <option value="">Seleccione un rol</option>
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Por favor seleccione al menos un rol
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" id="permissionsContainer"
                                                         style="display: none;">
                                                        <label for="permissions" class="form-label">Permisos
                                                            Adicionales</label>
                                                        <select class="js-example-basic-multiple" id="permissions"
                                                                name="permissions[]" multiple="multiple">
                                                            <option value="">Seleccione</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="/dashboard/usuarios"
                                               class="btn btn-primary btn-tool rounded-pill me-2">
                                                <i class="fas fa-times me-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success rounded-pill"
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
@endsection

@section('scripts')
    <script type="module">
        // userList.js
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const UserUpdate = (() => {
            // Private variables
            let isSubmitting = false;

            const elements = {
                userCreateForm: '#userCreateForm',
                submitButton: '#submitButton',
                roles: '#roles',
                permissions: '#permissions',
                permissionsContainer: '#permissionsContainer'
            };

            const fetchRolesAndPermissions = async () => {
                try {
                    const [rolesResponse, permissionsResponse] = await Promise.all([
                        HTTPService.get('/api/dashboard/roles'),
                        HTTPService.get('/api/dashboard/permissions')
                    ]);
                    populateSelectRoles(elements.roles, rolesResponse.data || []);
                    populateSelect(elements.permissions, permissionsResponse.data || []);
                } catch (error) {
                    console.error('Error al cargar roles y permisos:', error);
                }
            }

            const populateSelectRoles = (selector, data) => {
                const select = $(selector);
                select.empty();
                select.append(new Option('Seleccione un rol', '', true, true));

                data.forEach(item => {
                    const option = new Option(item.name, item.id, false, false);
                    select.append(option);
                });

                select.trigger('change');
            }

            const populateSelect = (selector, data) => {
                const select = $(selector);
                select.empty();

                data.forEach(item => {
                    const option = new Option(item.name, item.id, false, false);
                    select.append(option);
                });

                select.trigger('change');
            }

            const handleRoleChange = async (event) => {
                const roleId = event.target.value;
                if (roleId) {
                    try {
                        const response = await HTTPService.get(`/api/dashboard/roles/${roleId}/has-permissions`);
                        $(elements.permissionsContainer).toggle(!response);
                    } catch (error) {
                        console.error('Error al verificar permisos del rol:', error);
                    }
                } else {
                    $(elements.permissionsContainer).hide();
                }
            };

            const loadUserData = async () => {
                let id = Helpers.getAllGetParams(3);
                if (!id) return; // Si no hay ID, estamos creando un nuevo usuario

                try {
                    const response = await HTTPService.get(`api/dashboard/users/${id}`);
                    const user = response.data;
                    console.log(response.data.roles[0])
                    document.querySelector('#name').value = user.name;
                    document.querySelector('#email').value = user.email;
                    document.querySelector('#phone').value = user.phone;
                    document.querySelector(elements.roles).value = user.roles[0]?.id;
                    $(elements.roles).trigger('change');

                    if (user.permissions && user.permissions.length > 0) {
                        $(elements.permissions).val(user.permissions.map(p => p.id));
                        $(elements.permissions).trigger('change');
                    }
                } catch (error) {
                    console.error('Error al cargar los datos del usuario:', error);
                }
            };

            const handleSubmit = async (event) => {
                event.preventDefault();

                if (isSubmitting) {
                    return;
                }

                if ($(elements.userCreateForm).valid()) {
                    isSubmitting = true;

                    const form = document.querySelector(elements.userCreateForm);
                    const formData = new FormData(form);

                    const formDataObject = {
                        name: formData.get('name'),
                        email: formData.get('email'),
                        password: formData.get('password'),
                        password_confirmation: formData.get('password_confirmation'),
                        phone: formData.get('phone'),
                        roles: formData.get('roles'),
                        permissions: Array.from(formData.getAll('permissions[]'))
                    };

                    try {
                        const response = await HTTPService.post('/api/dashboard/users', formDataObject);
                        Helpers.getMessage('Usuario creado exitosamente', '/dashboard/usuarios');
                    } catch (error) {
                        console.error('Error al crear el usuario:', error);
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

                $(elements.userCreateForm).validate({
                    rules: {
                        name: "required",
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true,
                            minlength: 8
                        },
                        password_confirmation: {
                            required: true,
                            equalTo: "#password"
                        },
                        phone: {
                            required: true,
                            digits: true,
                            minlength: 10,
                            maxlength: 10
                        },
                        roles: "required"
                    },
                    messages: {
                        name: "Por favor, ingrese un nombre completo",
                        email: {
                            required: "Por favor, ingrese un correo electrónico",
                            email: "Por favor, ingrese un correo electrónico válido"
                        },
                        password: {
                            required: "Por favor, ingrese una contraseña",
                            minlength: "La contraseña debe tener al menos 8 caracteres"
                        },
                        password_confirmation: {
                            required: "Por favor, confirme la contraseña",
                            equalTo: "Las contraseñas no coinciden"
                        },
                        phone: {
                            required: "Por favor, ingrese un número de teléfono",
                            digits: "Por favor, ingrese solo dígitos",
                            minlength: "El número de teléfono debe tener 10 dígitos",
                            maxlength: "El número de teléfono debe tener 10 dígitos"
                        },
                        roles: "Por favor, seleccione al menos un rol"
                    }
                });
            };

            // Initialize when DOM is ready
            $(document).ready(() => {
                fetchRolesAndPermissions();
                loadUserData();
                // Set up event listeners
                document.querySelector(elements.userCreateForm).addEventListener('submit', handleSubmit);
                $(elements.roles).on('change', handleRoleChange);

                // Toggle password visibility
                $('#togglePassword').on('click', function () {
                    const passwordInput = $('#password');
                    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                    passwordInput.attr('type', type);
                    $(this).find('i').toggleClass('mdi-eye mdi-eye-off');
                });

                $('#togglePasswordConfirmation').on('click', function () {
                    const passwordInput = $('#password_confirmation');
                    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                    passwordInput.attr('type', type);
                    $(this).find('i').toggleClass('mdi-eye mdi-eye-off');
                });
            });


            // Create public API
            const publicApi = {
                loadUserData,
                fetchRolesAndPermissions,
                populateSelectRoles,
                populateSelect,
                setupValidation,
                handleRoleChange
            };

            // Expose to window object for inline event handlers
            window.UserUpdate = publicApi;

            // Return public API for module imports
            return publicApi;
        })();


    </script>
@endsection
