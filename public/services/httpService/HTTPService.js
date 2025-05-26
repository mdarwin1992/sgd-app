const HTTPService = (() => {
    // Constantes para headers HTTP
    const CONTENT_TYPE = 'Content-Type';
    const AUTHORIZATION = 'Authorization';
    const ACCEPT = 'Accept';
    const BEARER = 'Bearer';
    const X_XSRF_TOKEN = 'X-XSRF-TOKEN';

    // Constants for file upload
    const FILE_CONSTRAINTS = {
        MAX_SIZE: 262144000, // 250MB in bytes
        ALLOWED_TYPES: ['image/*', 'application/pdf', '.doc', '.docx', '.xls', '.xlsx'] // Add more as needed
    };

    // Verificar si estamos en producción o desarrollo
    const isProduction = window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1';

    // Obtener token desde localStorage con verificación de expiración
    const getToken = () => {
        const token = localStorage.getItem('token');
        const expiry = localStorage.getItem('expirationTime');
        if (!token || !expiry || Date.now() > parseInt(expiry)) {
            localStorage.removeItem('token');
            localStorage.removeItem('expirationTime');
            return null;
        }
        return token;
    };

    // Obtener y verificar entity_id desde localStorage
    const getEntityId = () => {
        const entityId = localStorage.getItem('entity_id');
        if (!entityId) {
            console.warn('entity_id not found in localStorage');
            return null;
        }
        return entityId;
    };

    // Obtener XSRF token de las cookies
    const getXSRFToken = () => {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'XSRF-TOKEN') {
                return decodeURIComponent(value);
            }
        }
        return null;
    };

    // Construir los headers de la petición
    const getHeaders = () => ({
        [CONTENT_TYPE]: 'application/json',
        [ACCEPT]: 'application/json', ...(getToken() && {[AUTHORIZATION]: `${BEARER} ${getToken()}`}),
        [X_XSRF_TOKEN]: getXSRFToken()
    });

    // Sistema de cola para peticiones
    const queue = [];
    const maxConcurrent = 5;
    let runningRequests = 0;

    const enqueueRequest = (method, path, data) => {
        return new Promise((resolve, reject) => {
            queue.push({method, path, data, resolve, reject});
            processQueue();
        });
    };

    const processQueue = () => {
        if (runningRequests >= maxConcurrent || queue.length === 0) return;

        runningRequests++;
        const {method, path, data, resolve, reject} = queue.shift();

        request(method, path, data)
            .then(resolve)
            .catch(reject)
            .finally(() => {
                runningRequests--;
                processQueue();
            });
    };

    // Manejo de errores mejorado
    const handleError = (error, method, path, data) => {
        console.error('Error en la petición HTTP:', error);
        console.error('Detalles de la solicitud:', {method, path, data});
        throw new Error(error.message === 'Failed to fetch' ? 'Error de red o CORS' : `Error HTTP: ${error.message}`);
    };

    // Método común para todas las peticiones HTTP
    const request = async (method, path, data = null) => {
        const options = {
            method, headers: getHeaders(), credentials: 'include', // Necesario para Sanctum
            ...(data && {body: JSON.stringify(data)})
        };

        try {
            const url = new URL(path, HTTPService.baseURL).toString();
            const response = await fetch(url, options);

            let responseData;
            const responseText = await response.text();
            try {
                responseData = JSON.parse(responseText);
            } catch (parseError) {
                console.error('Respuesta no es JSON válido:', responseText);
                throw new Error(`La respuesta no es JSON válido: ${parseError.message}`);
            }

            if (!response.ok) {
                console.error('Respuesta de error:', responseData);
                throw new Error(`Error HTTP! estado: ${response.status}, mensaje: ${responseData.message || 'Sin mensaje'}`);
            }

            return responseData;
        } catch (error) {
            return handleError(error, method, path, data);
        }
    };

    // CRUD simplificado
    const HTTP_METHODS = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];
    const httpMethodsMap = HTTP_METHODS.reduce((acc, method) => ({
        ...acc, [method.toLowerCase()]: (path, data) => enqueueRequest(method, path, data)
    }), {});

    // Inicialización de Sanctum
    const initializeSanctum = async () => {
        try {
            await HTTPService.get('/sanctum/csrf-cookie');
        } catch (error) {
            console.error('Error al inicializar Sanctum:', error);
        }
    };

    // Función de carga de archivos
    const upload = async (url, event) => {
        try {
            // Validate if we have a file
            if (!event?.target?.files?.[0]) {
                throw new Error('No se ha seleccionado ningún archivo');
            }

            const file = event.target.files[0];

            // Validate file size
            if (file.size > FILE_CONSTRAINTS.MAX_SIZE) {
                throw new Error(`El archivo excede el tamaño máximo permitido de ${FILE_CONSTRAINTS.MAX_SIZE / 1048576}MB`);
            }

            // Create FormData properly
            const formData = new FormData();
            formData.append('filepath', file);

            // If there's a form with ID 'AddForm', append other form data
            const form = document.getElementById('AddForm');
            if (form) {
                const formFields = new FormData(form);
                for (let [key, value] of formFields.entries()) {
                    if (key !== 'filepath') { // Avoid duplicate file field
                        formData.append(key, value);
                    }
                }
            }

            // Get the base headers but remove Content-Type to let browser set it
            const headers = getHeaders();
            delete headers['Content-Type']; // Let browser set correct multipart boundary

            const response = await fetch(new URL(url, HTTPService.baseURL).toString(), {
                method: 'POST', headers: {
                    ...headers, 'Accept': 'application/json', // Let browser set Content-Type with boundary
                }, credentials: 'include', body: formData
            });

            // Get response text first
            const responseText = await response.text();

            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Response is not valid JSON:', responseText);
                throw new Error('La respuesta del servidor no es válida');
            }

            // Check for error responses
            if (!response.ok) {
                const errorMessage = data.message || `Error del servidor: ${response.status}`;
                console.error('Upload error response:', data);
                throw new Error(errorMessage);
            }

            // Return data or throw error if unexpected response format
            if (data.data) {
                return data.data;
            } else if (data.url) {
                return data.url;
            } else {
                console.error('Unexpected response format:', data);
                throw new Error('Formato de respuesta inesperado del servidor');
            }

        } catch (error) {
            // Log the complete error for debugging
            console.error('Error detallado en la carga del archivo:', {
                message: error.message, stack: error.stack, originalError: error
            });

            // Throw a user-friendly error
            throw new Error(`Error al cargar el archivo: ${error.message}`);
        }
    };

    // En HTTPService.js
    const postPdf = async (url, data, config = {}) => {
        try {
            const csrfToken = getXSRFToken(); // Reuse the existing XSRF token getter

            const response = await fetch(new URL(url, HTTPService.baseURL).toString(), {
                method: 'POST', body: JSON.stringify(data), headers: {
                    ...getHeaders(), // Include all default headers
                    'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest'
                }, credentials: 'include', ...config
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // If we expect a blob response, return it directly
            if (config.responseType === 'blob') {
                return response.blob();
            }

            // For other response types, try to parse as JSON
            return response.json();
        } catch (error) {
            return handleError(error, 'POST', url, data);
        }
    };

    return Object.freeze({
        baseURL: isProduction ? 'http://www.sgd.local' : 'http://127.0.0.1:8000',
        getToken,
        getEntityId,
        initializeSanctum,
        postPdf,
        upload, ...httpMethodsMap
    });
})();

export default HTTPService;
