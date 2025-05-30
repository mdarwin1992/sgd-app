// Importa el servicio HTTP centralizado
import HTTPService from './HTTPService.js'; // Ajusta la ruta si es necesario

/**
 * Clase: Helpers
 * Proporciona funciones utilitarias para la interfaz de usuario, navegación y lógica de negocio específica.
 * Delega las responsabilidades de autenticación y manejo de tokens a HTTPService.
 */
class Helpers {
    /**
     * Muestra un mensaje de éxito usando SweetAlert2 y redirige a la URL especificada.
     * @param {string} message - Mensaje a mostrar.
     * @param {string} url - URL a la que redirigir después de mostrar el mensaje.
     */
    static showSuccessMessageAndRedirect(message, url) {
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
     * Obtiene un segmento de la ruta (pathname) de la URL.
     * Ejemplo: para una URL como '/users/123/edit', `getPathSegment(1)` devolvería '123'.
     * @param {number} [index=0] - El índice del segmento de la ruta a obtener (0-based).
     * @return {string|null} - El segmento de la ruta o null si no existe.
     */
    static getPathSegment(index = 0) {
        // Separa la ruta por '/' y filtra los elementos vacíos (ej. de un '/' inicial o final).
        const parts = location.pathname.split("/").filter(item => item !== "");
        return parts[index] || null; // Devuelve el parámetro en el índice dado o null si no existe.
    }

    // --- Métodos de autenticación: Delegan completamente a HTTPService ---

    /**
     * Verifica si el usuario está autenticado, delegando la comprobación a HTTPService.
     * @return {boolean} - Devuelve true si hay un token válido.
     */
    static isAuthenticated() {
        return !!HTTPService.getToken(); // `HTTPService.getToken()` ya verifica si hay un token válido.
    }

    // --- Funciones de Lógica de Negocio (Podrían ir en módulos más específicos) ---

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