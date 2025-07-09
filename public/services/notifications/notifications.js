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
            notificationContainer: '.px-3[data-simplebar]',
            clearAllBtn: '.text-dark.text-decoration-underline',
            viewAllBtn: '.dropdown-item.text-center.text-primary',
            notificationIcon: '.ri-notification-3-line'
        };

        this.notificationContainer = document.querySelector(this.elements.notificationContainer);
        this.clearAllBtn = document.querySelector(this.elements.clearAllBtn);
        this.viewAllBtn = document.querySelector(this.elements.viewAllBtn);
        this.notificationIcon = document.querySelector(this.elements.notificationIcon);

        if (!this.notificationContainer || !this.notificationIcon) {
            console.error('Error: El contenedor de notificaciones o el icono no se encontraron. La clase NotificationHandler no se inicializará.');
            return;
        }

        const userData = HTTPService.getUserData();
        this.userId = userData ? userData.id : null;
        this.token = HTTPService.getToken();

        this.refreshInterval = 30000;
        this.refreshTimer = null;

        this.initEventListeners();
        this.startAutoRefresh();
    }

    initEventListeners() {
        if (this.clearAllBtn) {
            this.clearAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.clearAllNotifications();
            });
        }
    }

    startAutoRefresh() {
        this.fetchNotifications();
        this.refreshTimer = setInterval(() => this.fetchNotifications(), this.refreshInterval);
    }

    stopAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    updateNotificationBadge(hasUnread) {
        const parent = this.notificationIcon.parentElement;
        if (!parent) return;

        const existingBadge = parent.querySelector('.noti-icon-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        if (hasUnread) {
            const badge = document.createElement('span');
            badge.className = 'noti-icon-badge';
            parent.insertBefore(badge, this.notificationIcon.nextSibling);
        }
    }

    async fetchNotifications() {
        if (!this.userId || !this.token) {
            this.displayError('Sesión expirada o inválida.');
            this.stopAutoRefresh();
            return;
        }

        try {
            const notifications = await HTTPService.get(`/api/notifications/unread/${this.userId}`);
            // this.displayNotifications(notifications);

            // CORRECCIÓN 1: Usamos la propiedad 'read' (booleano) en lugar de 'read_at' (timestamp)
            const hasUnread = notifications.some(notif => !notif.read); // <-- ¡CAMBIO AQUÍ!

            //this.updateNotificationBadge(hasUnread);
        } catch (error) {
            console.error('Error al obtener notificaciones:', error);
            this.handleFetchError(error);
        }
    }

    handleFetchError(error) {
        if (error.name === 'HttpError') {
            this.displayError(`Error del servidor: ${error.message}.`);
            if (error.status === 401 || error.status === 403) {
                this.stopAutoRefresh();
            }
        } else {
            this.displayError('Ocurrió un error inesperado al cargar las notificaciones.');
        }
    }

    displayNotifications(notifications) {
        this.notificationContainer.innerHTML = '';

        if (notifications.length === 0) {
            // Se cumple tu petición: el contenedor queda vacío si no hay notificaciones.
            this.updateNotificationBadge(false);
            return;
        }

        let currentDate = '';

        notifications.forEach(notification => {
            let notificationData = notification.data;

            if (!notificationData || typeof notificationData !== 'object') {
                console.warn('Se encontró una notificación con `data` inválida. Usando datos de fallback.', notification);
                notificationData = { title: 'Notificación', message: 'Contenido no disponible.' };
            }

            const notificationDate = new Date(notification.created_at);
            const formattedDate = this.formatDateInSpanish(notificationDate);

            if (formattedDate !== currentDate) {
                currentDate = formattedDate;
                const dateHeader = document.createElement('h5');
                dateHeader.className = 'text-muted font-13 fw-normal mt-2 mb-2 text-center';
                dateHeader.textContent = this.isToday(notificationDate) ? 'Hoy' : formattedDate;
                this.notificationContainer.appendChild(dateHeader);
            }

            const notificationElement = this.createNotificationElement(notification, notificationData);
            this.notificationContainer.appendChild(notificationElement);
        });
    }

    createNotificationElement(notification, notificationData) {
        const element = document.createElement('a');
        element.href = notificationData.url || 'javascript:void(0);';
        if (notificationData.url) {
            element.target = '_self';
        }

        // CORRECCIÓN 2: Usamos la propiedad 'read' para determinar si la notificación está leída
        const isUnread = !notification.read; // <-- ¡CAMBIO AQUÍ!

        element.className = `dropdown-item p-0 notify-item card shadow-none mb-2 ${isUnread ? 'unread-noti bg-light' : ''}`;

        const title = notificationData.title || 'Notificación';
        const message = notificationData.message || '';
        const iconClass = notificationData.icon || 'mdi mdi-comment-account-outline';
        const additionalInfo = this.getAdditionalInfo(notificationData);

        element.innerHTML = `
            <div class="card-body">
                <span class="float-end noti-close-btn text-muted" title="Marcar como leída"><i class="mdi mdi-close"></i></span>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="notify-icon bg-primary">
                            <i class="${iconClass}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 text-truncate ms-2">
                        <h5 class="noti-item-title fw-semibold font-14">
                            ${title}
                            <small class="fw-normal text-muted ms-1">${this.timeAgoInSpanish(new Date(notification.created_at))}</small>
                        </h5>
                        <small class="noti-item-subtitle text-muted">${message}</small>
                        ${additionalInfo ? `<small class="noti-item-details text-muted d-block mt-1">${additionalInfo}</small>` : ''}
                    </div>
                </div>
            </div>`;

        element.querySelector('.noti-close-btn').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.markAsRead(notification.id);
        });

        element.addEventListener('click', (e) => {
            if (!notificationData.url) {
                e.preventDefault();
            }
            this.handleNotificationClick(notification, notificationData);
        });

        return element;
    }

    getAdditionalInfo(notificationData) {
        if (notificationData.transfer_id) {
            return `Transferencia #${notificationData.transfer_id}`;
        }
        return '';
    }

    async markAsRead(notificationId) {
        try {
            const result = await HTTPService.post(`/api/notifications/${notificationId}/mark-as-read`, {});
            if (result && result.success) {
                this.fetchNotifications();
            }
        } catch (error) {
            console.error('Error marcando notificación como leída:', error);
        }
    }

    async clearAllNotifications() {
        if (!this.userId) return;
        try {
            const result = await HTTPService.post(`/api/notifications/clear-all/${this.userId}`, {});
            if (result && result.success) {
                this.fetchNotifications();
            }
        } catch (error) {
            console.error('Error al limpiar todas las notificaciones:', error);
        }
    }

    handleNotificationClick(notification, notificationData) {
        this.markAsRead(notification.id);
    }

    formatDateInSpanish(date) {
        return date.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }

    isToday(date) {
        const today = new Date();
        return date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
    }

    timeAgoInSpanish(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return `hace ${seconds} seg`;
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `hace ${minutes} min`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `hace ${hours} h`;
        const days = Math.floor(hours / 24);
        return `hace ${days} día(s)`;
    }

    displayError(message) {
        this.notificationContainer.innerHTML = `<div class="text-center py-2"><p class="text-danger">${message}</p></div>`;
        this.updateNotificationBadge(false);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new NotificationHandler();
});
