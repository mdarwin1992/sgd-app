# HTTPService y DocumentManagement - Servicio HTTP Avanzado para JavaScript

Este módulo proporciona una capa de servicio HTTP robusta y versátil para aplicaciones JavaScript, ofreciendo una amplia gama de funcionalidades para gestionar peticiones HTTP, autenticación, manejo de errores, creación de componentes dinámicos y ahora, gestión de documentos.

## Características

- Gestión automática de tokens de autenticación
- Protección CSRF
- Sistema de cola para peticiones con límite de concurrencia
- Manejo de errores mejorado y detallado
- Carga de archivos con validación de tamaño
- Sistema de creación de componentes con reactividad
- Detección automática de entorno (producción/desarrollo)
- Soporte para todos los métodos HTTP estándar
- Inicialización automática para Laravel Sanctum
- Nuevo sistema de gestión de documentos (DocumentManagement)

## Instalación

Incluye el archivo `HTTPService.js` en tu proyecto:

```html
<script src="ruta/hacia/HTTPService.js"></script>
```

## Uso Detallado

### 1. Realizar Peticiones HTTP

HTTPService soporta todos los métodos HTTP estándar:

```javascript
// GET
HTTPService.get('/api/usuarios')
  .then(respuesta => console.log(respuesta))
  .catch(error => console.error(error));

// POST
HTTPService.post('/api/usuarios', {
  nombre: 'Ana',
  email: 'ana@ejemplo.com'
});

// PUT
HTTPService.put('/api/usuarios/1', {
  nombre: 'Ana Actualizada'
});

// DELETE
HTTPService.delete('/api/usuarios/1');

// PATCH
HTTPService.patch('/api/usuarios/1', {
  estado: 'inactivo'
});

// HEAD
HTTPService.head('/api/estado');

// OPTIONS
HTTPService.options('/api/opciones');
```

### 2. Gestión de Tokens

El servicio maneja automáticamente los tokens de autenticación:

```javascript
// Obtener el token actual
const token = HTTPService.getToken();

// El token se incluye automáticamente en las peticiones
HTTPService.get('/api/datos-protegidos');
```

### 3. Protección CSRF

Los tokens CSRF se gestionan automáticamente:

```javascript
// No es necesario añadir manualmente el token CSRF
HTTPService.post('/api/accion', datos);
```

### 4. Carga de Archivos

```javascript
const inputArchivo = document.querySelector('input[type="file"]');
inputArchivo.addEventListener('change', (e) => {
  HTTPService.upload('/api/subir', e)
    .then(respuesta => console.log('Archivo subido:', respuesta))
    .catch(error => console.error('Error:', error));
});
```

### 5. Sistema de Cola para Peticiones

Las peticiones se encolan automáticamente, limitando a 5 peticiones concurrentes:

```javascript
// Estas peticiones se encolarán automáticamente si hay más de 5 en curso
for (let i = 0; i < 10; i++) {
  HTTPService.get(`/api/recurso/${i}`);
}
```

### 6. Manejo de Errores

```javascript
HTTPService.get('/api/datos-inexistentes')
  .catch(error => {
    console.error('Tipo de error:', error.message);
    console.error('Detalles:', error.details);
  });
```

### 7. Creación de Componentes Reactivos

```javascript
const contadorComponente = HTTPService.createComponent({
  data: {
    contador: 0,
    visible: true
  },
  methods: {
    incrementar() {
      this.setData({ contador: this.data.contador + 1 });
    },
    toggleVisibilidad() {
      this.setData({ visible: !this.data.visible });
    }
  },
  computed: {
    contadorDoble() {
      return this.data.contador * 2;
    }
  },
  elements: {
    contadorElemento: '#contador',
    botonIncrementar: '#incrementar',
    botonToggle: '#toggle'
  },
  events: {
    'botonIncrementar:click': 'incrementar',
    'botonToggle:click': 'toggleVisibilidad'
  },
  render() {
    this.elements.contadorElemento.textContent = this.data.contador;
    this.elements.contadorElemento.style.display = this.data.visible ? 'block' : 'none';
    console.log(`Contador doble: ${this.contadorDoble}`);
  }
});

contadorComponente.init();
```

### 8. Inicialización de Sanctum

Para proyectos que utilizan Laravel Sanctum:

```javascript
HTTPService.initializeSanctum()
  .then(() => console.log('Sanctum inicializado'))
  .catch(error => console.error('Error al inicializar Sanctum:', error));
```

### 9. Gestión de Documentos con DocumentManagement

El nuevo sistema DocumentManagement permite realizar operaciones CRUD sobre documentos de manera eficiente y segura, integrándose perfectamente con HTTPService.

#### Inicialización

```javascript
const DocumentManagement = (() => {
  // La implementación se integra con HTTPService
  return {
    create: async (document) => {
      return await HTTPService.post('/api/documents', document);
    },
    read: async (id) => {
      return await HTTPService.get(`/api/documents/${id}`);
    },
    update: async (id, updates) => {
      return await HTTPService.put(`/api/documents/${id}`, updates);
    },
    delete: async (id) => {
      return await HTTPService.delete(`/api/documents/${id}`);
    }
  };
})();
```

#### Operaciones CRUD

##### Crear un documento

```javascript
const nuevoDocumento = {
  titulo: 'Informe Anual',
  contenido: 'Este es el contenido del informe anual...',
  autor: 'Juan Pérez'
};

DocumentManagement.create(nuevoDocumento)
  .then(documentoCreado => console.log('Documento creado con ID:', documentoCreado.id))
  .catch(error => console.error('No se pudo crear el documento:', error));
```

##### Leer un documento

```javascript
DocumentManagement.read(1)
  .then(documento => console.log('Contenido del documento:', documento.contenido))
  .catch(error => console.error('No se pudo leer el documento:', error));
```

##### Actualizar un documento

```javascript
const actualizaciones = {
  titulo: 'Informe Anual Revisado',
  contenido: 'Contenido actualizado del informe anual...'
};

DocumentManagement.update(1, actualizaciones)
  .then(documentoActualizado => console.log('Documento actualizado:', documentoActualizado))
  .catch(error => console.error('No se pudo actualizar el documento:', error));
```

##### Eliminar un documento

```javascript
DocumentManagement.delete(1)
  .then(resultado => console.log('Documento eliminado con éxito:', resultado))
  .catch(error => console.error('No se pudo eliminar el documento:', error));
```

#### Integración con componentes

DocumentManagement se integra perfectamente con los componentes creados por HTTPService:

```javascript
const documentoComponente = HTTPService.createComponent({
  data: {
    documentos: [],
    documentoActual: null
  },
  methods: {
    cargarDocumentos() {
      HTTPService.get('/api/documents')
        .then(documentos => this.setData({ documentos }))
        .catch(error => console.error('Error al cargar documentos:', error));
    },
    crearDocumento(documento) {
      DocumentManagement.create(documento)
        .then(nuevoDocumento => {
          this.setData({
            documentos: [...this.data.documentos, nuevoDocumento]
          });
        })
        .catch(error => console.error('Error al crear documento:', error));
    },
    actualizarDocumento(id, actualizaciones) {
      DocumentManagement.update(id, actualizaciones)
        .then(documentoActualizado => {
          const documentosActualizados = this.data.documentos.map(doc =>
            doc.id === id ? documentoActualizado : doc
          );
          this.setData({ documentos: documentosActualizados });
        })
        .catch(error => console.error('Error al actualizar documento:', error));
    },
    eliminarDocumento(id) {
      DocumentManagement.delete(id)
        .then(() => {
          const documentosRestantes = this.data.documentos.filter(doc => doc.id !== id);
          this.setData({ documentos: documentosRestantes });
        })
        .catch(error => console.error('Error al eliminar documento:', error));
    }
  },
  created() {
    this.cargarDocumentos();
  },
  render() {
    console.log('Documentos actuales:', this.data.documentos);
    // Aquí iría la lógica de renderizado de los documentos
  }
});

documentoComponente.init();
```

## Configuración Avanzada

### URL Base Dinámica

El servicio detecta automáticamente el entorno:

```javascript
console.log(HTTPService.baseURL);
// En desarrollo: 'http://127.0.0.1:8000'
// En producción: 'http://www.sgd.local'
```

### Personalización de Límites

```javascript
// Cambiar el límite de tamaño para archivos (por defecto 250MB)
const IMAGE_SIZE_DOCUMENT = 100 * 1024 * 1024; // 100MB
```

## Consideraciones de Seguridad

- Verificación automática de la expiración de tokens
- Validación de respuestas JSON
- Límite de tamaño en la carga de archivos
- Inclusión automática de tokens CSRF en todas las peticiones
- Asegúrate de implementar la autorización adecuada en el servidor para las operaciones CRUD de documentos
- Valida y sanitiza todos los datos de entrada en las operaciones de creación y actualización de documentos

## Depuración

El servicio incluye logs detallados para facilitar la depuración:

```javascript
// Los errores se registran automáticamente en la consola
HTTPService.get('/api/ruta-problematica')
  .catch(error => {
    // Información detallada del error disponible en la consola
  });

// Depuración de operaciones de DocumentManagement
DocumentManagement.create(documento)
  .catch(error => {
    console.error('Error detallado al crear documento:', error);
    // Aquí puedes agregar lógica adicional de depuración
  });
```

## Contribución

Si encuentras algún error o tienes sugerencias de mejora, por favor abre un issue o envía un pull request en nuestro repositorio.

## Licencia

Este proyecto está bajo la Licencia MIT.
