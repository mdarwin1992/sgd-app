// Importa el servicio HTTP centralizado
import HTTPService from '../httpService/HTTPService.js'; // Asegúrate de que la ruta sea correcta

/**
 * Clase: NotificationHandler
 * Gestiona la visualización, actualización y marcado de notificaciones en el frontend.
 * Utiliza HTTPService para todas las operaciones de red y gestión de autenticación.
 */
class NotificationHandler {
    constructor() {
        this.elements = {
            notificationContainer: '.px-3[data-simplebar]', // Contenedor principal de notificaciones
            clearAllBtn: '.text-dark.text-decoration-underline', // Botón para limpiar todas
            viewAllBtn: '.dropdown-item.text-center.text-primary', // Botón para ver todas
            notificationIcon: '.ri-notification-3-line' // Icono de la campana de notificaciones
        };

        // Selecciona los elementos del DOM
        this.notificationContainer = document.querySelector(this.elements.notificationContainer);
        this.clearAllBtn = document.querySelector(this.elements.clearAllBtn);
        this.viewAllBtn = document.querySelector(this.elements.viewAllBtn);
        this.notificationIcon = document.querySelector(this.elements.notificationIcon);

        // Verifica si el contenedor de notificaciones existe
        if (!this.notificationContainer) {
            console.error('Error: El contenedor de notificaciones no se encontró. Asegúrate de que el selector CSS sea correcto.');
            return; // Detiene la ejecución si no se encuentra el contenedor crítico
        }

        // Obtiene el ID del usuario y el token a través de HTTPService
        // Ya no se accede directamente a localStorage
        const userData = HTTPService.getUserData();
        this.userId = userData ? userData.id : null;
        this.token = HTTPService.getToken(); // Usamos HTTPService para obtener el token

        this.refreshInterval = 30000; // Intervalo de refresco de notificaciones (30 segundos)
        this.refreshTimer = null; // Para almacenar el temporizador del refresco automático

        // Inicializa los listeners de eventos y el refresco automático
        this.initEventListeners();
        this.startAutoRefresh();
    }

    /**
     * Inicializa los listeners para los botones de la interfaz de notificaciones.
     */
    initEventListeners() {
        if (this.clearAllBtn) {
            this.clearAllBtn.addEventListener('click', () => this.clearAllNotifications());
        }
        if (this.viewAllBtn) {
            this.viewAllBtn.addEventListener('click', () => this.viewAllNotifications());
        }
    }

    /**
     * Inicia el refresco automático de notificaciones.
     * Realiza una primera carga inmediatamente y luego repite cada `refreshInterval`.
     */
    startAutoRefresh() {
        this.fetchNotifications(); // Carga inicial
        this.refreshTimer = setInterval(() => this.fetchNotifications(), this.refreshInterval);
    }

    /**
     * Detiene el refresco automático de notificaciones.
     */
    stopAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    /**
     * Actualiza el indicador visual (badge) de notificaciones no leídas.
     * @param {boolean} hasUnread - True si hay notificaciones no leídas, false en caso contrario.
     */
    updateNotificationBadge(hasUnread) {
        // Busca y remueve cualquier badge existente
        const existingBadge = this.notificationIcon.parentElement.querySelector('.noti-icon-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        // Si hay notificaciones no leídas, crea y añade un nuevo badge
        if (hasUnread) {
            const badge = document.createElement('span');
            badge.className = 'noti-icon-badge'; // Clase CSS para el badge
            // Inserta el badge después del icono de notificación
            this.notificationIcon.parentElement.insertBefore(badge, this.notificationIcon.nextSibling);
        }
    }

    /**
     * Realiza la petición para obtener las notificaciones no leídas del usuario.
     * Utiliza HTTPService para manejar la petición HTTP y la autenticación.
     */
    async fetchNotifications() {
        if (!this.userId || !this.token) {
            console.warn('Advertencia: ID de usuario o token no disponible. No se pueden cargar las notificaciones.');
            // Muestra un mensaje de error si la autenticación falla o redirige si es necesario
            this.displayError('No se pudo cargar las notificaciones. Autenticación fallida o sesión expirada.');
            this.stopAutoRefresh(); // Detiene el refresco si no hay autenticación válida
            // Podrías añadir una redirección al login aquí si fuera crítico y el HTTPService no lo manejara
            // window.location.href = '/login';
            return;
        }

        try {
            // Usa HTTPService para realizar la petición GET
            const notifications = await HTTPService.get(`/api/notifications/unread/${this.userId}`);

            // Muestra las notificaciones obtenidas
            this.displayNotifications(notifications);

            // Verifica si hay notificaciones no leídas para actualizar el badge
            const hasUnreadNotifications = notifications.some(notif => notif.read === 0);
            this.updateNotificationBadge(hasUnreadNotifications);

        } catch (error) {
            console.error('Error al obtener notificaciones:', error);
            this.handleFetchError(error);
        }
    }

    /**
     * Maneja los errores ocurridos durante la obtención de notificaciones.
     * @param {Error} error - El objeto de error.
     */
    handleFetchError(error) {
        // Puedes personalizar los mensajes de error según el tipo de error
        if (error.name === 'HTTPError') { // Asumiendo que HTTPService lanza errores con nombre 'HTTPError'
            this.displayError(`Error del servidor: ${error.message}. Por favor, inténtalo de nuevo más tarde.`);
            // Si el error es 401/403, HTTPService ya debería haber manejado la redirección
        } else if (error instanceof TypeError) {
            this.displayError('La respuesta del servidor no tiene el formato esperado. Por favor, inténtalo de nuevo más tarde.');
        } else {
            this.displayError('Ocurrió un error inesperado al cargar las notificaciones. Por favor, inténtalo de nuevo más tarde.');
        }
    }

    /**
     * Muestra las notificaciones en el contenedor del DOM.
     * @param {Array<Object>} notifications - Array de objetos de notificación.
     */
    displayNotifications(notifications) {
        this.notificationContainer.innerHTML = ''; // Limpia el contenedor actual
        if (notifications.length === 0) {
            this.notificationContainer.innerHTML = '<p class="text-muted p-3 text-center">No hay notificaciones nuevas</p>';
            this.updateNotificationBadge(false); // Asegura que no haya badge si no hay notificaciones
            return;
        }

        let currentDate = ''; // Para agrupar notificaciones por fecha

        notifications.forEach(notification => {
            // Asegúrate de que notification.data es un string JSON válido antes de parsearlo
            let notificationData = {};
            try {
                notificationData = JSON.parse(notification.data);
            } catch (e) {
                console.error('Error al parsear datos de notificación:', e, notification.data);
                // Si falla el parseo, usa un objeto vacío para evitar errores
                notificationData = { title: 'Notificación inválida', message: 'Contenido no disponible' };
            }

            const notificationDate = new Date(notification.created_at);
            const formattedDate = this.formatDateInSpanish(notificationDate);

            // Añade un encabezado de fecha si la fecha cambia
            if (formattedDate !== currentDate) {
                currentDate = formattedDate;
                const dateHeader = document.createElement('h5');
                dateHeader.className = 'text-muted font-13 fw-normal mt-2 text-center';
                dateHeader.textContent = this.isToday(notificationDate) ? 'Hoy' : formattedDate;
                this.notificationContainer.appendChild(dateHeader);
            }

            // Crea y añade el elemento de la notificación
            const notificationElement = this.createNotificationElement(notification, notificationData);
            this.notificationContainer.appendChild(notificationElement);
        });
    }

    /**
     * Crea un elemento HTML para una notificación individual.
     * @param {Object} notification - El objeto de notificación original de la API.
     * @param {Object} notificationData - Los datos parseados del campo `data` de la notificación.
     * @returns {HTMLElement} - El elemento HTML de la notificación.
     */
    createNotificationElement(notification, notificationData) {
        const element = document.createElement('a');
        element.href = 'javascript:void(0);'; // Enlaces que se manejan por JS
        // Clases para el estilo de la notificación
        element.className = `dropdown-item p-0 notify-item card shadow-none mb-2 ${notification.read === 0 ? 'unread-noti' : ''}`;

        // Obtiene título y mensaje de los datos parseados, con fallbacks
        const title = notificationData.title || 'Notificación';
        const message = notificationData.message || '';
        const additionalInfo = this.getAdditionalInfo(notificationData);

        element.innerHTML = `
            <div class="card-body">
                <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="notify-icon bg-primary">
                            <i class="mdi mdi-comment-account-outline"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 text-truncate ms-2">
                        <h5 class="noti-item-title fw-semibold font-14">
                            ${title}
                            <small class="fw-normal text-muted ms-1">${this.timeAgoInSpanish(new Date(notification.created_at))}</small>
                        </h5>
                        <small class="noti-item-subtitle text-muted">
                            ${message}
                        </small>
                        ${additionalInfo ? `<small class="noti-item-details text-muted d-block mt-1">${additionalInfo}</small>` : ''}
                    </div>
                </div>
            </div>
        `;

        // Agrega listener al botón de cerrar/marcar como leído
        element.querySelector('.noti-close-btn').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Evita que el clic en el botón active el manejador del elemento padre
            this.markAsRead(notification.id);
        });

        // Agrega listener al clic en la notificación completa
        element.addEventListener('click', () => this.handleNotificationClick(notification, notificationData));

        return element;
    }

    /**
     * Extrae información adicional de los datos de la notificación.
     * @param {Object} notificationData - Los datos parseados de la notificación.
     * @returns {string} - Cadena con información adicional o vacía.
     */
    getAdditionalInfo(notificationData) {
        // Ejemplo: si la notificación es de un préstamo de documento
        if (notificationData.document_loan_id) {
            return `Préstamo #${notificationData.document_loan_id} · Orden: ${notificationData.order_number || 'N/A'}`;
        }
        // Puedes añadir más lógica aquí para diferentes tipos de notificaciones
        return '';
    }

    /**
     * Marca una notificación específica como leída en el backend.
     * Utiliza HTTPService para la petición POST.
     * @param {string} notificationId - El ID de la notificación a marcar como leída.
     */
    async markAsRead(notificationId) {
        try {
            // Usa HTTPService para enviar la petición POST
            // HTTPService ya debería manejar el X-CSRF-TOKEN y el Authorization header
            const result = await HTTPService.post(`/api/notifications/${notificationId}/mark-as-read`, {});

            if (result && result.success) { // Asumiendo que el backend devuelve {success: true}
                this.fetchNotifications(); // Refresca la lista de notificaciones después de marcar
            } else {
                console.warn('La notificación no pudo ser marcada como leída:', result);
                this.displayError('No se pudo marcar la notificación como leída.');
            }
        } catch (error) {
            console.error('Error marcando notificación como leída:', error);
            this.displayError('Error al procesar la solicitud para marcar como leída.');
        }
    }

    /**
     * Formatea una fecha a una cadena en español (ej. "lunes, 25 de mayo de 2023").
     * @param {Date} date - Objeto Date a formatear.
     * @returns {string} - Fecha formateada.
     */
    formatDateInSpanish(date) {
        const options = {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        };
        return date.toLocaleDateString('es-ES', options);
    }

    /**
     * Comprueba si una fecha dada es "hoy".
     * @param {Date} date - Objeto Date a comprobar.
     * @returns {boolean} - True si la fecha es hoy, false en caso contrario.
     */
    isToday(date) {
        const today = new Date();
        return date.getDate() === today.getDate() &&
            date.getMonth() === today.getMonth() &&
            date.getFullYear() === today.getFullYear();
    }

    /**
     * Calcula y formatea la diferencia de tiempo en español (ej. "hace 5 minutos").
     * @param {Date} date - Objeto Date del cual calcular la diferencia.
     * @returns {string} - Cadena de tiempo transcurrido.
     */
    timeAgoInSpanish(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000; // Años

        if (interval > 1) {
            const years = Math.floor(interval);
            return `hace ${years} ${years === 1 ? 'año' : 'años'}`;
        }

        interval = seconds / 2592000; // Meses
        if (interval > 1) {
            const months = Math.floor(interval);
            return `hace ${months} ${months === 1 ? 'mes' : 'meses'}`;
        }

        interval = seconds / 86400; // Días
        if (interval > 1) {
            const days = Math.floor(interval);
            return `hace ${days} ${days === 1 ? 'día' : 'días'}`;
        }

        interval = seconds / 3600; // Horas
        if (interval > 1) {
            const hours = Math.floor(interval);
            return `hace ${hours} ${hours === 1 ? 'hora' : 'horas'}`;
        }

        interval = seconds / 60; // Minutos
        if (interval > 1) {
            const minutes = Math.floor(interval);
            return `hace ${minutes} ${minutes === 1 ? 'minuto' : 'minutos'}`;
        }

        const secs = Math.floor(seconds); // Segundos
        return `hace ${secs} ${secs === 1 ? 'segundo' : 'segundos'}`;
    }

    /**
     * Muestra un mensaje de error dentro del contenedor de notificaciones.
     * @param {string} message - Mensaje de error a mostrar.
     */
    displayError(message) {
        this.notificationContainer.innerHTML = `<p class="text-danger p-3 text-center">Error: ${message}</p>`;
        this.updateNotificationBadge(false); // No mostrar badge en caso de error
    }

    /**
     * Implementa la lógica para limpiar todas las notificaciones.
     * Esto requerirá una petición al backend para marcarlas todas como leídas o eliminarlas.
     */
    async clearAllNotifications() {
        console.log('Solicitando limpiar todas las notificaciones...');
        if (!this.userId || !this.token) {
            this.displayError('No se puede limpiar las notificaciones sin autenticación.');
            return;
        }
        try {
            // Asume un endpoint para limpiar todas las notificaciones del usuario
            const result = await HTTPService.post(`/api/notifications/clear-all/${this.userId}`, {});
            if (result && result.success) {
                console.log('Todas las notificaciones han sido limpiadas.');
                this.fetchNotifications(); // Refresca la lista después de limpiar
            } else {
                console.warn('Fallo al limpiar todas las notificaciones:', result);
                this.displayError('No se pudieron limpiar todas las notificaciones.');
            }
        } catch (error) {
            console.error('Error al limpiar todas las notificaciones:', error);
            this.displayError('Error al intentar limpiar todas las notificaciones.');
        }
    }

    /**
     * Implementa la lógica para redirigir al usuario a una página donde pueda ver todas sus notificaciones.
     */
    viewAllNotifications() {
        console.log('Redirigiendo a la página de todas las notificaciones...');
        // Puedes redirigir a una URL específica, por ejemplo:
        window.location.href = '/notifications';
    }

    /**
     * Maneja el clic en una notificación individual.
     * Marca la notificación como leída y puede realizar acciones adicionales basadas en el tipo.
     * @param {Object} notification - El objeto de notificación original.
     * @param {Object} notificationData - Los datos parseados del campo `data` de la notificación.
     */
    handleNotificationClick(notification, notificationData) {
        console.log('Notificación clickeada:', notification, notificationData);
        // Marca la notificación como leída
        this.markAsRead(notification.id);

        // Agrega lógica específica basada en el tipo de notificación o sus datos
        if (notificationData.document_loan_id) {
            console.log('Navegando a los detalles del préstamo:', notificationData.document_loan_id);
            // Ejemplo de redirección a la página de detalles de un préstamo
            // Asegúrate de que esta URL sea válida en tus rutas
            // window.location.href = `/loans/${notificationData.document_loan_id}`;
        }
        // Puedes añadir más `if` o un `switch` para manejar diferentes `notificationData.type` si tu backend los envía.
    }
}

// Inicializa el manejador de notificaciones cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    new NotificationHandler();
});