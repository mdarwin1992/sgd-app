@extends('layouts.app')

@section('title', 'Consultas')

@section('content')
    <div data-permissions="centralfile.create">
        <div class="row pt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Sistema de Gestión de Documentos</h4>
                        <p class="text-muted font-14">
                            Un archivo central organiza y conserva documentos de una entidad de manera sistemática.
                        </p>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="text" id="documentCode" class="form-control form-control-sm" readonly>
                            </div>
                            <div class="col-md-9">
                                <button type="button" class="btn btn-primary btn-sm"
                                        onclick="window.DocumentManagement.showSearchModal()">
                                    Buscar Documento
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm"
                                        onclick="window.DocumentManagement.openSearchModal('box')">Buscar Por Caja
                                </button>
                                <button type="button" class="btn btn-info btn-sm"
                                        onclick="window.DocumentManagement.openSearchModal('series')">Buscar por
                                    Serie
                                </button>
                                <button type="button" class="btn btn-success btn-sm"
                                        onclick="window.DocumentManagement.showSigns()">
                                    Rotulos
                                </button>
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="window.DocumentManagement.openSearchModal('year')">Buscar Por Año
                                </button>
                                <button type="button" class="btn btn-danger btn-sm"
                                        onclick="window.DocumentManagement.showDeleteModal()">
                                    Eliminar
                                </button>

                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="documentsTable"
                                   class="table table-striped table-sm table-centered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>CODIGO_SISTEMA</th>
                                    <th>Archivado</th>
                                    <th>Oficina</th>
                                    <th>Serie</th>
                                    <th>Número de Folio</th>
                                    <th>Terceros</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Search Modal -->
    <div class="modal fade" id="bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Resultado de Búsqueda en Archivo Central</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body" id="results">
                    ...
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Código del Sistema:</label>
                                <input type="text" class="form-control" id="updateCodigoSistema" readonly>
                            </div>
                            <div class="form-group">
                                <label>Nombre del Documento:</label>
                                <input type="text" class="form-control" id="updateNombreDocumento">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="window.DocumentManagement.updateDocument()">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar el siguiente documento?</p>
                    <div id="deleteList" class="alert alert-warning">
                    </div>
                    <input type="text" id="deleteImput" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="window.DocumentManagement.deleteDocument()">
                        Eliminar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bs-example-modal-lg-pdfViewer" tabindex="-1" role="dialog"
         aria-labelledby="pdfViewerModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfViewerModalTitle">Visor de PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body" style="height: 80vh;"> <!-- Altura fija para mejor visualización -->
                    <iframe id="pdfViewer"
                            style="width: 100%; height: 100%; border: none;"
                            allowfullscreen>
                    </iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="full-width-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-full-width">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="fullWidthModalLabel">Búsqueda de Documentos</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="searchType" class="form-select">
                                <option value="box">Buscar por Caja</option>
                                <option value="series">Buscar por Serie</option>
                                <option value="year">Buscar por Año</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <select id="seriesSelect" class="form-select d-none">
                                    <option value="">Seleccione una serie</option>
                                </select>
                                <input type="text" id="searchTerm" class="form-control"
                                       placeholder="Ingrese término de búsqueda">
                                <button onclick="window.DocumentManagement.searchDocuments()" class="btn btn-primary">
                                    <i class="mdi mdi-magnify"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="resultsTable"
                               class="table table-striped table-sm table-centered t-responsive mb-0 nowrap w-100">
                            <thead>
                            <tr>
                                <th scope="col">Código</th>
                                <th scope="col">Serie y Sub Serie</th>
                                <th scope="col">Referencia Documental</th>
                                <th scope="col">Tercero(s)</th>
                                <th scope="col">Estante</th>
                                <th scope="col">Caja</th>
                                <th scope="col">Orden</th>
                                <th scope="col">N° Folio</th>
                                <th scope="col">Año</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Results will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

@endsection

@section('scripts')
    <script type="module">
        import HTTPService from '/services/httpService/HTTPService.js';

        const DocumentManagement = (() => {
            let documents = [];
            let dataTable = null;
            let selectedCode = '';
            let currentSearchType = 'box';

            const fetchDocuments = async () => {
                try {
                    const response = await HTTPService.get('/api/dashboard/central-archive');
                    documents = response.data || response;
                    initializeDataTable(documents);
                } catch (error) {
                    console.error('Error al obtener los documentos:', error);
                    Swal.fire('Error', 'Error al cargar los documentos. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            const initializeDataTable = (data) => {
                if (dataTable) {
                    dataTable.destroy();
                }

                dataTable = $('#documentsTable').DataTable({
                    data: data,
                    columns: [
                        {
                            data: null,
                            render: (data, type, row) =>
                                `<input type="checkbox" class="document-checkbox" data-codigo="${row.filed}" onclick="window.DocumentManagement.toggleDocument(this, '${row.filed}')">`,
                            orderable: false
                        },
                        {data: 'filed', title: 'Codigo sistema'},
                        {data: 'document_reference', title: 'Referencia del Documento'},
                        {data: 'office.name', title: 'Oficina'},
                        {data: 'series.series_entity.series_name', title: 'Serie'},
                        {data: 'folio_number', title: 'Número de Folio'},
                        {data: 'third_parties', title: 'Terceros'}
                    ],
                    language: {
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros por página",
                        zeroRecords: "No se encontraron resultados",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "Mostrando 0 a 0 de 0 registros",
                        infoFiltered: "(filtrado de _MAX_ registros totales)",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        }
                    }
                });
            };

            const toggleDocument = (checkbox, filed) => {
                if (selectedCode === filed) {
                    selectedCode = '';
                    checkbox.checked = false;
                } else {
                    if (selectedCode) {
                        document.querySelector(`input[data-codigo="${selectedCode}"]`).checked = false;
                    }
                    selectedCode = filed;
                    checkbox.checked = true;
                }
                updateSelectedItems();
            };

            const updateSelectedItems = () => {
                const documentCodeElement = document.getElementById('documentCode');
                if (documentCodeElement) {
                    documentCodeElement.value = selectedCode;
                }
            };

            const showSearchModal = async () => {
                if (!selectedCode) {
                    await Swal.fire("Por favor seleccione un documento primero");
                    return;
                }

                try {
                    const response = await HTTPService.get(`/api/dashboard/find-document/${selectedCode}`);
                    displayResults(response);
                    $('#bs-example-modal-lg').modal('show');
                } catch (error) {
                    if (error.message.includes('401')) {
                        await Swal.fire('Error', 'Sesión expirada. Por favor, inicie sesión nuevamente.', 'error');
                    } else {
                        await Swal.fire('Error', 'Error al buscar documentos', 'error');
                    }
                }
            };

            const showSigns = () => {
                if (!selectedCode) {
                    Swal.fire("Por favor seleccione un documento primero");
                    return;
                }

                const selectedDoc = documents.find(doc => doc.filed === selectedCode);
                const url = `/reportes/rotulos/${selectedDoc.filed}`;
                window.open(url, '_blank');
            };

            const showUpdateModal = () => {
                if (!selectedCode) {
                    Swal.fire("Por favor seleccione un documento primero");
                    return;
                }

                const selectedDoc = documents.find(doc => doc.filed === selectedCode);
                if (selectedDoc) {
                    $('#updateCodigoSistema').val(selectedDoc.filed);
                    $('#updateNombreDocumento').val(selectedDoc.document_reference);
                    $('#updateModal').modal('show');
                }
            };

            const showDeleteModal = () => {
                if (!selectedCode) {
                    Swal.fire("Por favor seleccione un documento primero");
                    return;
                }

                const selectedDoc = documents.find(doc => doc.filed === selectedCode);
                if (selectedDoc) {
                    $('#deleteList').html(`Código: ${selectedDoc.filed}<br>Referencia: ${selectedDoc.document_reference}`);
                    $('#deleteImput').val(selectedDoc.id)
                    $('#deleteModal').modal('show');
                }
            };

            const deleteDocument = async () => {
                if (!selectedCode) {
                    await Swal.fire('Error', 'Por favor seleccione un documento para eliminar', 'warning');
                    return;
                }

                try {
                    const deleteId = $('#deleteImput').val()

                    await HTTPService.delete(`/api/dashboard/central-archive/destroy/${deleteId}`);

                    documents = documents.filter(doc => doc.document_reference !== selectedCode);
                    selectedCode = '';
                    updateSelectedItems();
                    dataTable.clear().rows.add(documents).draw();

                    $('#deleteModal').modal('hide');
                    await Swal.fire('Éxito', 'Documento eliminado exitosamente', 'success');
                    window.location.reload();
                } catch (error) {
                    console.error('Error al eliminar el documento:', error);
                    await Swal.fire('Error', 'Error al eliminar el documento. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            const updateDocument = async () => {
                const codigo = $('#updateCodigoSistema').val();
                const nuevoNombre = $('#updateNombreDocumento').val();

                try {
                    await HTTPService.put(`/api/dashboard/central-archive/update/${codigo}`, {
                        nombre: nuevoNombre
                    });

                    const index = documents.findIndex(doc => doc.document_reference === codigo);
                    if (index !== -1) {
                        documents[index].nombre = nuevoNombre;
                        dataTable.clear().rows.add(documents).draw();
                        $('#updateModal').modal('hide');
                        await Swal.fire('Éxito', 'Documento actualizado exitosamente', 'success');
                    } else {
                        await Swal.fire('Error', 'Error al actualizar el documento', 'error');
                    }
                } catch (error) {
                    console.error('Error al actualizar el documento:', error);
                    await Swal.fire('Error', 'Error al actualizar el documento. Por favor, intente de nuevo más tarde.', 'error');
                }
            };

            const formatDate = (fecha) => {
                const fechaUTC = new Date(fecha);
                const options = {
                    timeZone: 'America/Bogota',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                };
                return new Intl.DateTimeFormat('es-CO', options).format(fechaUTC);
            };

            const displayResults = (data) => {
                if (!data || data.length === 0) {
                    $('#results').html('<div class="alert alert-info">No se encontraron resultados</div>');
                    return;
                }

                const resultsHtml = data.map(doc => generateDocumentHtml(doc)).join('');
                $('#results').html(resultsHtml);
            };

            const generateDocumentHtml = (doc) => {
                const ruta = doc.file_path;
                const numero = ruta.match(/archivo_central\/(\d+)\//);

                if (numero) {
                    console.log(numero[1]); // Esto imprimirá "8111"
                } else {
                    console.log("No se encontró un número en la ruta");
                }

                return `
                <div class="col-lg-12">
                        <div class="border p-1 mt-1 mt-lg-0 rounded">
                            <div class="table-responsive">
                                <table class="table table-sm table-centered t-responsive mb-0 nowrap w-100">
                                    <tbody>
                                        <tr>
                                            <th>
                                                Código Sistema<br>
                                                ${doc.system_code || ''}
                                            </th>
                                            <th>Retención Doc <br> </th>
                                            <th colspan="3">Codigo Documental <br> ${doc.filed || ''}</th>
                                        </tr>
                                        <tr>
                                            <th>Codigo de Oficina <br> ${doc.office?.code || ''}</th>
                                            <th colspan="4">Oficina <br> ${doc.office?.name.toUpperCase() || ''}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="5">Referencia del Documento <br> ${doc.document_reference || ''}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="5">Terceros <br> ${doc.third_parties || ''}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="5">Objeto <br> ${doc.object_observations || ''}</th>
                                        </tr>
                                        <tr>
                                            <th>Codigo Serie <br> ${doc.series?.series_code || ''}</th>
                                            <th colspan="3">Nombre Serie <br> ${doc.series?.series_entity?.series_name || ''}</th>
                                             <th>N° Estante <br> ${doc.shelf_number || ''}</th>
                                        </tr>
                                        <tr>
                                            <th>Cod. Sub Serie <br> ${doc.subseries?.subseries_code || ''}</th>
                                            <th colspan="3">Nombre Sub Serie <br> ${doc.subseries?.subseries_name || ''}</th>
                                            <th>Bandeja <br> ${doc.tray || ''}</th>
                                        </tr>
                                        <tr>
                                            <th>Soportado en <br> ${doc.support || ''}</th>
                                            <th>Fecha Inicial <br> ${formatDate(doc.start_date) || ''}</th>
                                            <th>Fecha Final <br> ${formatDate(doc.end_date) || ''}</th>
                                            <th>N° Caja <br> ${doc.box_number || ''}</th>
                                            <th>N° Orden <br> ${doc.ord_number || ''}</th>
                                        </tr>
                                        <tr>
                                            <th>Medio Cons <br> ${doc.main_conservation_medium || ''}</th>
                                            <th>Conservado en <br> ${doc.preserved_in || ''}</th>
                                            <th>Año Caja <br> ${doc.folder_year || ''}</th>
                                            <th>Archivado En <br> ${formatDate(doc.updated_at)}</th>
                                            <th>N° Folio <br> ${doc.folio_number || ''}</th>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="d-flex justify-content-end mt-2">
                            <div class="btn-group mb-2">
                                <button type="button" class="btn btn-warning" onclick="window.DocumentManagement.downloadPDF(${numero ? numero[1] : ''})"> <i class="fas fa-download"></i> Descargar PDF</button>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i> Cerrar</button>
                            </div>
                            </div>
                        </div>
                </div>
        `;
            };

            const downloadPDF = async (id) => {
                if (!id) {
                    await Swal.fire('Error', 'ID de documento no válido', 'error');
                    return;
                }

                try {
                    $('#bs-example-modal-lg').modal('hide');
                    const pdfViewer = document.getElementById('pdfViewer');
                    pdfViewer.src = `/storage/upload/archivo_central/${id}/${id}.pdf`;
                    $('#bs-example-modal-lg-pdfViewer').modal('show');
                } catch (error) {
                    console.error('Error al descargar el PDF:', error);
                    await Swal.fire('Error', 'Error al descargar el PDF', 'error');
                }
            };

            const openSearchModal = (searchType) => {
                currentSearchType = searchType;
                $('#searchType').val(searchType);
                $('#searchTerm').val('');
                $('#seriesSelect').val('');
                $('#resultsTable tbody').empty();
                updateSearchForm();
                $('#full-width-modal').modal('show');
            };

            const updateSearchForm = async () => {
                const searchType = $('#searchType').val();
                const searchTermInput = $('#searchTerm');
                const seriesSelect = $('#seriesSelect');

                if (searchType === 'series') {
                    searchTermInput.addClass('d-none');
                    seriesSelect.removeClass('d-none');

                    try {
                        const response = await HTTPService.get(`/api/dashboard/series/used-series`);
                        const series = await response;
                        seriesSelect.empty().append('<option value="">Seleccione una serie</option>');
                        series.forEach(s => {
                            seriesSelect.append(`<option value="${s.series_id}">${s.code} - ${s.name}</option>`);
                        });
                    } catch (error) {
                        console.error('Error fetching series:', error);
                        await Swal.fire('Error', 'Error al cargar las series', 'error');
                    }
                } else {
                    searchTermInput.removeClass('d-none');
                    seriesSelect.addClass('d-none');
                }

                updateSearchPlaceholder();
            };

            const updateSearchPlaceholder = () => {
                const searchType = $('#searchType').val();
                const searchInput = $('#searchTerm');
                const placeholders = {
                    'box': 'Ingrese número de caja',
                    'series': 'Seleccione una serie',
                    'year': 'Ingrese año'
                };
                searchInput.attr('placeholder', placeholders[searchType]);
            };

            const searchDocuments = async () => {
                const searchType = $('#searchType').val();
                let searchTerm;

                if (searchType === 'series') {
                    searchTerm = $('#seriesSelect').val();
                } else {
                    searchTerm = $('#searchTerm').val().trim();
                }

                if (!searchTerm) {
                    await Swal.fire('Atención', 'Por favor ingrese un término de búsqueda', 'warning');
                    return;
                }

                try {
                    Swal.showLoading();
                    if (searchType === 'box') {
                        const response = await HTTPService.get(`/api/dashboard/search-by-box/${searchTerm}`);
                        if (response) {
                            updateTable(response, searchType);
                        } else {
                            updateTable([], searchType);
                        }
                    } else if (searchType === 'series') {
                        const response = await HTTPService.get(`/api/dashboard/search-by-serial/${searchTerm}`);
                        if (response) {
                            updateTable(response, searchType);
                        } else {
                            updateTable([], searchType);
                        }
                    } else {
                        const response = await HTTPService.get(`/api/dashboard/search-by-year/${searchTerm}`);
                        if (response) {
                            updateTable(response, searchType);
                        } else {
                            updateTable([], searchType);
                        }
                    }
                    Swal.close();
                } catch (error) {
                    console.error('Error al buscar documentos:', error);
                    await Swal.fire('Error', 'Error al realizar la búsqueda', 'error');
                }
            };

            const updateTable = (documents, searchType) => {
                const tbody = document.querySelector('#resultsTable tbody');


                tbody.innerHTML = '';

                if (!documents || !documents.length) {
                    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center">No se encontraron resultados</td>
        </tr>
    `;
                    return;
                }

                // Update table headers based on search type
                const thead = document.querySelector('#resultsTable thead tr');
                if (searchType === 'series' || searchType === 'year') {
                    thead.innerHTML = `
        <th scope="col">Código</th>
        <th scope="col">Serie Documental</th>
        <th scope="col">Sub Serie Documental</th>
        <th scope="col">Referencia Documental</th>
        <th scope="col">Estante</th>
        <th scope="col">Caja</th>
        <th scope="col">Año</th>
    `;
                } else {
                    // Default box view
                    thead.innerHTML = `
        <th scope="col">Código</th>
        <th scope="col">Serie y Sub Serie</th>
        <th scope="col">Referencia Documental</th>
        <th scope="col">Tercero(s)</th>
        <th scope="col">Estante</th>
        <th scope="col">Caja</th>
        <th scope="col">Orden</th>
        <th scope="col">N° Folio</th>
        <th scope="col">Año</th>
    `;
                }

                documents.forEach(doc => {
                    let row = '';
                    if (searchType === 'series' || searchType === 'year') {
                        row = `
            <tr>
                <td>${doc.filed || ''}</td>
                <td>${doc.series?.series_entity?.series_name || ''}</td>
                <td>${doc.subseries?.subseries_name || ''}</td>
                <td>${doc.document_reference || ''}</td>
                <td>${doc.shelf_number || ''}</td>
                <td>${doc.box_number || ''}</td>
                <td>${doc.folder_year || ''}</td>
            </tr>
        `;
                    } else {
                        // Default box view
                        row = `
            <tr>
                <td>${doc.filed || ''}</td>
                <td>${doc.series?.series_code || ''} ${doc.series?.series_entity?.series_name || ''} ${doc.subseries?.subseries_name || ''}</td>
                <td>${doc.document_reference || ''}</td>
                <td>${doc.third_parties || ''}</td>
                <td>${doc.shelf_number || ''}</td>
                <td>${doc.box_number || ''}</td>
                <td>${doc.ord_number || ''}</td>
                <td>${doc.folio_number || ''}</td>
                <td>${doc.folder_year || ''}</td>
            </tr>
        `;
                    }
                    tbody.innerHTML += row;
                });
            };

            // Initialize when DOM is ready
            $(document).ready(() => {
                fetchDocuments();

                // Add event listener for search type change
                $('#searchType').on('change', updateSearchForm);

                // Add event listener for the Enter key in the search input field
                $('#searchTerm, #seriesSelect').on('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchDocuments();
                    }
                });
            });

            // Create public API
            const publicApi = {
                toggleDocument,
                showSearchModal,
                downloadPDF,
                showUpdateModal,
                showSigns,
                showDeleteModal,
                deleteDocument,
                updateDocument,
                openSearchModal,
                searchDocuments,
                updateTable,
                updateSearchForm
            };

            // Expose to window object for inline event handlers
            window.DocumentManagement = publicApi;

            // Return public API for module imports
            return publicApi;
        })();


    </script>
@endsection
