class NotificationHandler {
    constructor() {
        this.elements = {
            // Selectores para los elementos principales
            notificationContainer: '.px-3[data-simplebar]',
            clearAllBtn: '.text-dark.text-decoration-underline',
            viewAllBtn: '.dropdown-item.text-center.text-primary',
            notificationIcon: '.ri-notification-3-line' // Selector para el icono de la campana
        };

        // Obtener referencias a los elementos del DOM
        this.notificationContainer = document.querySelector(this.elements.notificationContainer);
        this.clearAllBtn = document.querySelector(this.elements.clearAllBtn);
        this.viewAllBtn = document.querySelector(this.elements.viewAllBtn);
        this.notificationIcon = document.querySelector(this.elements.notificationIcon);

        // Verificar si se encontró el contenedor de notificaciones
        if (!this.notificationContainer) {
            console.error('Notification container not found');
            return;
        }

        // Obtener datos de usuario y token
        this.userId = this.getUserId();
        this.token = this.getToken();
        this.refreshInterval = 30000; // Intervalo de actualización: 30 segundos
        this.refreshTimer = null;

        // Inicializar los eventos y el auto-refresh
        this.initEventListeners();
        this.startAutoRefresh();
    }

    // Obtener ID de usuario del localStorage
    getUserId() {
        const attributes = JSON.parse(localStorage.getItem('attributes'));
        return attributes ? attributes.id : null;
    }

    // Obtener token del localStorage
    getToken() {
        return localStorage.getItem('token');
    }

    // Inicializar los event listeners
    initEventListeners() {
        if (this.clearAllBtn) {
            this.clearAllBtn.addEventListener('click', () => this.clearAllNotifications());
        }
        if (this.viewAllBtn) {
            this.viewAllBtn.addEventListener('click', () => this.viewAllNotifications());
        }
    }

    // Iniciar la actualización automática
    startAutoRefresh() {
        this.fetchNotifications(); // Obtención inicial
        this.refreshTimer = setInterval(() => this.fetchNotifications(), this.refreshInterval);
    }

    // Detener la actualización automática
    stopAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    // Actualizar el estado del badge
    updateNotificationBadge(hasUnread) {
        // Remover badge existente si hay
        const existingBadge = this.notificationIcon.parentElement.querySelector('.noti-icon-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        // Agregar nuevo badge si hay notificaciones sin leer
        if (hasUnread) {
            const badge = document.createElement('span');
            badge.className = 'noti-icon-badge';
            this.notificationIcon.parentElement.insertBefore(badge, this.notificationIcon.nextSibling);
        }
    }

    // Obtener notificaciones del servidor
    async fetchNotifications() {
        if (!this.userId || !this.token) {
            console.error('User ID or token not found');
            this.displayError('Authentication failed. Please log in again.');
            this.stopAutoRefresh();
            return;
        }

        try {
            const response = await fetch(`/api/notifications/unread/${this.userId}`, {
                headers: {
                    'Authorization': `Bearer ${this.token}`, 'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new TypeError("Oops, we haven't got JSON!");
            }

            const notifications = await response.json();
            this.displayNotifications(notifications);

            // Actualizar el badge basado en si hay notificaciones sin leer
            const hasUnreadNotifications = notifications.some(notif => notif.read === 0);
            this.updateNotificationBadge(hasUnreadNotifications);

        } catch (error) {
            console.error('Error fetching notifications:', error);
            this.handleFetchError(error);
        }
    }

    // Manejar errores de fetch
    handleFetchError(error) {
        if (error instanceof TypeError) {
            this.displayError('The server response was not in the expected format. Please try again later.');
        } else if (error.message.includes('HTTP error!')) {
            this.displayError(`Server error: ${error.message}. Please try again later.`);
        } else {
            this.displayError('An unexpected error occurred. Please try again later.');
        }
    }

    // Mostrar las notificaciones en el contenedor
    displayNotifications(notifications) {
        this.notificationContainer.innerHTML = '';
        if (notifications.length === 0) {
            this.notificationContainer.innerHTML = '<p class="text-muted p-3">No hay notificaciones nuevas</p>';
            return;
        }

        const today = new Date().toDateString();
        let currentDate = '';

        notifications.forEach(notification => {
            const notificationDate = new Date(notification.created_at);
            const formattedDate = this.formatDateInSpanish(notificationDate);

            if (formattedDate !== currentDate) {
                currentDate = formattedDate;
                const dateHeader = document.createElement('h5');
                dateHeader.className = 'text-muted font-13 fw-normal mt-2';
                dateHeader.textContent = this.isToday(notificationDate) ? 'Hoy' : formattedDate;
                this.notificationContainer.appendChild(dateHeader);
            }

            const notificationElement = this.createNotificationElement(notification);
            this.notificationContainer.appendChild(notificationElement);
        });
    }

    // Crear elemento de notificación individual
    createNotificationElement(notification) {
        const element = document.createElement('a');
        element.href = 'javascript:void(0);';
        element.className = 'dropdown-item p-0 notify-item card unread-noti shadow-none mb-2';

        const title = notification.correspondence_transfer?.document?.subject || 'Sin título';
        const jobType = notification.correspondence_transfer?.job_type || 'Sin tipo';
        const sender = notification.correspondence_transfer?.document?.sender_name || 'Sin remitente';

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
                  ${jobType} - ${sender}
                </small>
              </div>
            </div>
          </div>
        `;

        // Agregar event listeners
        element.querySelector('.noti-close-btn').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.markAsRead(notification.id);
        });

        element.addEventListener('click', () => this.handleNotificationClick(notification));

        return element;
    }

    // Marcar notificación como leída
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/mark-as-read`, {
                method: 'POST', headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Authorization': `Bearer ${this.token}`
                }
            });
            const result = await response.json();
            if (result.success) {
                this.fetchNotifications();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Formatear fecha en español
    formatDateInSpanish(date) {
        const options = {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        };
        return date.toLocaleDateString('es-ES', options);
    }

    // Verificar si la fecha es hoy
    isToday(date) {
        const today = new Date();
        return date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
    }

    // Formatear tiempo relativo en español
    timeAgoInSpanish(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;

        if (interval > 1) {
            const years = Math.floor(interval);
            return `hace ${years} ${years === 1 ? 'año' : 'años'}`;
        }

        interval = seconds / 2592000;
        if (interval > 1) {
            const months = Math.floor(interval);
            return `hace ${months} ${months === 1 ? 'mes' : 'meses'}`;
        }

        interval = seconds / 86400;
        if (interval > 1) {
            const days = Math.floor(interval);
            return `hace ${days} ${days === 1 ? 'día' : 'días'}`;
        }

        interval = seconds / 3600;
        if (interval > 1) {
            const hours = Math.floor(interval);
            return `hace ${hours} ${hours === 1 ? 'hora' : 'horas'}`;
        }

        interval = seconds / 60;
        if (interval > 1) {
            const minutes = Math.floor(interval);
            return `hace ${minutes} ${minutes === 1 ? 'minuto' : 'minutos'}`;
        }

        const secs = Math.floor(seconds);
        return `hace ${secs} ${secs === 1 ? 'segundo' : 'segundos'}`;
    }

    // Mostrar mensaje de error
    displayError(message) {
        this.notificationContainer.innerHTML = `<p class="text-danger p-3">Error: ${message}</p>`;
    }

    // Implementar limpiar todas las notificaciones
    clearAllNotifications() {
        console.log('Clear all notifications');
        // Implementar la lógica para limpiar todas las notificaciones
    }

    // Implementar ver todas las notificaciones
    viewAllNotifications() {
        console.log('View all notifications');
        // Implementar la lógica para ver todas las notificaciones
    }

    // Manejar clic en notificación
    handleNotificationClick(notification) {
        console.log('Notification clicked:', notification);
        this.markAsRead(notification.id);
    }
}

// Inicializar el manejador de notificaciones cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    new NotificationHandler();
});
