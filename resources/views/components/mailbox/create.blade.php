@extends('layouts.app')

@section('title', 'Responder solicitudes')

@section('content')
    <div class="dashboard">
        <div data-permissions="api.mailbox.store">
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
                                                <input type="hidden" class="form-control"
                                                       id="correspondence_transfer_id"
                                                       name="correspondence_transfer_id" readonly>
                                                <input type="hidden" class="form-control" id="document_id"
                                                       name="document_id" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-lg-12 form-group">
                                                <label for="job_type">Resultado de la Respuesta</label>
                                                <textarea class="form-control" id="response_content"
                                                          name="response_content" rows="5"
                                                          required></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-lg-7 form-group">
                                                <label for="reference_code">CC Email</label>
                                                <input type="email" class="form-control" id="response_email"
                                                       name="response_email">
                                            </div>
                                            <div class="col-md-6 col-lg-5 form-group">
                                                <label for="responsepath">Respuesta</label>
                                                <input type="file" class="form-control" id="filepath"
                                                       name="filepath">
                                                <input type="hidden" class="form-control" id="response_document_path"
                                                       name="response_document_path">
                                                <input type="hidden" class="form-control" id="directory"
                                                       name="directory">
                                                <input type="hidden" class="form-control" id="response_file"
                                                       name="response_file">
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

        const TransferComponent = HTTPService.createComponent({
            data: () => ({
                isSubmitting: false,
                filePath: '',
                error: null,
            }),

            methods: {
                async fetchOfficeId() {
                    const {
                        directory,
                        referenceCode,
                        subject,
                        senderName,
                        documentId,
                        transferId,
                        responseFile,
                    } = this.elements;

                    try {
                        let id = Helpers.getAllGetParams(3);
                        const response = await HTTPService.get(`api/dashboard/correspondence-transfer/show/${id}`);

                        if (response && response.data) {
                            console.log(response.data);

                            const document = response.data.document;
                            const documentStatus = response.data.document.document_status;

                            const Path = document.file_path;
                            if (Path) {
                                let doc = 'respuesta';
                                const fileName = Path.split('/').pop();
                                const fileNumbers = fileName.match(/\d+/)[0];
                                responseFile.value = doc + '_' + fileName;
                                directory.value = fileNumbers;
                            }

                            referenceCode.value = document.reference_code || '';
                            subject.value = document.subject || '';
                            senderName.value = document.sender_name || '';
                            documentId.value = document.id || '';
                            transferId.value = response.data.id || '';

                            // Asegúrate de que estos valores también se muestren si son necesarios
                            // documentStatus.status // Estado del documento
                        }
                    } catch (error) {
                        console.error('Error al obtener datos del usuario de la oficina', error);
                        this.showError('No se pudieron cargar las oficinas');
                    }
                },
                async handleFileChange(event) {
                    const url = '/api/dashboard/response/upload';
                    try {
                        const uploadedData = await HTTPService.upload(url, event);
                        this.setData({filePath: uploadedData});
                        this.elements.filePath.value = uploadedData;
                    } catch (error) {
                        console.error('Error al cargar el archivo:', error.message);
                    }
                },
                async createReply(event) {
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
                            const response = await HTTPService.post(`api/dashboard/mailbox/store`, formDataObject);
                            Helpers.getMessage('Transferencia exitosa', '/dashboard/mi-buzon')
                        } catch (error) {
                            console.error('Error al crear la transferencia', error);
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
                            response_content: "required",
                        },
                        messages: {
                            response_content: "Por favor, ingrese la respuesta a solicitud",
                        },
                        submitHandler: (form, event) => {
                            event.preventDefault();
                            this.createReply(event);
                        }
                    });
                },
            },

            elements: {
                filePath: '#response_document_path',
                fileInput: 'input[name="filepath"]',
                directory: '#directory',
                referenceCode: '#reference_code',
                subject: '#subject',
                senderName: '#sender_name',
                documentId: '#document_id',
                transferId: '#correspondence_transfer_id',
                responseFile: '#response_file',
                AddForm: '#AddForm',
                submitButton: '#submitButton'
            },

            created() {
                this.fetchOfficeId();
            },

            render() {
                if (!this.officeSelectListenerAdded) {
                    this.elements.fileInput.addEventListener('change', this.handleFileChange.bind(this));
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
