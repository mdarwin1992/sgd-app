// config.js (Archivo de configuración simulado)
// En una aplicación real, esto provendría de variables de entorno
const APP_CONFIG = {
    API_BASE_URL: window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
        ? 'http://127.0.0.1:8000'
        : 'http://www.sgd.local',
    ACCESS_TOKEN_LIFETIME_SECONDS: 1800, // Token de acceso de corta duración (5 minutos)
    // Otros valores de configuración...
};

/**
 * @fileoverview Servicio HTTP para manejar todas las peticiones a la API, incluyendo autenticación y autorización.
 */

const HTTPService = (() => {
    // --- Constantes y Configuración ---
    const CONTENT_TYPE = 'Content-Type';
    const AUTHORIZATION = 'Authorization';
    const ACCEPT = 'Accept';
    const BEARER = 'Bearer';
    const X_XSRF_TOKEN = 'X-XSRF-TOKEN';
    const X_REQUESTED_WITH = 'X-Requested-With';

    const API_BASE_URL = APP_CONFIG.API_BASE_URL;
    const ACCESS_TOKEN_LIFETIME = APP_CONFIG.ACCESS_TOKEN_LIFETIME_SECONDS;

    const FILE_CONSTRAINTS = {
        MAX_SIZE: 262144000, // 250MB in bytes
        ALLOWED_TYPES: ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ALLOWED_EXTENSIONS: ['.jpg', '.jpeg', '.png', '.pdf', '.doc', '.docx', '.xls', '.xlsx']
    };

    // --- Clases de Errores Personalizadas ---

    /**
     * @class HttpError
     * @extends Error
     * @description Error específico para respuestas HTTP no exitosas.
     */
    class HttpError extends Error {
        /**
         * Crea una instancia de HttpError.
         * @param {string} message - El mensaje de error.
         * @param {number} status - El código de estado HTTP.
         * @param {Object} data - Los datos de la respuesta de error (si están disponibles).
         */
        constructor(message, status, data = null) {
            super(message);
            this.name = 'HttpError';
            this.status = status;
            this.data = data;
        }
    }

    /**
     * @class NetworkError
     * @extends Error
     * @description Error específico para problemas de red o CORS.
     */
    class NetworkError extends Error {
        constructor(message = 'Error de red o CORS. Por favor, verifica tu conexión a internet.') {
            super(message);
            this.name = 'NetworkError';
        }
    }

    // --- Funciones de Utilidad y Autenticación/Autorización ---

    /**
     * Obtiene el token de autenticación del localStorage y verifica su expiración.
     * @returns {string|null} El token si es válido y no ha expirado, de lo contrario null.
     */
    const getToken = () => {
        const token = localStorage.getItem('access_token');
        const expiry = localStorage.getItem('access_token_expirationTime');
        if (!token || !expiry || Date.now() > parseInt(expiry, 10)) {
            // El token ha expirado o no existe, limpia y retorna null
            localStorage.removeItem('access_token');
            localStorage.removeItem('access_token_expirationTime');
            localStorage.removeItem('user_data'); // Limpiar datos de usuario también
            console.warn('Token de acceso expirado o no encontrado.');
            return null;
        }
        return token;
    };

    /**
     * Establece el token de autenticación y su tiempo de expiración en localStorage.
     * @param {string} token - El token de autenticación.
     * @param {number} expiresInSeconds - El tiempo de expiración en segundos.
     */
    const setToken = (token, expiresInSeconds) => {
        const expirationTime = Date.now() + expiresInSeconds * 1000; // Convertir segundos a milisegundos
        localStorage.setItem('access_token', token);
        localStorage.setItem('access_token_expirationTime', expirationTime.toString());
    };

    /**
     * Almacena los datos del usuario (incluyendo roles y permisos) en localStorage.
     * @param {Object} userData - Objeto con la información del usuario, roles y permisos.
     * @property {Array<string>} [userData.roles] - Roles del usuario.
     * @property {Array<string>} [userData.permissions] - Permisos del usuario.
     */
    const setUserData = (userData) => {
        try {
            localStorage.setItem('user_data', JSON.stringify(userData));
        } catch (e) {
            console.error('Error al guardar datos de usuario en localStorage:', e);
        }
    };

    /**
     * Obtiene los datos del usuario almacenados en localStorage.
     * @returns {Object|null} Los datos del usuario o null si no se encuentran.
     */
    const getUserData = () => {
        try {
            const userData = localStorage.getItem('user_data');
            return userData ? JSON.parse(userData) : null;
        } catch (e) {
            console.error('Error al parsear datos de usuario de localStorage:', e);
            return null;
        }
    };

    /**
     * Verifica si el usuario actual tiene un rol específico.
     * @param {string} role - El nombre del rol a verificar.
     * @returns {boolean} True si el usuario tiene el rol, de lo contrario False.
     */
    const hasRole = (role) => {
        const userData = getUserData();
        return userData && Array.isArray(userData.roles) && userData.roles.includes(role);
    };

    /**
     * Verifica si el usuario actual tiene un permiso específico.
     * @param {string} permission - El nombre del permiso a verificar.
     * @returns {boolean} True si el usuario tiene el permiso, de lo contrario False.
     */
    const hasPermission = (permission) => {
        const userData = getUserData();
        return userData && Array.isArray(userData.permissions) && userData.permissions.includes(permission);
    };

    /**
     * Obtiene y verifica entity_id desde localStorage.
     * @returns {string|null} El entity_id si existe, de lo contrario null.
     */
    const getEntityId = () => {
        const entityId = localStorage.getItem('entity_id');
        if (!entityId) {
            console.warn('entity_id no encontrado en localStorage');
            return null;
        }
        return entityId;
    };

    /**
     * Obtiene el XSRF token de las cookies.
     * @returns {string|null} El token XSRF si se encuentra, de lo contrario null.
     */
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

    /**
     * Construye los headers de la petición.
     * @param {boolean} isMultipart - Indica si la petición es multipart/form-data.
     * @returns {Object} Los headers de la petición.
     */
    const getHeaders = (isMultipart = false) => {
        const headers = {
            [ACCEPT]: 'application/json',
            [X_XSRF_TOKEN]: getXSRFToken() || '',
            [X_REQUESTED_WITH]: 'XMLHttpRequest'
        };

        const currentToken = getToken();
        if (currentToken) {
            headers[AUTHORIZATION] = `${BEARER} ${currentToken}`;
        }

        if (!isMultipart) {
            headers[CONTENT_TYPE] = 'application/json';
        }

        return headers;
    };

    // --- Sistema de Cola para Peticiones ---
    const queue = [];
    const maxConcurrent = 5;
    let runningRequests = 0;

    /**
     * Añade una petición a la cola para su procesamiento.
     * @param {string} method - El método HTTP (GET, POST, etc.).
     * @param {string} path - La ruta de la URL.
     * @param {Object} [data=null] - Los datos a enviar en el cuerpo de la petición.
     * @param {Object} [customOptions={}] - Opciones personalizadas para la petición fetch.
     * @returns {Promise<Object>} Una promesa que se resuelve con los datos de la respuesta.
     */
    const enqueueRequest = (method, path, data, customOptions) => {
        return new Promise((resolve, reject) => {
            const controller = new AbortController();
            queue.push({ method, path, data, resolve, reject, customOptions, controller });
            processQueue();
        });
    };

    /**
     * Procesa la cola de peticiones, limitando la concurrencia.
     */
    const processQueue = () => {
        if (runningRequests >= maxConcurrent || queue.length === 0) return;

        runningRequests++;
        const { method, path, data, resolve, reject, customOptions, controller } = queue.shift();

        request(method, path, data, customOptions, controller.signal)
            .then(resolve)
            .catch(reject)
            .finally(() => {
                runningRequests--;
                processQueue();
            });
    };

    /**
     * Cancela una petición pendiente en la cola.
     * @param {string} path - La ruta de la petición a cancelar.
     * @returns {boolean} True si la petición fue encontrada y cancelada, false de lo contrario.
     */
    const cancelRequest = (path) => {
        const index = queue.findIndex(req => req.path === path);
        if (index > -1) {
            const { controller } = queue[index];
            controller.abort();
            queue.splice(index, 1);
            console.log(`Petición a ${path} cancelada.`);
            return true;
        }
        return false;
    };

    // --- Manejo de Errores Centralizado ---

    /**
     * Maneja y normaliza los errores de las peticiones HTTP.
     * @param {Error} error - El objeto de error original.
     * @param {string} method - El método HTTP de la petición.
     * @param {string} path - La ruta de la petición.
     * @param {Object} data - Los datos enviados en la petición.
     * @returns {Promise<never>} Una promesa rechazada con un error normalizado.
     */
    const handleError = async (error, method, path, data) => {
        console.error('Error en la petición HTTP:', error);
        console.error('Detalles de la solicitud:', { method, path, data });

        if (error instanceof HttpError) {
            throw error;
        } else if (error.name === 'AbortError') {
            throw new Error('Petición cancelada.');
        } else if (error.message === 'Failed to fetch' || (error.name === 'TypeError' && error.message.includes('Network request failed'))) {
            throw new NetworkError();
        } else if (error.name === 'SyntaxError' && error.message.includes('JSON')) {
            throw new Error(`La respuesta del servidor no es JSON válido: ${error.message}`);
        }
        throw new Error(`Error inesperado en la petición: ${error.message}`);
    };

    // --- Refresco de Token ---
    let isRefreshing = false;
    let refreshSubscribers = [];

    /**
     * Añade un callback a la lista de suscriptores para cuando el token se refresque.
     * @param {Function} callback - La función a llamar cuando el token se refresque.
     */
    const subscribeTokenRefresh = (callback) => {
        refreshSubscribers.push(callback);
    };

    /**
     * Notifica a todos los suscriptores que el token ha sido refrescado.
     * @param {string} newToken - El nuevo token de autenticación.
     */
    const onTokenRefreshed = (newToken) => {
        refreshSubscribers.forEach(callback => callback(newToken));
        refreshSubscribers = [];
    };

    /**
     * Realiza el proceso de refrescar el token de autenticación.
     * ASUME que tu backend gestiona el token de refresco en una cookie HttpOnly.
     * El navegador enviará automáticamente la cookie 'refresh_token'.
     * @returns {Promise<string>} El nuevo token de autenticación (access token).
     * @throws {Error} Si el refresco del token falla.
     */
    const refreshToken = async () => {
        if (isRefreshing) {
            // Si ya hay un proceso de refresco en curso, espera a que termine
            return new Promise(resolve => {
                subscribeTokenRefresh(token => resolve(token));
            });
        }

        isRefreshing = true;
        try {
            console.log('Intentando refrescar token de acceso utilizando el refresh token (HttpOnly cookie)...');
            // La petición al endpoint de refresco NO debe llevar el Authorization header
            // El navegador enviará la cookie 'refresh_token' automáticamente si credentials: 'include'
            const response = await fetch(`${API_BASE_URL}/api/refresh-token`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    [X_XSRF_TOKEN]: getXSRFToken() || '', // Si tu endpoint de refresco lo requiere
                    [X_REQUESTED_WITH]: 'XMLHttpRequest'
                },
                credentials: 'include', // Crucial para que las cookies HttpOnly se envíen
            });

            const data = await response.json();

            if (!response.ok) {
                // Si el backend responde con un error (ej. refresh token inválido/expirado)
                throw new HttpError(data.message || 'Fallo al refrescar token', response.status, data);
            }

            // Asumiendo que el backend devuelve { access_token: '...', expires_in: ..., user: {...} }
            const newAccessToken = data.access_token;
            // Usa data.expires_in si el backend lo envía, de lo contrario, usa la constante
            const newExpiryInSeconds = data.expires_in || ACCESS_TOKEN_LIFETIME;
            const userData = data.user; // Si el backend actualiza/envía datos de usuario con el refresh

            if (!newAccessToken) {
                throw new Error('Respuesta de refresco de token incompleta: No se recibió access_token.');
            }

            setToken(newAccessToken, newExpiryInSeconds);
            if (userData) { // Actualiza datos de usuario si se proporcionan
                setUserData(userData);
            }
            console.log('Token de acceso refrescado exitosamente.');
            onTokenRefreshed(newAccessToken);
            return newAccessToken;
        } catch (error) {
            console.error('Error crítico al refrescar el token. Forzando cierre de sesión:', error);
            localStorage.clear(); // Limpiar todos los tokens y datos de usuario
            // Redirigir al usuario al login. Esto debería ser manejado por el enrutador de tu framework.
            window.location.href = '/login';
            throw error;
        } finally {
            isRefreshing = false;
        }
    };

    // --- Método Común para Todas las Peticiones HTTP ---

    /**
     * Realiza una petición HTTP.
     * @param {string} method - El método HTTP (GET, POST, etc.).
     * @param {string} path - La ruta de la URL.
     * @param {Object} [data=null] - Los datos a enviar en el cuerpo de la petición.
     * @param {Object} [customOptions={}] - Opciones personalizadas para la petición fetch.
     * @param {AbortSignal} [signal=null] - Una señal para abortar la petición.
     * @returns {Promise<Object>} Una promesa que se resuelve con los datos de la respuesta.
     */
    const request = async (method, path, data = null, customOptions = {}, signal = null) => {
        const options = {
            method,
            headers: getHeaders(), // Headers incluyen el access token si existe
            credentials: 'include', // Necesario para Sanctum (cookies, incluyendo HttpOnly para refresco)
            ...(data && { body: JSON.stringify(data) }),
            signal,
            ...customOptions
        };

        const url = new URL(path, API_BASE_URL).toString();

        try {
            let response = await fetch(url, options);

            // Manejo de 401 Unauthorized para refresco de token
            // Se reintenta la petición SÓLO UNA VEZ después del refresh.
            if (response.status === 401 && !options.headers['X-Retry-Auth']) {
                console.warn('401 Unauthorized. Intentando refrescar token de acceso y reintentar la petición...');
                try {
                    const newToken = await refreshToken();
                    const retryOptions = {
                        ...options,
                        headers: {
                            ...getHeaders(), // Obtiene headers con el nuevo token
                            [AUTHORIZATION]: `${BEARER} ${newToken}`, // Asegura el nuevo token
                            'X-Retry-Auth': 'true' // Marca para evitar bucles infinitos en el reintento
                        }
                    };
                    response = await fetch(url, retryOptions);
                } catch (refreshError) {
                    // Si el refresco falla (ej. refresh token expirado/inválido),
                    // la función refreshToken ya habrá redirigido al login.
                    throw refreshError;
                }
            }

            let responseData;
            const responseText = await response.text();
            try {
                responseData = responseText ? JSON.parse(responseText) : {};
            } catch (parseError) {
                if (response.status === 204 || responseText === '') {
                    responseData = { message: 'No Content', status: response.status };
                } else {
                    console.error('Respuesta no es JSON válido:', responseText);
                    throw new SyntaxError(`La respuesta del servidor no es JSON válido: ${parseError.message}`);
                }
            }

            if (!response.ok) {
                console.error('Respuesta de error del servidor:', responseData);
                throw new HttpError(
                    responseData.message || `Error HTTP! estado: ${response.status}`,
                    response.status,
                    responseData
                );
            }

            return responseData;
        } catch (error) {
            return handleError(error, method, path, data);
        }
    };

    // --- CRUD Simplificado y Métodos Específicos ---

    const HTTP_METHODS = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];
    const httpMethodsMap = HTTP_METHODS.reduce((acc, method) => ({
        ...acc,
        /**
         * Realiza una petición HTTP usando el método ${method}.
         * @param {string} path - La ruta de la URL.
         * @param {Object} [data=null] - Los datos a enviar en el cuerpo de la petición (para POST, PUT, PATCH).
         * @param {Object} [customOptions={}] - Opciones personalizadas para la petición fetch.
         * @returns {Promise<Object>} Una promesa que se resuelve con los datos de la respuesta.
         */
        [method.toLowerCase()]: (path, data, customOptions) => enqueueRequest(method, path, data, customOptions)
    }), {});

    /**
     * Inicializa Sanctum obteniendo el CSRF cookie.
     * Debe llamarse al cargar la aplicación para asegurar que el XSRF-TOKEN esté disponible.
     * @returns {Promise<void>}
     */
    const initializeSanctum = async () => {
        try {
            // Petición GET simple para obtener el CSRF cookie
            // No usamos enqueueRequest aquí para evitar un bucle de dependencia,
            // ya que esta es una inicialización crítica.
            await fetch(`${API_BASE_URL}/sanctum/csrf-cookie`, { credentials: 'include' });
            console.log('Sanctum CSRF cookie inicializado.');
        } catch (error) {
            console.error('Error al inicializar Sanctum CSRF cookie:', error);
            // Podrías decidir si esto es un error crítico que requiere mostrar un mensaje al usuario.
        }
    };

    /**
     * Valida el tipo de archivo y la extensión.
     * @param {File} file - El objeto File a validar.
     * @returns {void}
     * @throws {Error} Si el tipo o extensión no es permitido.
     */
    const validateFileType = (file) => {
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        const isAllowedType = FILE_CONSTRAINTS.ALLOWED_TYPES.includes(file.type);
        const isAllowedExtension = FILE_CONSTRAINTS.ALLOWED_EXTENSIONS.includes(fileExtension);

        if (!isAllowedType && !isAllowedExtension) {
            throw new Error(`Tipo de archivo o extensión no permitido: "${file.type || fileExtension}". Extensiones permitidas: ${FILE_CONSTRAINTS.ALLOWED_EXTENSIONS.join(', ')}`);
        }
    };

    /**
     * Carga un archivo a una URL específica, compatible con el backend de Laravel.
     * @param {string} url - La URL del endpoint de carga (ej: '/api/helpers/files/upload').
     * @param {File} file - El objeto File a cargar.
     * @param {Object} [additionalData={}] - Datos adicionales que se añadirán al FormData (ej: { reference_code: '123' }).
     * @returns {Promise<Object>} La respuesta completa del servidor, incluyendo el objeto `data` con la URL y path del archivo.
     * @throws {HttpError} Si la carga falla o las validaciones no son exitosas en el backend.
     */
    const upload = async (url, file, additionalData = {}) => {
        // --- 1. Validaciones en el lado del cliente (sin cambios, ya estaban bien) ---
        if (!(file instanceof File)) {
            throw new Error('El segundo argumento debe ser un objeto File.');
        }

        if (file.size > FILE_CONSTRAINTS.MAX_SIZE) {
            throw new Error(`El archivo excede el tamaño máximo permitido de ${FILE_CONSTRAINTS.MAX_SIZE / 1048576}MB.`);
        }

        validateFileType(file);

        // --- 2. Creación del FormData (sin cambios, ya estaba bien) ---
        const formData = new FormData();
        formData.append('filepath', file); // Clave 'filepath' que espera el backend de Laravel

        for (const key in additionalData) {
            if (Object.prototype.hasOwnProperty.call(additionalData, key)) {
                formData.append(key, additionalData[key]); // Añade 'reference_code', 'directory', etc.
            }
        }

        try {
            // --- 3. Obtención de Headers (sin cambios, ya estaba bien) ---
            // getHeaders(true) es correcto: obtiene Auth y CSRF, pero deja que el navegador
            // establezca el 'Content-Type: multipart/form-data' automáticamente.
            const headers = getHeaders(true);

            // --- 4. Construcción de la URL y Petición (sin cambios, ya estaba bien) ---
            const fullUrl = new URL(url, API_BASE_URL).toString();

            const response = await fetch(fullUrl, {
                method: 'POST',
                headers: headers,
                credentials: 'include', // Necesario para que Laravel Sanctum funcione correctamente con cookies.
                body: formData,
            });

            // --- 5. Procesamiento de la Respuesta (CORRECCIÓN CLAVE) ---
            // La lógica original era un poco compleja. Se simplifica y robustece.
            const responseText = await response.text();
            let responseData;

            // Intenta parsear la respuesta como JSON. Si está vacía o hay un error, maneja el caso.
            try {
                responseData = responseText ? JSON.parse(responseText) : {};
            } catch (parseError) {
                // Si el parsing falla, no es un error fatal si la petición fue exitosa (ej. status 204).
                // Pero si falló, es útil saber que la respuesta no fue JSON válido.
                if (!response.ok) {
                    console.error('La respuesta de error del servidor no es JSON válido:', responseText);
                    // Lanzamos un error con el texto original para que el desarrollador pueda depurarlo.
                    throw new HttpError(
                        `Error del servidor. Respuesta no válida: ${responseText || 'Vacía'}`,
                        response.status
                    );
                }
                // Si la petición fue OK pero la respuesta JSON es inválida, se puede tratar como un éxito sin datos.
                responseData = { message: 'Operación exitosa con respuesta no-JSON.', status: response.status };
            }

            // Si la respuesta HTTP no fue exitosa (status 4xx o 5xx), lanzamos un error estructurado.
            if (!response.ok) {
                // El backend de Laravel devuelve { message: '...', errors: {...} }. Usamos ese `message`.
                const errorMessage = responseData.message || `Error del servidor al cargar el archivo: ${response.status}`;
                console.error('Error en la carga del archivo:', responseData);
                throw new HttpError(errorMessage, response.status, responseData);
            }

            // --- 6. Retorno de Datos Exitosos (CORRECCIÓN CLAVE) ---
            // El backend devuelve un objeto `{ message: '...', data: { url: '...', path: '...' } }`.
            // Es mejor retornar el objeto completo `responseData` para que el código que llama a `upload`
            // tenga acceso a todo (`message` y `data`).
            return responseData;

        } catch (error) {
            // Si el error ya es un HttpError, simplemente lo relanzamos.
            if (error instanceof HttpError) {
                throw error;
            }
            // Para otros errores (ej. de red), los envolvemos en un error genérico para consistencia.
            console.error('Error detallado en la carga del archivo:', error);
            throw new Error(`Error inesperado al cargar el archivo: ${error.message}`);
        }
    };

    /**
     * Realiza una petición POST que puede esperar una respuesta de tipo Blob (ej. PDF).
     * @param {string} url - La URL del endpoint.
     * @param {Object} [data={}] - Los datos a enviar en el cuerpo de la petición.
     * @param {Object} [config={}] - Configuración adicional, incluyendo `responseType: 'blob'`.
     * @returns {Promise<Object|Blob>} Los datos de la respuesta o un Blob.
     */
    const postPdf = async (url, data = {}, config = {}) => {
        try {
            const options = {
                method: 'POST',
                body: JSON.stringify(data),
                headers: getHeaders(), // Ya incluye Content-Type, Accept, Auth, X-XSRF-Token
                credentials: 'include',
                ...config
            };

            const response = await fetch(new URL(url, API_BASE_URL).toString(), options);

            if (!response.ok) {
                let errorData;
                const responseText = await response.text();
                try {
                    errorData = responseText ? JSON.parse(responseText) : {};
                } catch (e) {
                    errorData = { message: responseText || 'Error desconocido del servidor', originalText: responseText };
                }
                throw new HttpError(
                    errorData.message || `HTTP error! status: ${response.status}`,
                    response.status,
                    errorData
                );
            }

            // Si se espera un Blob (ej. PDF), retornar el Blob
            if (config.responseType === 'blob') {
                return response.blob();
            }

            // De lo contrario, intentar parsear como JSON
            const responseText = await response.text();
            try {
                return responseText ? JSON.parse(responseText) : {};
            } catch (e) {
                throw new SyntaxError(`La respuesta no es JSON válido para postPdf: ${responseText}`);
            }

        } catch (error) {
            return handleError(error, 'POST', url, data);
        }
    };

    // --- Retorno del Objeto Público ---

    return Object.freeze({
        baseURL: API_BASE_URL,
        getToken,
        setToken,
        setUserData,
        getUserData,
        hasRole,
        hasPermission,
        getEntityId,
        initializeSanctum,
        postPdf,
        upload,
        cancelRequest,
        ...httpMethodsMap
    });
})();

export default HTTPService;
