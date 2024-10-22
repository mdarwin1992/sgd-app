const HTTPService = (() => {
    // Constantes para headers HTTP
    const CONTENT_TYPE = 'Content-Type';
    const AUTHORIZATION = 'Authorization';
    const ACCEPT = 'Accept';
    const BEARER = 'Bearer';
    const X_XSRF_TOKEN = 'X-XSRF-TOKEN';

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
    const upload = async (url, e) => {
        const IMAGE_SIZE_DOCUMENT = 262144000;
        if (e.target.files[0].size > IMAGE_SIZE_DOCUMENT) {
            throw new Error('El archivo excede el tamaño máximo permitido');
        }

        let formData = new FormData($("#AddForm")[0]);
        formData.set('filepath', e.target.files[0]);

        try {
            const response = await fetch(url, {
                method: "POST", body: formData,
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.data) {
                return data.data;
            } else {
                throw new Error(data.message || 'Error desconocido en la carga del archivo');
            }
        } catch (error) {
            console.error('Error en la carga del archivo:', error);
            throw error;
        }
    };

    return Object.freeze({
        //baseURL: isProduction ? 'https://apptransportessinbarreras.transportessinbarreras.com' : 'http://127.0.0.1:8000',
        baseURL: isProduction ? 'http://www.sgd.local' : 'http://127.0.0.1:8000',
        getToken,
        initializeSanctum,
        upload, ...httpMethodsMap,

        // Creación de componentes
        createComponent({data = {}, methods = {}, computed = {}, elements = {}, events = {}, created, render}) {
            const component = {
                data: typeof data === 'function' ? data() : data,
                methods,
                elements: {},
                computedProperties: {},
                renderCount: 0,
                lastRenderTime: 0,
                renderScheduled: false,

                init() {
                    this.bindMethods();
                    this.cacheElements(elements);
                    this.bindEvents(events);

                    if (created) {
                        created.call(this);
                    }

                    this.scheduleRender();
                },

                bindMethods() {
                    Object.entries(this.methods).forEach(([name, method]) => {
                        this[name] = method.bind(this);
                    });
                },

                cacheElements(elements) {
                    Object.entries(elements).forEach(([key, selector]) => {
                        this.elements[key] = document.querySelector(selector);
                    });
                },

                bindEvents(events) {
                    Object.entries(events).forEach(([key, methodName]) => {
                        const [elementKey, eventType] = key.split(':');
                        const element = this.elements[elementKey];
                        const method = this.methods[methodName];
                        if (element && method) {
                            element.addEventListener(eventType, method);
                        }
                    });
                },

                scheduleRender() {
                    if (this.renderScheduled) {
                        return;
                    }

                    const now = Date.now();
                    if (now - this.lastRenderTime < 16) {
                        this.renderScheduled = true;
                        requestAnimationFrame(() => {
                            this.renderScheduled = false;
                            this.updateView();
                        });
                    } else {
                        this.updateView();
                    }
                },

                updateView() {
                    this.lastRenderTime = Date.now();

                    Object.entries(this.elements).forEach(([key, element]) => {
                        const dataKey = element.getAttribute('data-bind');
                        const showKey = element.getAttribute('data-show');
                        if (dataKey && this.data[dataKey] !== undefined) {
                            element.textContent = this.data[dataKey];
                        }
                        if (showKey && this.data[showKey] !== undefined) {
                            element.style.display = this.data[showKey] ? '' : 'none';
                        }
                    });

                    if (render) {
                        render.call(this);
                    }

                    this.renderCount++;
                },

                setData(newData) {
                    Object.assign(this.data, newData);
                    this.scheduleRender();
                }
            };

            // Agregar propiedades computadas
            Object.keys(computed).forEach(key => {
                Object.defineProperty(component, key, {
                    get: () => computed[key].call(component), enumerable: true
                });
            });

            return component;
        }
    });
})();

export default HTTPService;
