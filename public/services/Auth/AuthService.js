// Importamos el HTTPService que ya hemos definido.
// Asegúrate de que la ruta de importación sea correcta en tu proyecto.
import HTTPService from '../httpService/HTTPService.js'; // Ajusta esta ruta si es necesario

export default {
    /**
     * Realiza la petición de autenticación al servidor.
     * Utiliza el HTTPService para manejar la llamada POST, los headers y el manejo de errores.
     * @param {string} url - La URL del endpoint de autenticación.
     * @param {Object} request - El objeto con las credenciales del usuario (ej. { email, password }).
     * @returns {Promise<void>} Una promesa que se resuelve cuando la autenticación es exitosa o se rechaza en caso de error.
     */
    async setAuthenticate(url, request) {
        try {
            this.toggleLoadingBackground(true); // Mostrar el fondo de carga

            // Usamos HTTPService.post para realizar la petición.
            // HTTPService ya maneja los headers, Content-Type, Accept y el manejo de errores HTTP.
            const responseData = await HTTPService.post(url, request);

            // Si la petición a través de HTTPService.post fue exitosa,
            // significa que no hubo errores HTTP (status 4xx, 5xx) y la respuesta es JSON válida.
            this.handleSuccess(responseData);

        } catch (error) {
            // Capturamos cualquier error lanzado por HTTPService (HttpError, NetworkError, etc.)
            // o cualquier otro error inesperado.
            console.error('Error en setAuthenticate:', error);
            // Pasamos el mensaje de error para mostrarlo al usuario.
            // Si es un HttpError, el mensaje ya será descriptivo.
            this.handleError(error.message || 'Ocurrió un error inesperado durante la autenticación.');
        } finally {
            this.toggleLoadingBackground(false); // Ocultar el fondo de carga
        }
    },

    /**
     * Maneja una respuesta exitosa de autenticación.
     * Almacena el token y los datos del usuario utilizando el HTTPService.
     * @param {Object} data - Los datos de la respuesta del servidor, incluyendo el token y la información del usuario.
     * @property {string} data.access_token - El token de acceso.
     * @property {number} data.expires_in - El tiempo de expiración del token en segundos.
     * @property {Object} data.user - Los datos del usuario autenticado.
     */
    handleSuccess(data) {
        // Almacenar el token y su tiempo de expiración usando HTTPService
        if (data.access_token && data.expires_in) {
            HTTPService.setToken(data.access_token, data.expires_in);
        } else {
            console.warn('Respuesta de autenticación exitosa pero sin access_token o expires_in.');
        }

        // Almacenar los datos del usuario usando HTTPService
        if (data.user) {
            HTTPService.setUserData(data.user);
        } else {
            console.warn('Respuesta de autenticación exitosa pero sin datos de usuario.');
        }

        // Redirigir al usuario al dashboard después de un breve retraso
        setTimeout(() => location.href = '/dashboard', 100);
    },

    /**
     * Muestra un mensaje de error al usuario.
     * @param {string} message - El mensaje de error a mostrar.
     */
    handleError(message) {
        this.showMessage('danger', message);
    },

    /**
     * Muestra un mensaje en la interfaz de usuario.
     * Asume la existencia de un elemento con id 'messages' en el DOM.
     * @param {string} type - El tipo de alerta (ej. 'success', 'danger', 'warning').
     * @param {string} message - El contenido del mensaje.
     */
    showMessage(type, message) {
        const messagesElement = document.getElementById('messages');
        if (messagesElement) {
            messagesElement.innerHTML = `
                <div class="alert alert-${type} alert-rounded">
                    ${message}
                </div>`;
        } else {
            console.error('Elemento con ID "messages" no encontrado para mostrar el mensaje.');
        }
    },

    /**
     * Muestra u oculta un fondo de carga.
     * Asume la existencia de un elemento con id 'loading-background' en el DOM.
     * @param {boolean} show - True para mostrar, false para ocultar.
     */
    toggleLoadingBackground(show) {
        const loadingBackgroundElement = document.getElementById('loading-background');
        if (loadingBackgroundElement) {
            loadingBackgroundElement.style.display = show ? 'flex' : 'none';
        } else {
            console.warn('Elemento con ID "loading-background" no encontrado.');
        }
    }
};