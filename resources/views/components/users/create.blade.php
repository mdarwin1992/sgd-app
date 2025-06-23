@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="dashboard">
        <div data-permissions="users.update">
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
                                        <div class="modal-body">
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
                                                                name="name" required autocomplete="off"
                                                                title="El nombre debe contener entre 3 y 50 caracteres, solo letras y espacios">
                                                            <div class="invalid-feedback">
                                                                Por favor ingrese un nombre válido
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label for="email" class="form-label">Correo
                                                                Electrónico</label>
                                                            <input type="email" class="form-control" id="email"
                                                                name="email" required autocomplete="off">
                                                            <div class="invalid-feedback">
                                                                Por favor ingrese un correo electrónico válido
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="phone" class="form-label">Teléfono</label>
                                                            <input type="tel" class="form-control" id="phone"
                                                                autocomplete="off" name="phone"
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
                                                                <input type="password" class="form-control" id="password"
                                                                    name="password" required>
                                                                <button class="btn btn-outline-secondary" type="button"
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
                                                                    id="password_confirmation" name="password_confirmation"
                                                                    required>
                                                                <button class="btn btn-outline-secondary" type="button"
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
                                                        <div class="col-md-4">
                                                            <label for="roles" class="form-label">Roles *</label>
                                                            <select class="js-example-basic-multiple" id="roles"
                                                                name="roles" required>
                                                                <option value="">Seleccione un rol</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Por favor seleccione al menos un rol
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4" id="permissionsContainer"
                                                            style="display: none;">
                                                            <label for="permissions" class="form-label">Permisos
                                                                Adicionales</label>
                                                            <select class="js-example-basic-multiple" id="permissions"
                                                                name="permissions[]" multiple="multiple">
                                                                <option value="">Seleccione</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="office_id" class="form-label">Oficina</label>
                                                            <select class="form-select" id="office_id" name="office_id"
                                                                required>
                                                                <option value="">Seleccione una oficina</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Por favor, seleccione una oficina.
                                                            </div>
                                                            <input type="hidden" class="form-control" id="entity_id"
                                                                name="entity_id" value="">
                                                        </div>
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

        class UserCreate {
            #isSubmitting = false; // Propiedad privada para el estado de envío
            #elements = {
                userCreateForm: document.getElementById('userCreateForm'),
                submitButton: document.getElementById('submitButton'),
                rolesSelect: document.getElementById('roles'),
                permissionsSelect: document.getElementById('permissions'),
                permissionsContainer: document.getElementById('permissionsContainer'),
                officeSelect: document.getElementById('office_id'),
                entityId: document.getElementById('entity_id'),
                passwordInput: document.getElementById('password'),
                passwordConfirmationInput: document.getElementById('password_confirmation'),
                togglePasswordBtn: document.getElementById('togglePassword'),
                togglePasswordConfirmationBtn: document.getElementById('togglePasswordConfirmation')
            };

            constructor() {
                this.#init(); // Inicializa el módulo al crearse la instancia
            }

            /**
             * Inicializa todos los componentes y listeners del formulario.
             */
            async #init() {
                this.#setupValidation();
                await this.#fetchInitialData(); // Carga oficinas, roles y permisos al inicio
                this.#addEventListeners();
                await this.#getEntityId(); // Obtiene el ID de la entidad del usuario actual
            }

            /**
             * Configura la validación del formulario y carga los datos iniciales.
             */
            async #getEntityId() {
                const userData = HTTPService.getUserData();

                this.#elements.entityId.value = userData.entity_id || 'null';

            }

            /**
             * Carga las oficinas y los roles/permisos necesarios para el formulario.
             */
            async #fetchInitialData() {
                try {
                    // Usa Promise.all para cargar todos los datos simultáneamente
                    const [officesResponse, rolesResponse, permissionsResponse] = await Promise.all([
                        HTTPService.get('/api/dashboard/offices-without-user'),
                        HTTPService.get('/api/dashboard/roles'),
                        HTTPService.get('/api/dashboard/permissions')
                    ]);

                    this.#populateOffices(officesResponse.data || []);
                    this.#populateSelect(this.#elements.rolesSelect, rolesResponse.data || [], 'Seleccione un rol');
                    this.#populateSelect(this.#elements.permissionsSelect, permissionsResponse.data || []);
                } catch (error) {
                    console.error('Error al cargar datos iniciales (oficinas, roles, permisos):', error);
                    // Considera mostrar un mensaje al usuario o deshabilitar partes del formulario
                }
            }

            /**
             * Llena el selector de oficinas.
             * @param {Array<Object>} offices - Array de objetos de oficina.
             */
            #populateOffices(offices) {
                const select = this.#elements.officeSelect;
                if (!select) return;

                select.innerHTML = '<option value="">Seleccione una oficina</option>';
                offices.forEach(office => {
                    const option = document.createElement('option');
                    option.value = office.id;
                    option.textContent = office.name;
                    select.appendChild(option);
                });
            }

            /**
             * Llena un elemento select con datos.
             * @param {HTMLSelectElement} selectElement - El elemento select a poblar.
             * @param {Array<Object>} data - Los datos para poblar el select.
             * @param {string} [defaultOptionText=''] - Texto para la opción por defecto.
             */
            #populateSelect(selectElement, data, defaultOptionText = '') {
                if (!selectElement) return;

                // Usa jQuery para la manipulación si es un Select2 o similar,
                // de lo contrario, se puede hacer con JS nativo.
                const $select = $(selectElement); // Envuelve el elemento nativo con jQuery

                $select.empty();
                if (defaultOptionText) {
                    $select.append(new Option(defaultOptionText, '', true, true));
                }

                data.forEach(item => {
                    const option = new Option(item.name, item.id, false, false);
                    $select.append(option);
                });

                // Trigger change solo si jQuery y Select2/similar lo requieren
                $select.trigger('change');
            }

            /**
             * Configura los listeners de eventos para el formulario y otros elementos.
             */
            #addEventListeners() {
                if (this.#elements.userCreateForm) {
                    this.#elements.userCreateForm.addEventListener('submit', this.#handleSubmit.bind(this));
                }
                if (this.#elements.rolesSelect) {
                    $(this.#elements.rolesSelect).on('change', this.#handleRoleChange.bind(this));
                }
                if (this.#elements.togglePasswordBtn) {
                    this.#elements.togglePasswordBtn.addEventListener('click', () =>
                        this.#togglePasswordVisibility(this.#elements.passwordInput, this.#elements
                            .togglePasswordBtn)
                    );
                }
                if (this.#elements.togglePasswordConfirmationBtn) {
                    this.#elements.togglePasswordConfirmationBtn.addEventListener('click', () =>
                        this.#togglePasswordVisibility(this.#elements.passwordConfirmationInput, this.#elements
                            .togglePasswordConfirmationBtn)
                    );
                }
            }

            /**
             * Maneja el cambio en el selector de roles para mostrar/ocultar el contenedor de permisos.
             * @param {Event} event - El evento de cambio.
             */
            async #handleRoleChange(event) {
                const roleId = event.target.value;
                if (!this.#elements.permissionsContainer) return; // Asegúrate de que el contenedor exista

                if (roleId) {
                    try {
                        const responseData = await HTTPService.get(`/api/dashboard/roles/${roleId}/has-permissions`);
                        // Asumiendo que el backend devuelve { can_assign_custom_permissions: true/false }
                        const canAssignCustomPermissions = responseData.can_assign_custom_permissions === true;

                        if (canAssignCustomPermissions) {
                            $(this.#elements.permissionsContainer).show();
                        } else {
                            $(this.#elements.permissionsContainer).hide();
                        }
                    } catch (error) {
                        console.error('Error al verificar permisos del rol:', error);
                        $(this.#elements.permissionsContainer).hide(); // Ocultar por seguridad en caso de error
                    }
                } else {
                    $(this.#elements.permissionsContainer).hide(); // Si no hay rol seleccionado, ocultar
                }
            }

            /**
             * Maneja el envío del formulario.
             * @param {Event} event - El evento de envío.
             */
            async #handleSubmit(event) {
                event.preventDefault();

                if (this.#isSubmitting) return;

                // Usar la validación de jQuery
                const $form = $(this.#elements.userCreateForm);
                if ($form.valid()) {
                    this.#isSubmitting = true;
                    // Opcional: Deshabilitar el botón de envío
                    if (this.#elements.submitButton) {
                        this.#elements.submitButton.disabled = true;
                    }

                    const formData = new FormData(this.#elements.userCreateForm);

                    // Convertir FormData a un objeto plano, incluyendo manejo de arrays (permissions[])
                    const formDataObject = {};
                    for (const [key, value] of formData.entries()) {
                        if (key.endsWith('[]')) { // Para campos de array como 'permissions[]'
                            const cleanKey = key.slice(0, -2); // Elimina '[]'
                            if (!formDataObject[cleanKey]) {
                                formDataObject[cleanKey] = [];
                            }
                            formDataObject[cleanKey].push(value);
                        } else {
                            formDataObject[key] = value;
                        }
                    }
                    // Asegúrate de incluir los permisos si el contenedor está visible y tiene valores
                    // (esto ya debería estar cubierto por formData.entries si el select es multiple)
                    // Si el select no es multiple y se selecciona solo un rol, puede que necesites manejarlo de otra forma.

                    try {
                        const response = await HTTPService.post('/api/dashboard/users', formDataObject);
                        Helpers.getMessage('Usuario creado exitosamente', '/dashboard/usuarios');
                    } catch (error) {
                        console.error('Error al crear el usuario:', error);
                        // Aquí puedes agregar lógica para mostrar errores de validación del backend si los hay
                        // Por ejemplo: Helpers.showErrorMessages(error.data.errors);
                    } finally {
                        this.#isSubmitting = false;
                        if (this.#elements.submitButton) {
                            this.#elements.submitButton.disabled = false;
                        }
                    }
                }
            }

            /**
             * Configura las reglas de validación del formulario usando jQuery Validate.
             */
            #setupValidation() {
                const $form = $(this.#elements.userCreateForm);
                if (!$form.length) return; // Salir si el formulario no existe

                $.validator.setDefaults({
                    errorClass: 'is-invalid',
                    validClass: 'is-valid',
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.mb-3').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass(errorClass).removeClass(validClass);
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass(errorClass).addClass(validClass);
                    }
                });

                $form.validate({
                    rules: {
                        name: "required",
                        email: {
                            required: true,
                            email: true // Agrega validación de formato de email
                        },
                        password: {
                            required: true,
                            minlength: 6 // Recomendar una longitud mínima
                        },
                        password_confirmation: {
                            required: true,
                            equalTo: '#password' // Asegura que coincida con la contraseña
                        },
                        phone: "required",
                        roles: "required",
                        // Si los permisos son opcionales, no los incluyas en rules
                        // Si son requeridos bajo ciertas condiciones, la lógica se vuelve más compleja
                        // 'permissions[]': {}
                    },
                    messages: {
                        name: "Por favor, ingrese el nombre del usuario.",
                        email: {
                            required: "Por favor, ingrese un correo electrónico.",
                            email: "Por favor, ingrese un formato de correo electrónico válido."
                        },
                        password: {
                            required: "Por favor, ingrese una contraseña.",
                            minlength: "La contraseña debe tener al menos {0} caracteres."
                        },
                        password_confirmation: {
                            required: "Por favor, confirme su contraseña.",
                            equalTo: "Las contraseñas no coinciden."
                        },
                        phone: "Por favor, ingrese un número de teléfono.",
                        roles: "Por favor, seleccione al menos un rol." // Asumiendo que 'roles' puede ser multiple o singular
                    },
                    submitHandler: (form, event) => {
                        // jQuery Validate ya previene el envío por defecto, así que solo llamamos a handleSubmit
                        // Pero como nuestro handleSubmit ya previene el defecto, se puede omitir event.preventDefault() aquí
                        this.#handleSubmit(event);
                    }
                });
            }

            /**
             * Alterna la visibilidad de un campo de contraseña.
             * @param {HTMLInputElement} passwordInput - El campo de contraseña.
             * @param {HTMLElement} toggleButton - El botón de alternar visibilidad.
             */
            #togglePasswordVisibility(passwordInput, toggleButton) {
                if (!passwordInput || !toggleButton) return;

                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                toggleButton.querySelector('i').classList.toggle('mdi-eye');
                toggleButton.querySelector('i').classList.toggle('mdi-eye-off');
            }

            // Métodos públicos (opcional, si quieres que se puedan llamar desde fuera de la clase)
            // En este caso, ya no se exponen a window.UserCreate como antes,
            // ya que la instancia se maneja dentro del script principal del módulo.
            // Si necesitas acceder a ellos globalmente (ej. desde Blade), podrías:
            // window.userCreateInstance = new UserCreate();
            // Y luego llamar window.userCreateInstance.somePublicMethod();
        }

        // Inicializa el módulo cuando el DOM esté completamente cargado.
        // Esta es la forma moderna de un document.ready en módulos JS.
        document.addEventListener('DOMContentLoaded', () => {
            new UserCreate();
        });

        // Nota: No se exporta UserCreate, ya que es una clase que se inicializa a sí misma
        // en esta configuración. Si UserCreate fuera un servicio que otros módulos necesitan
        // importar y usar (ej. para llamar métodos específicos desde otro lugar),
        // entonces se exportaría: export default UserCreate;
    </script>
@endsection
