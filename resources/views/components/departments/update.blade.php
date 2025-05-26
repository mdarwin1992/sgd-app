@extends('layouts.app')

@section('title', 'Actualizar Departamento')

@section('content')
    <div class="dashboard">
        <div data-permissions="departments.update">
            <div class="row  pt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Actualizar Departamento</h4>
                            <p class="text-muted font-14">
                                Modifique la información del departamento y guarde los cambios.
                            </p>

                            <form id="updateDepartmentForm" class="needs-validation" novalidate>
                                <input type="hidden" id="departmentId" name="id">
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
                                        <label for="entity" class="form-label">Entidad</label>
                                        <select class="form-control" id="entity" name="entity_id" required>
                                            <option value="">Seleccione una entidad</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, seleccione una entidad.
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <a href="/dashboard/departamentos"
                                       class="btn btn-primary rounded-pill  btn-tool me-2">
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
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const DepartmentUpdateComponent = (() => {
            // Private variables
            let isSubmitting = false;
            let entities = [];
            let departmentId = null;

            const elements = {
                updateDepartmentForm: '#updateDepartmentForm',
                submitButton: '#submitButton',
                departmentId: '#departmentId',
                code: '#code',
                name: '#name',
                entity: '#entity',
                status: '#status',
            };

            // Private methods
            const loadEntities = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/entities');
                    entities = response.data;
                    populateEntitySelect();
                } catch (error) {
                    console.error('Error al cargar las entidades:', error);
                }
            };

            const populateEntitySelect = () => {
                const entitySelect = document.querySelector(elements.entity);
                entitySelect.innerHTML = '<option value="">Seleccione una entidad</option>';
                entities.forEach(entity => {
                    const option = document.createElement('option');
                    option.value = entity.id;
                    option.textContent = entity.name;
                    entitySelect.appendChild(option);
                });
            };

            const loadDepartmentData = async () => {
                let id = Helpers.getAllGetParams(3);
                try {
                    const response = await HTTPService.get(`api/dashboard/department/show/${id}`);
                    const department = response.data;

                    document.querySelector(elements.departmentId).value = department.id;
                    document.querySelector(elements.code).value = department.code;
                    document.querySelector(elements.name).value = department.name;
                    document.querySelector(elements.entity).value = department.entity_id;
                    document.querySelector(elements.status).value = department.status;
                } catch (error) {
                    console.error('Error al cargar los datos del departamento:', error);
                }
            };

            const updateDepartment = async (event) => {
                event.preventDefault();

                let id = Helpers.getAllGetParams(3);

                if (isSubmitting) {
                    return;
                }

                if ($(elements.updateDepartmentForm).valid()) {
                    isSubmitting = true;

                    const form = document.querySelector(elements.updateDepartmentForm);
                    const formData = new FormData(form);
                    const formDataObject = Object.fromEntries(formData);

                    try {
                        const response = await HTTPService.patch(`api/dashboard/department/update/${id}`, formDataObject);
                        Helpers.getMessage('Departamento actualizado exitosamente', '/dashboard/departamentos');
                    } catch (error) {
                        console.error('Error al actualizar el departamento:', error);
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

                $(elements.updateDepartmentForm).validate({
                    rules: {
                        code: "required",
                        name: "required",
                        entity_id: "required",
                        status: "required"
                    },
                    messages: {
                        code: "Por favor, ingrese un código para el departamento",
                        name: "Por favor, ingrese un nombre para el departamento",
                        entity_id: "Por favor, seleccione una entidad",
                        status: "Por favor, seleccione un estado"
                    },
                    submitHandler: (form, event) => {
                        event.preventDefault();
                        updateDepartment(event);
                    }
                });
            };

            // Initialize when DOM is ready
            $(document).ready(async () => {
                await loadEntities();
                await loadDepartmentData();
                setupValidation();

                // Set up event listeners
                document.querySelector(elements.updateDepartmentForm).addEventListener('submit', updateDepartment);
            });

            // Create public API
            const publicApi = {
                loadEntities,
                loadDepartmentData,
                updateDepartment,
                setupValidation
            };

            // Expose to window object for inline event handlers
            window.DepartmentUpdateManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();

    </script>
@endsection
