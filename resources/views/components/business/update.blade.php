@extends('layouts.app')

@section('title', 'Crear Departamento')

@section('content')
    <div data-permissions="business.update" class="visible">
        <div class="row pt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Crear Nuevo Departamento</h4>
                        <p class="text-muted font-14">
                            Complete el formulario para registrar un nuevo departamento.
                        </p>

                        <form id="AddForm" name="AddForm" class="needs-validation" novalidate>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="nit" class="form-label">NIT</label>
                                    <input type="number" class="form-control" id="nit" name="nit" autocomplete="off"
                                           required>
                                    <div class="invalid-feedback">
                                        Por favor ingrese el NIT.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="verification_digit" class="form-label">Dígito de Verificación</label>
                                    <input type="number" class="form-control" id="verification_digit"
                                           name="verification_digit" required autocomplete="off">
                                    <div class="invalid-feedback">
                                        Por favor ingrese el dígito de verificación (0-9).
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombre de la Entidad</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                           autocomplete="off">
                                    <div class="invalid-feedback">
                                        Por favor ingrese el nombre de la entidad.
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="type" class="form-label">Tipo de Entidad</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Seleccione...</option>
                                        <option value="PÚBLICO">PÚBLICO</option>
                                        <option value="PRIVADO">PRIVADO</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor seleccione el tipo de entidad.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="address" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                           autocomplete="off" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" autocomplete="off"
                                           required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-5">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" autocomplete="off"
                                           required>
                                    <div class="invalid-feedback">
                                        Por favor ingrese un correo electrónico válido.
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="legal_representative" class="form-label">Representante Legal</label>
                                    <input type="text" class="form-control" id="legal_representative"
                                           name="legal_representative" autocomplete="off" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="employee_count" class="form-label">Número de Empleados</label>
                                    <input type="number" class="form-control" id="employee_count" name="employee_count"
                                           min="0" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="creation_date" class="form-label">Fecha de Creación</label>
                                    <input type="date" class="form-control" id="creation_date" name="creation_date">
                                </div>

                                <div class="col-md-5">
                                    <label for="website" class="form-label">Sitio Web</label>
                                    <input type="text" class="form-control" id="website" name="website"
                                           autocomplete="off">
                                    <div class="invalid-feedback">
                                        Por favor ingrese una URL válida.
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="filepath" class="form-label">Logo</label>
                                    <input type="file" class="form-control" id="filepath" name="filepath"
                                           accept="image/*">
                                    <input type="hidden" class="form-control" id="logo" name="logo">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <a href="/dashboard/empresa"
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
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const UpdateEntity = (() => {
            // Private variables
            let isSubmitting = false;
            let filePath = '';

            const elements = {
                AddForm: '#AddForm',
                submitButton: '#submitButton',
                nit: '#nit',
                verificationDigit: '#verification_digit',
                name: '#name',
                type: '#type',
                address: '#address',
                phone: '#phone',
                email: '#email',
                creationDate: '#creation_date',
                legalRepresentative: '#legal_representative',
                employeeCount: '#employee_count',
                website: '#website',
                logo: '#logo',
                fileInput: 'input[name="filepath"]',
                status: '#status'
            };

            // Función auxiliar para formatear la fecha
            const formatDate = (dateString) => {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toISOString().split('T')[0];
            };

            // Private methods
            const updateEntity = async (event) => {
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
                        const id = Helpers.getAllGetParams(3);
                        const response = await HTTPService.patch(`/api/dashboard/entity/update/${id}`, formDataObject);
                        console.log(response)
                        Helpers.getMessage('Se ha actualizado la entidad correctamente', '/dashboard/empresa');
                    } catch (error) {
                        console.error('Error al crear la entidad:', error);
                    } finally {
                        isSubmitting = false;
                    }
                }
            };

            const handleFileChange = async (event) => {
                const url = '/api/dashboard/upload/logo';
                try {
                    const uploadedData = await HTTPService.upload(url, event);
                    filePath = uploadedData;
                    document.querySelector(elements.logo).value = uploadedData;
                } catch (error) {
                    console.error('Error al cargar el archivo:', error.message);
                    Helpers.getMessage('Error al cargar el archivo', null, 'error');
                }
            };

            const loadEntityData = async () => {
                const id = Helpers.getAllGetParams(3);
                try {
                    const response = await HTTPService.get(`/api/dashboard/entity/show/${id}`);
                    populateForm(response.data);
                } catch (error) {
                    console.error('Error al cargar los datos de la oficina:', error);
                }
            };

            const setupValidation = () => {
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

                $(elements.AddForm).validate({
                    rules: {
                        nit: {
                            required: true,
                            number: true
                        },
                        verification_digit: {
                            required: true,
                            number: true,
                            min: 0,
                            max: 9
                        },
                        name: "required",
                        type: "required",
                        email: {
                            email: true
                        },
                        creation_date: {
                            date: true
                        },
                        employee_count: {
                            number: true,
                            min: 0
                        },
                    },
                    messages: {
                        nit: {
                            required: "Por favor, ingrese el NIT",
                            number: "Por favor, ingrese un número válido"
                        },
                        verification_digit: {
                            required: "Por favor, ingrese el dígito de verificación",
                            number: "Por favor, ingrese un número válido",
                            min: "El dígito de verificación debe ser entre 0 y 9",
                            max: "El dígito de verificación debe ser entre 0 y 9"
                        },
                        name: "Por favor, ingrese el nombre de la entidad",
                        type: "Por favor, seleccione el tipo de entidad",
                        email: {
                            email: "Por favor, ingrese un correo electrónico válido"
                        },
                        creation_date: {
                            date: "Por favor, ingrese una fecha válida"
                        },
                        employee_count: {
                            number: "Por favor, ingrese un número válido",
                            min: "El número de empleados no puede ser negativo"
                        }
                    },
                    submitHandler: (form, event) => {
                        event.preventDefault();
                        createEntity(event);
                    }
                });
            };

            const populateForm = (EntityData) => {
                document.querySelector(elements.nit).value = EntityData.nit;
                document.querySelector(elements.verificationDigit).value = EntityData.verification_digit;
                document.querySelector(elements.name).value = EntityData.name;
                document.querySelector(elements.type).value = EntityData.type;
                document.querySelector(elements.address).value = EntityData.address;
                document.querySelector(elements.phone).value = EntityData.phone;
                document.querySelector(elements.email).value = EntityData.email;
                document.querySelector(elements.creationDate).value = formatDate(EntityData.creation_date);
                document.querySelector(elements.legalRepresentative).value = EntityData.legal_representative;
                document.querySelector(elements.employeeCount).value = EntityData.employee_count;
                document.querySelector(elements.website).value = EntityData.website;
                document.querySelector(elements.logo).value = EntityData.logo;
            };

            // Initialize when DOM is ready
            $(document).ready(() => {
                setupValidation();
                loadEntityData();

                // Set up event listeners
                document.querySelector(elements.fileInput).addEventListener('change', handleFileChange);
                document.querySelector(elements.AddForm).addEventListener('submit', updateEntity);
            });

            // Create public API
            const publicApi = {
                updateEntity,
                handleFileChange,
                setupValidation
            };

            // Expose to window object for inline event handlers
            window.UpdateEntity = publicApi;

            // Return public API for module imports
            return publicApi;
        })();

    </script>
@endsection
