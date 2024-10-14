// Importar rutas
import {routes} from './routes.js';

/* ======================== Limitación de Tasa (Rate Limiting) ======================== */
const rateLimiter = {
    lastRequestTime: {}, requestCount: {}, limit: 5, interval: 60000, // en milisegundos (1 minuto)

    canMakeRequest(endpoint) {
        const now = Date.now();
        if (!this.lastRequestTime[endpoint]) {
            this.lastRequestTime[endpoint] = now;
            this.requestCount[endpoint] = 1;
            return true;
        }
        if (now - this.lastRequestTime[endpoint] > this.interval) {
            this.lastRequestTime[endpoint] = now;
            this.requestCount[endpoint] = 1;
            return true;
        }
        if (this.requestCount[endpoint] < this.limit) {
            this.requestCount[endpoint]++;
            return true;
        }
        return false;
    },
};

async function rateLimitedFetch(url, options = {}) {
    if (rateLimiter.canMakeRequest(url)) {
        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.statusText}`);
            }
            return response;
        } catch (error) {
            throw new Error(`Error en la solicitud: ${error.message}`);
        }
    } else {
        throw new Error('Has excedido el límite de solicitudes. Inténtalo más tarde.');
    }
}

/* ======================== Autenticación ======================== */
function isAuthenticated() {
    return !!getToken() && !isTokenExpired();
}

function getTokenFromCookies() {
    const name = 'token=';
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookies = decodedCookie.split(';');
    for (let cookie of cookies) {
        let c = cookie.trim();
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return null;
}

function isTokenExpired() {
    const expirationTime = localStorage.getItem('expirationTime');
    return !expirationTime || Date.now() > parseInt(expirationTime, 10);
}

function getToken() {
    return localStorage.getItem('token') || getTokenFromCookies();
}

function clearLocalStorage() {
    localStorage.clear();
}

function clearCookies() {
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
        const cookieName = cookie.split('=')[0].trim();
        document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=${location.hostname}; secure; samesite=strict`;
    }
}

/* ======================== Roles y Permisos ======================== */
function getUserRoles() {
    const roles = localStorage.getItem('roles');
    return roles ? JSON.parse(roles) : [];
}

function getUserPermissions() {
    const permissions = localStorage.getItem('permissions');
    return permissions ? JSON.parse(permissions) : [];
}

function hasRequiredRole(requiredRoles) {
    const userRoles = getUserRoles();
    return requiredRoles.some((role) => userRoles.includes(role));
}

function hasRequiredPermission(requiredPermissions) {
    const userPermissions = getUserPermissions();
    return requiredPermissions.every((permission) => userPermissions.includes(permission));
}

/* ======================== Obtener Datos de Usuario ======================== */
let currentUserData = null;

function getUserData() {
    if (currentUserData) {
        return currentUserData;
    }

    // Obtener datos del usuario del localStorage
    const attributesString = localStorage.getItem('attributes');
    let attributes = {};
    if (attributesString) {
        try {
            attributes = JSON.parse(attributesString);
        } catch (error) {
            console.error('Error al parsear los datos del usuario:', error);
        }
    }

    // Obtener nombre del elemento HTML si está disponible
    const nameElement = document.querySelector('[data-user-attribute="name"]');
    const name = nameElement ? nameElement.textContent.trim() : '';

    // Obtener roles usando la función existente
    const userRoles = getUserRoles();

    // Combinar datos, dando prioridad al nombre del elemento HTML si está disponible
    currentUserData = {
        ...attributes,
        name: name || attributes.name || '',
        roles: userRoles
    };

    return currentUserData;
}

function updateUserDataInDOM() {
    const userData = getUserData();

    // Actualizar el nombre del usuario en el DOM
    const userNameElements = document.querySelectorAll('[data-user-attribute="name"]');
    userNameElements.forEach(element => {
        element.textContent = userData.name;
    });

    // Actualizar los roles del usuario en el DOM
    const userRolesElements = document.querySelectorAll('[data-user-attribute="roles"]');
    userRolesElements.forEach(element => {
        element.textContent = userData.roles.join(', ');
    });

    // Puedes agregar más actualizaciones aquí según sea necesario
}

/* ======================== Spinner de Carga ======================== */
function showLoadingSpinner() {
    const loadingBackground = document.getElementById('loading-background');
    if (loadingBackground) {
        loadingBackground.style.display = 'block';
    }
}

function hideLoadingSpinner() {
    const loadingBackground = document.getElementById('loading-background');
    if (loadingBackground) {
        loadingBackground.style.display = 'none';
    }
}

/* ======================== Renderizado de Componentes ======================== */
const renderComponent = (componentName, params = {}) => {
    // Find the matching route
    const matchedRoute = routes.find(route => route.component === componentName);


    if (matchedRoute) {

        // Ocultar elementos no autorizados
        hideUnauthorizedElements();

        // Imprimir elementos visibles/invisibles
        const elements = document.querySelectorAll('[data-roles], [data-permissions]');
        elements.forEach((element) => {
            const requiredRoles = element.dataset.roles ? element.dataset.roles.split(',') : [];
            const requiredPermissions = element.dataset.permissions ? element.dataset.permissions.split(',') : [];

            const hasRole = requiredRoles.length === 0 || hasRequiredRole(requiredRoles);
            const hasPermission = requiredPermissions.length === 0 || hasRequiredPermission(requiredPermissions);

        });

    } else {
        console.log("No matching route found for component:", componentName);
    }
};

/* ======================== Manejo de Navegación ======================== */
function handleNavigation() {
    const currentUrl = window.location.pathname;
    let matchedRoute = null;
    let params = {};

    for (let route of routes) {
        const routeParts = route.path.split('/');
        const urlParts = currentUrl.split('/');

        if (routeParts.length === urlParts.length) {
            let match = true;
            for (let i = 0; i < routeParts.length; i++) {
                if (routeParts[i].startsWith(':')) {
                    params[routeParts[i].slice(1)] = urlParts[i];
                } else if (routeParts[i] !== urlParts[i]) {
                    match = false;
                    break;
                }
            }
            if (match) {
                matchedRoute = route;
                break;
            }
        }
    }

    if (!matchedRoute) {
        console.log("Ruta no encontrada. Redirigiendo a página 404...");
        window.location.href = '/not-found';
        return;
    }

    const {requiresAuth, roles, permissions} = matchedRoute.meta || {};

    if (requiresAuth && !isAuthenticated()) {
        console.log("Autenticación requerida. Redirigiendo a login...");
        redirectToLogin();
        return;
    }

    if (roles && !hasRequiredRole(roles)) {
        console.log("Usuario no tiene los roles necesarios. Redirigiendo a página de acceso denegado...");
        window.location.href = '/forbidden';
        return;
    }

    if (permissions && !hasRequiredPermission(permissions)) {
        console.log("Usuario no tiene los permisos necesarios. Redirigiendo a página de acceso denegado...");
        window.location.href = '/forbidden';
        return;
    }

    renderComponent(matchedRoute.component, params);
}

/* ======================== Autorización y Visibilidad de Elementos ======================== */
function hideUnauthorizedElements() {
    const elements = document.querySelectorAll('[data-roles], [data-permissions]');

    elements.forEach((element) => {
        const requiredRoles = element.dataset.roles ? element.dataset.roles.split(',') : [];
        const requiredPermissions = element.dataset.permissions ? element.dataset.permissions.split(',') : [];

        const hasRole = requiredRoles.length === 0 || hasRequiredRole(requiredRoles);
        const hasPermission = requiredPermissions.length === 0 || hasRequiredPermission(requiredPermissions);

        if (hasRole && hasPermission) {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    });
}

/* ======================== Cierre de Sesión ======================== */
async function logout() {
    showLoadingSpinner();
    try {
        const token = getToken();
        if (token) {
            await rateLimitedFetch('/api/authenticate/logout', {
                method: 'POST', headers: {
                    'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`,
                }, credentials: 'same-origin',
            });
        }
    } catch (error) {
        console.error('Error durante el cierre de sesión:', error);
    } finally {
        hideLoadingSpinner();
        clearLocalStorage();
        clearCookies();
        redirectToLogin();
    }
}

function redirectToLogin() {
    clearLocalStorage();
    clearCookies();
    window.location.href = '/login';
}

/* ======================== Inicialización ======================== */
function initApp() {
    getUserData(); // Cargar datos de usuario
    updateUserDataInDOM(); // Actualizar el DOM con los datos del usuario
    handleNavigation(); // Manejar la navegación inicial
    hideUnauthorizedElements(); // Ocultar elementos no autorizados

    // Configurar el botón de cierre de sesión
    const logoutButton = document.getElementById('logout-btn');
    if (logoutButton) {
        logoutButton.addEventListener('click', logout);
    }
}

/* ======================== Eventos de Navegación ======================== */
window.addEventListener('popstate', handleNavigation);

document.addEventListener('DOMContentLoaded', initApp);

export {
    rateLimitedFetch, logout, isAuthenticated, getUserData, updateUserDataInDOM
};
