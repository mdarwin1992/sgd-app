/**
 * Clase: Helpers
 * Proporciona funciones utilitarias para autenticación y manejo de mensajes.
 */
class Helpers {
    /**
     * Muestra un mensaje de éxito y redirige a la URL especificada.
     * @param {string} message - Mensaje a mostrar.
     * @param {string} url - URL a la que redirigir después de mostrar el mensaje.
     */
    static getMessage(message, url) {
        Swal.fire({
            toast: true,
            icon: 'success',
            title: message,
            animation: false,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000, // Tiempo de visualización del mensaje en milisegundos.
            timerProgressBar: true,
            didOpen: (toast) => {
                // Manejo de eventos para pausar/reanudar el temporizador al pasar el mouse.
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        }).then(() => {
            location.assign(url); // Redirige a la URL especificada.
        });
    }

    /**
     * Obtiene un parámetro de la URL.
     * @param {number|null} id - Índice del parámetro a obtener. Si no se proporciona, se obtiene el primero.
     * @return {string|null} - Parámetro de la URL o null si no existe.
     */
    static getAllGetParams(id = 0) {
        // Separa la ruta y filtra elementos vacíos.
        const parts = location.pathname.split("/").filter(item => item !== "");
        return parts[id] || null; // Devuelve el parámetro en el índice dado o null si no existe.
    }

    /**
     * Clase interna: AuthService
     * Maneja la autenticación y verificación de token.
     */
    static AuthService = class {
        /**
         * Verifica si el token ha expirado.
         */
        static checkTokenExpiration() {
            const expirationTime = localStorage.getItem('expirationTime');
            const currentTime = new Date().getTime();

            if (currentTime > expirationTime) {
                // Si el token ha expirado, se eliminan los datos de autenticación.
                localStorage.removeItem('token');
                localStorage.removeItem('attributes');
                localStorage.removeItem('expirationTime');
                alert('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
                location.href = '/login'; // Redirige al usuario a la página de inicio de sesión.
            }
        }

        /**
         * Obtiene el token de autenticación.
         * @return {string|null} - Devuelve el token si existe.
         */
        static getToken() {
            return localStorage.getItem('token');
        }

        /**
         * Verifica si el usuario está autenticado.
         * @return {boolean} - Devuelve true si hay un token.
         */
        static isAuthenticated() {
            const token = this.getToken();
            return !!token; // Devuelve true si hay un token.
        }
    }

    /**
     * Verifica la autenticación y la expiración del token.
     */
    static checkAuthentication() {
        this.AuthService.checkTokenExpiration();
    }

    /**
     * Comprueba si el usuario está autenticado.
     * @return {boolean} - Devuelve true si está autenticado.
     */
    static isAuthenticated() {
        return this.AuthService.isAuthenticated();
    }

    /**
     * Obtiene el token de autenticación.
     * @return {string|null} - Devuelve el token si existe.
     */
    static obtenerToken() {
        return this.AuthService.getToken();
    }

    static verifyLoan(valor) {
        return valor === 1 ? 'NO DISPONIBLE' : 'DISPONIBLE';
    }

    static verifyDelivery(valor) {
        return valor === 0 ? 'DOCUMENTO ENTREGADO' : 'DOCUMENTO NO ENTREGADO';
    }

    static verifyDeliveryStyle(valor) {
        return valor === 0 ? 'badge-outline-success' : 'badge-outline-warning';
    }

}

export default Helpers;
