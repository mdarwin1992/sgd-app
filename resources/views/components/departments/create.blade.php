@extends('layouts.app')

@section('title', 'Crear Departamento')

@section('content')
    <div class="row pt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Crear Nuevo Departamento</h4>
                    <p class="text-muted font-14">
                        Complete el formulario para registrar un nuevo departamento.
                    </p>

                    <form id="createDepartmentForm" class="needs-validation" novalidate>
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
                            <a href="/dashboard/departamentos" class="btn btn-primary btn-tool rounded-pill me-2">
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
@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';
        import Helpers from '/services/httpService/Helpers.js';

        const DepartmentCreateComponent = HTTPService.createComponent({
            data: () => ({
                isSubmitting: false,
                entities: [],
            }),

            methods: {
                async loadEntities() {
                    try {
                        const response = await HTTPService.get('/api/dashboard/entities');
                        this.setData({entities: response.data});
                        this.populateEntitySelect();
                    } catch (error) {
                        console.error('Error al cargar las entidades:', error);
                    }
                },

                populateEntitySelect() {
                    const entitySelect = this.elements.entity;
                    entitySelect.innerHTML = '<option value="">Seleccione una entidad</option>';
                    this.data.entities.forEach(entity => {
                        const option = document.createElement('option');
                        option.value = entity.id;
                        option.textContent = entity.name;
                        entitySelect.appendChild(option);
                    });
                },

                async createDepartment(event) {
                    event.preventDefault();

                    if (this.data.isSubmitting) {
                        return;
                    }

                    if ($(this.elements.createDepartmentForm).valid()) {
                        this.setData({isSubmitting: true});

                        const form = this.elements.createDepartmentForm;
                        const formData = new FormData(form);
                        const formDataObject = Object.fromEntries(formData.entries());

                        try {
                            const response = await HTTPService.post('/api/dashboard/department/store', formDataObject);
                            Helpers.getMessage('Departamento creado exitosamente', '/dashboard/departamentos');
                        } catch (error) {
                            console.error('Error al crear el departamento:', error);
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

                    $(this.elements.createDepartmentForm).validate({
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
                            this.createDepartment(event);
                        }
                    });
                }
            },

            elements: {
                createDepartmentForm: '#createDepartmentForm',
                submitButton: '#submitButton',
                code: '#code',
                name: '#name',
                entity: '#entity',
                status: '#status',
            },

            created() {
                this.loadEntities();
            },

            render() {
                this.setupValidation();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            DepartmentCreateComponent.init();
        });
    </script>
@endsection
