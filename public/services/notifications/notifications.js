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

        if (!this.notificationContainer) {
            console.error('Notification container not found');
            return;
        }

        this.userId = this.getUserId();
        this.token = this.getToken();
        this.refreshInterval = 30000;
        this.refreshTimer = null;

        this.initEventListeners();
        this.startAutoRefresh();
    }

    getUserId() {
        const attributes = JSON.parse(localStorage.getItem('attributes'));
        return attributes ? attributes.id : null;
    }

    getToken() {
        return localStorage.getItem('token');
    }

    initEventListeners() {
        if (this.clearAllBtn) {
            this.clearAllBtn.addEventListener('click', () => this.clearAllNotifications());
        }
        if (this.viewAllBtn) {
            this.viewAllBtn.addEventListener('click', () => this.viewAllNotifications());
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
        const existingBadge = this.notificationIcon.parentElement.querySelector('.noti-icon-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        if (hasUnread) {
            const badge = document.createElement('span');
            badge.className = 'noti-icon-badge';
            this.notificationIcon.parentElement.insertBefore(badge, this.notificationIcon.nextSibling);
        }
    }

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
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json'
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

            const hasUnreadNotifications = notifications.some(notif => notif.read === 0);
            this.updateNotificationBadge(hasUnreadNotifications);

        } catch (error) {
            console.error('Error fetching notifications:', error);
            this.handleFetchError(error);
        }
    }

    handleFetchError(error) {
        if (error instanceof TypeError) {
            this.displayError('The server response was not in the expected format. Please try again later.');
        } else if (error.message.includes('HTTP error!')) {
            this.displayError(`Server error: ${error.message}. Please try again later.`);
        } else {
            this.displayError('An unexpected error occurred. Please try again later.');
        }
    }

    displayNotifications(notifications) {
        this.notificationContainer.innerHTML = '';
        if (notifications.length === 0) {
            this.notificationContainer.innerHTML = '<p class="text-muted p-3">No hay notificaciones nuevas</p>';
            return;
        }

        const today = new Date().toDateString();
        let currentDate = '';

        notifications.forEach(notification => {
            // Parsear los datos JSON de la notificación
            const notificationData = JSON.parse(notification.data);
            const notificationDate = new Date(notification.created_at);
            const formattedDate = this.formatDateInSpanish(notificationDate);

            if (formattedDate !== currentDate) {
                currentDate = formattedDate;
                const dateHeader = document.createElement('h5');
                dateHeader.className = 'text-muted font-13 fw-normal mt-2';
                dateHeader.textContent = this.isToday(notificationDate) ? 'Hoy' : formattedDate;
                this.notificationContainer.appendChild(dateHeader);
            }

            const notificationElement = this.createNotificationElement(notification, notificationData);
            this.notificationContainer.appendChild(notificationElement);
        });
    }

    createNotificationElement(notification, notificationData) {
        const element = document.createElement('a');
        element.href = 'javascript:void(0);';
        element.className = 'dropdown-item p-0 notify-item card unread-noti shadow-none mb-2';

        // Usar los datos parseados para construir la notificación
        const title = notificationData.title || 'Sin título';
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

        element.querySelector('.noti-close-btn').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.markAsRead(notification.id);
        });

        element.addEventListener('click', () => this.handleNotificationClick(notification, notificationData));

        return element;
    }

    getAdditionalInfo(notificationData) {
        // Extraer información adicional basada en el tipo de notificación
        if (notificationData.document_loan_id) {
            return `Préstamo #${notificationData.document_loan_id} · Orden: ${notificationData.order_number}`;
        }
        return '';
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
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

    formatDateInSpanish(date) {
        const options = {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        };
        return date.toLocaleDateString('es-ES', options);
    }

    isToday(date) {
        const today = new Date();
        return date.getDate() === today.getDate() &&
            date.getMonth() === today.getMonth() &&
            date.getFullYear() === today.getFullYear();
    }

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

    displayError(message) {
        this.notificationContainer.innerHTML = `<p class="text-danger p-3">Error: ${message}</p>`;
    }

    clearAllNotifications() {
        console.log('Clear all notifications');
        // Implementar lógica para limpiar todas las notificaciones
    }

    viewAllNotifications() {
        console.log('View all notifications');
        // Implementar lógica para ver todas las notificaciones
    }

    handleNotificationClick(notification, notificationData) {
        console.log('Notification clicked:', notification, notificationData);
        this.markAsRead(notification.id);

        // Aquí puedes agregar lógica específica basada en el tipo de notificación
        if (notificationData.document_loan_id) {
            // Redirigir o mostrar detalles del préstamo de documento
            console.log('Mostrando detalles del préstamo:', notificationData.document_loan_id);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new NotificationHandler();
});