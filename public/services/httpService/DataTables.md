# Integración de HTTPService con DataTables

## Configuración Básica

### 1. HTML Base
Primero, necesitas tener la estructura HTML básica para tu DataTable:

```html
<!-- Incluir CSS necesarios -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- Estructura de la tabla -->
<table id="miTabla" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <!-- Los datos se cargarán dinámicamente -->
    </tbody>
</table>

<!-- Scripts necesarios -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
```

### 2. Implementación Básica con HTTPService

```javascript
// Ejemplo de implementación básica
const inicializarTabla = () => {
    const tabla = $('#miTabla').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/usuarios',
            type: 'GET',
            beforeSend: function(request) {
                // Obtener headers del HTTPService
                const headers = HTTPService.getHeaders();
                Object.keys(headers).forEach(key => {
                    request.setRequestHeader(key, headers[key]);
                });
            },
            data: function(params) {
                // Transformar parámetros de DataTables al formato de tu API
                return {
                    page: (params.start / params.length) + 1,
                    per_page: params.length,
                    search: params.search.value,
                    order_by: params.columns[params.order[0].column].data,
                    order_direction: params.order[0].dir
                };
            },
            dataFilter: function(response) {
                // Transformar la respuesta al formato que espera DataTables
                const json = JSON.parse(response);
                return JSON.stringify({
                    draw: json.draw,
                    recordsTotal: json.total,
                    recordsFiltered: json.filtered,
                    data: json.data
                });
            }
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'email' },
            { data: 'fecha' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button onclick="editarRegistro(${row.id})" class="btn btn-sm btn-primary">
                            Editar
                        </button>
                        <button onclick="eliminarRegistro(${row.id})" class="btn btn-sm btn-danger">
                            Eliminar
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    return tabla;
};
```

### 3. Funciones CRUD con HTTPService

```javascript
// Función para cargar datos
const cargarDatos = async () => {
    try {
        const tabla = $('#miTabla').DataTable();
        tabla.ajax.reload();
    } catch (error) {
        console.error('Error al cargar datos:', error);
        mostrarError('Error al cargar los datos');
    }
};

// Función para crear nuevo registro
const crearRegistro = async (datos) => {
    try {
        await HTTPService.post('/api/usuarios', datos);
        $('#miTabla').DataTable().ajax.reload();
        mostrarExito('Registro creado exitosamente');
    } catch (error) {
        console.error('Error al crear registro:', error);
        mostrarError('Error al crear el registro');
    }
};

// Función para editar registro
const editarRegistro = async (id) => {
    try {
        // Primero obtener los datos actuales
        const datos = await HTTPService.get(`/api/usuarios/${id}`);
        
        // Aquí podrías mostrar un modal con los datos para editar
        mostrarModalEdicion(datos);
    } catch (error) {
        console.error('Error al obtener datos para edición:', error);
        mostrarError('Error al cargar los datos para editar');
    }
};

// Función para actualizar registro
const actualizarRegistro = async (id, datos) => {
    try {
        await HTTPService.put(`/api/usuarios/${id}`, datos);
        $('#miTabla').DataTable().ajax.reload();
        mostrarExito('Registro actualizado exitosamente');
    } catch (error) {
        console.error('Error al actualizar registro:', error);
        mostrarError('Error al actualizar el registro');
    }
};

// Función para eliminar registro
const eliminarRegistro = async (id) => {
    if (confirm('¿Está seguro de eliminar este registro?')) {
        try {
            await HTTPService.delete(`/api/usuarios/${id}`);
            $('#miTabla').DataTable().ajax.reload();
            mostrarExito('Registro eliminado exitosamente');
        } catch (error) {
            console.error('Error al eliminar registro:', error);
            mostrarError('Error al eliminar el registro');
        }
    }
};
```

### 4. Ejemplo con Búsqueda y Filtros Personalizados

```javascript
const inicializarTablaConFiltros = () => {
    // Elementos de filtro
    const filtroFecha = $('#filtroFecha');
    const filtroEstado = $('#filtroEstado');

    const tabla = $('#miTabla').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/usuarios',
            type: 'GET',
            beforeSend: function(request) {
                const headers = HTTPService.getHeaders();
                Object.keys(headers).forEach(key => {
                    request.setRequestHeader(key, headers[key]);
                });
            },
            data: function(params) {
                // Agregar filtros personalizados
                return {
                    page: (params.start / params.length) + 1,
                    per_page: params.length,
                    search: params.search.value,
                    order_by: params.columns[params.order[0].column].data,
                    order_direction: params.order[0].dir,
                    fecha: filtroFecha.val(),
                    estado: filtroEstado.val()
                };
            }
        },
        // ... resto de la configuración ...
    });

    // Eventos de filtros
    filtroFecha.on('change', () => tabla.ajax.reload());
    filtroEstado.on('change', () => tabla.ajax.reload());

    return tabla;
};
```

### 5. Ejemplo con Exportación de Datos

```javascript
const exportarDatos = async (formato) => {
    try {
        // Obtener parámetros actuales de la tabla
        const tabla = $('#miTabla').DataTable();
        const params = tabla.ajax.params();

        // Realizar petición de exportación
        const response = await HTTPService.post('/api/exportar', {
            formato: formato,
            filtros: {
                search: params.search.value,
                order_by: params.order_by,
                order_direction: params.order_direction
            }
        }, {
            responseType: 'blob' // Para descargar archivos
        });

        // Crear link de descarga
        const url = window.URL.createObjectURL(new Blob([response]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `exportacion.${formato}`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Error al exportar datos:', error);
        mostrarError('Error al exportar los datos');
    }
};
```

### 6. Componente Reutilizable

```javascript
// Crear un componente reutilizable con HTTPService
const TablaComponent = HTTPService.createComponent({
    data: {
        tabla: null,
        filtros: {
            fecha: '',
            estado: ''
        }
    },
    methods: {
        async inicializar() {
            this.tabla = this.inicializarTabla();
            this.bindEventos();
        },
        
        bindEventos() {
            // Bind de eventos para filtros
            this.elements.filtros.forEach(filtro => {
                filtro.addEventListener('change', () => this.actualizarFiltros());
            });
        },

        actualizarFiltros() {
            this.data.filtros = {
                fecha: this.elements.filtroFecha.value,
                estado: this.elements.filtroEstado.value
            };
            this.tabla.ajax.reload();
        },

        async exportar(formato) {
            await exportarDatos(formato);
        }
    },
    elements: {
        tabla: '#miTabla',
        filtroFecha: '#filtroFecha',
        filtroEstado: '#filtroEstado',
        filtros: '.filtro'
    },
    created() {
        this.inicializar();
    }
});
```

### 7. Manejo de Errores y Loading

```javascript
// Funciones auxiliares para UI
const mostrarLoading = () => {
    // Mostrar spinner o indicador de carga
    $('#loadingIndicator').show();
};

const ocultarLoading = () => {
    $('#loadingIndicator').hide();
};

const mostrarError = (mensaje) => {
    // Mostrar mensaje de error usando tu sistema de notificaciones
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
};

const mostrarExito = (mensaje) => {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje
    });
};

// Configuración de eventos globales de DataTables
$.fn.dataTable.ext.errMode = 'none';
$(document).on('error.dt', function(e, settings, techNote, message) {
    console.error('Error en DataTable:', message);
    mostrarError('Error al procesar los datos de la tabla');
});
```

## Ejemplos de Uso Común

### Tabla con Búsqueda y Filtros

```javascript
// HTML
<div class="filtros-container">
    <input type="date" id="filtroFecha" class="filtro">
    <select id="filtroEstado" class="filtro">
        <option value="">Todos</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    </select>
</div>

<table id="miTabla">
    <!-- ... estructura de la tabla ... -->
</table>

// JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const tabla = inicializarTablaConFiltros();
    
    // Agregar botones de exportación
    document.getElementById('btnExportarExcel').addEventListener('click', () => {
        exportarDatos('xlsx');
    });
    
    document.getElementById('btnExportarPDF').addEventListener('click', () => {
        exportarDatos('pdf');
    });
});
```

Este ejemplo proporciona una base sólida para trabajar con DataTables y HTTPService. Puedes adaptarlo según tus necesidades específicas, agregando más funcionalidades o modificando el comportamiento existente.
