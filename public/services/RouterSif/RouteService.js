// main.js (o el nombre que le des a este archivo central)

// Importar rutas
import { routes } from './routes.js'; // Asegúrate de que la ruta sea correcta
// Importar el servicio HTTP centralizado
import HTTPService from '../httpService/HTTPService.js'; // Asegúrate de que la ruta sea correcta

/* ======================== Autenticación y Autorización (Delegado a HTTPService) ======================== */

/**
 * Verifica si el usuario está autenticado.
 * Delega la verificación de la existencia y validez del token a HTTPService.
 * @returns {boolean} - True si el usuario está autenticado, false en caso contrario.
 */
function isAuthenticated() {
    return !!HTTPService.getToken();
}

/**
 * Obtiene los roles del usuario actual.
 * @returns {Array<string>} - Un array de roles del usuario.
 */
function getUserRoles() {
    const userData = HTTPService.getUserData();
    return userData && Array.isArray(userData.roles) ? userData.roles : [];
}

/**
 * Obtiene los permisos del usuario actual.
 * @returns {Array<string>} - Un array de permisos del usuario.
 */
function getUserPermissions() {
    const userData = HTTPService.getUserData();
    return userData && Array.isArray(userData.permissions) ? userData.permissions : [];
}

/**
 * Verifica si el usuario tiene al menos uno de los roles requeridos.
 * @param {Array<string>} requiredRoles - Array de roles necesarios.
 * @returns {boolean} - True si el usuario tiene al menos uno de los roles, false en caso contrario.
 */
function hasRequiredRole(requiredRoles) {
    if (!requiredRoles || requiredRoles.length === 0) return true; // Si no se requieren roles, se considera autorizado
    return requiredRoles.some((role) => HTTPService.hasRole(role)); // Lógica OR: el usuario necesita AL MENOS UNO de los roles
}

/**
 * Verifica si el usuario tiene todos los permisos requeridos.
 * @param {Array<string>} requiredPermissions - Array de permisos necesarios.
 * @returns {boolean} - True si el usuario tiene TODOS los permisos, false en caso contrario.
 */
function hasRequiredPermission(requiredPermissions) {
    if (!requiredPermissions || requiredPermissions.length === 0) return true; // Si no se requieren permisos, se considera autorizado
    return requiredPermissions.every((permission) => HTTPService.hasPermission(permission)); // Lógica AND: el usuario necesita TODOS los permisos
}

/* ======================== Obtener Datos de Usuario y Actualizar DOM ======================== */

/**
 * Obtiene los datos completos del usuario, priorizando el servicio HTTP como fuente de verdad.
 * @returns {Object} - Objeto con los datos del usuario (name, roles, permissions).
 */
function getFullUserData() {
    const userData = HTTPService.getUserData();
    return {
        name: userData ? userData.name : 'Invitado',
        roles: getUserRoles(),
        permissions: getUserPermissions() // Incluir permisos para consistencia
    };
}

/**
 * Actualiza la información del usuario en los elementos del DOM.
 */
function updateUserDataInDOM() {
    const userData = getFullUserData(); // Usar la función que prioriza el servicio

    // Actualiza todos los elementos con el atributo data-user-attribute="name"
    const userNameElements = document.querySelectorAll('[data-user-attribute="name"]');
    userNameElements.forEach(element => {
        element.textContent = userData.name;
    });

    // Actualiza todos los elementos con el atributo data-user-attribute="roles"
    const userRolesElements = document.querySelectorAll('[data-user-attribute="roles"]');
    userRolesElements.forEach(element => {
        element.textContent = userData.roles.join(', ') || 'Ninguno';
    });

    // Opcional: Actualizar permisos si se muestran en algún lugar
    const userPermissionsElements = document.querySelectorAll('[data-user-attribute="permissions"]');
    userPermissionsElements.forEach(element => {
        element.textContent = userData.permissions.join(', ') || 'Ninguno';
    });
}

/* ======================== Spinner de Carga ======================== */

/**
 * Muestra el spinner de carga.
 */
function showLoadingSpinner() {
    const loadingBackground = document.getElementById('loading-background');
    if (loadingBackground) {
        loadingBackground.style.display = 'block';
    }
}

/**
 * Oculta el spinner de carga.
 */
function hideLoadingSpinner() {
    const loadingBackground = document.getElementById('loading-background');
    if (loadingBackground) {
        loadingBackground.style.display = 'none';
    }
}

/* ======================== Renderizado de Componentes ======================== */

/**
 * Renderiza un componente de la aplicación.
 * Este es un placeholder. Aquí iría la lógica real para cargar y mostrar el componente.
 * @param {string} componentName - El nombre del componente a renderizar.
 * @param {Object} [params={}] - Parámetros a pasar al componente.
 */
const renderComponent = (componentName, params = {}) => {
    const matchedRoute = routes.find(route => route.component === componentName);

    if (matchedRoute) {
        console.log(`Renderizando componente: ${componentName} con parámetros:`, params);
        // Lógica para cargar dinámicamente el módulo del componente e inicializarlo.
        // Ejemplo (requiere un sistema de importación dinámica y un punto de entrada para cada componente):
        // import(`/path/to/components/${componentName}.js`)
        //     .then(module => {
        //         if (module.init) { // Si el módulo exporta una función init
        //             module.init(params);
        //         } else if (module.default) { // Si exporta una clase o función por defecto
        //             new module.default(params);
        //         }
        //         applyAuthorizationVisibility(); // Aplicar visibilidad después de que el componente se renderice
        //     })
        //     .catch(error => {
        //         console.error(`Error al cargar el componente ${componentName}:`, error);
        //         // Redirigir a una página de error o mostrar un mensaje
        //     });
        applyAuthorizationVisibility(); // Aplicar visibilidad a elementos estáticos o ya presentes
    } else {
        console.warn("No se encontró una ruta coincidente para el componente:", componentName);
    }
};

/* ======================== Manejo de Navegación ======================== */

/**
 * Maneja la navegación de la aplicación basándose en la URL actual.
 * Realiza verificaciones de autenticación y autorización antes de renderizar el componente.
 */
async function handleNavigation() { // Hacer la función async para await initializeSanctum
    showLoadingSpinner(); // Muestra el spinner al inicio de la navegación
    const currentUrl = window.location.pathname;
    let matchedRoute = null;
    let params = {};

    // Recorre las rutas definidas para encontrar una coincidencia
    for (let route of routes) {
        const routeParts = route.path.split('/');
        const urlParts = currentUrl.split('/');

        if (routeParts.length === urlParts.length) {
            let match = true;
            for (let i = 0; i < routeParts.length; i++) {
                if (routeParts[i].startsWith(':')) { // Maneja parámetros dinámicos de ruta (ej. /users/:id)
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

    // Si no se encuentra una ruta, redirige a la página 404
    if (!matchedRoute) {
        console.warn("Ruta no encontrada. Redirigiendo a página 404...");
        hideLoadingSpinner();
        window.location.href = '/not-found';
        return;
    }

    const { requiresAuth, roles, permissions } = matchedRoute.meta || {};

    // Validaciones de autenticación y autorización de la ruta
    if (requiresAuth && !isAuthenticated()) {
        console.log("Autenticación requerida. Redirigiendo a login...");
        hideLoadingSpinner();
        logout(); // Cierra la sesión y redirige al login si no está autenticado
        return;
    }

    if (roles && !hasRequiredRole(roles)) {
        console.warn("Usuario no tiene los roles necesarios. Redirigiendo a página de acceso denegado...");
        hideLoadingSpinner();
        window.location.href = '/forbidden'; // Redirige si no tiene los roles requeridos
        return;
    }

    if (permissions && !hasRequiredPermission(permissions)) {
        console.warn("Usuario no tiene los permisos necesarios. Redirigiendo a página de acceso denegado...");
        hideLoadingSpinner();
        window.location.href = '/forbidden'; // Redirige si no tiene los permisos requeridos
        return;
    }

    // Si todas las validaciones pasan, renderiza el componente asociado a la ruta
    renderComponent(matchedRoute.component, params);
    hideLoadingSpinner(); // Oculta el spinner una vez que el componente se ha "renderizado"
}

/* ======================== Autorización y Visibilidad de Elementos del DOM (Doble Capa de Control) ======================== */

// **Asegúrate de añadir estas clases en tu archivo CSS:**
// .hidden-by-auth {
//     display: none !important; /* OCULTA COMPLETAMENTE el elemento */
// }

// .disabled-by-auth {
//     pointer-events: none; /* INHABILITA los clics y eventos de ratón */
//     opacity: 0.5;          /* Hace el elemento semitransparente para indicar que está inactivo */
//     cursor: not-allowed;   /* Cambia el cursor del ratón a "no permitido" */
// }

/**
 * Verifica si un elemento del DOM tiene autorización para ser visible y activo
 * basándose en sus atributos `data-roles` y `data-permissions`.
 * @param {HTMLElement} element - El elemento del DOM a verificar.
 * @returns {boolean} - `true` si el elemento está autorizado, `false` en caso contrario.
 */
function isElementAuthorized(element) {
    // Obtiene los roles y permisos requeridos del atributo data del elemento
    const requiredRoles = element.dataset.roles ? element.dataset.roles.split(',').map(s => s.trim()) : [];
    const requiredPermissions = element.dataset.permissions ? element.dataset.permissions.split(',').map(s => s.trim()) : [];

    // Verifica si el usuario tiene los roles y permisos necesarios
    const hasRole = requiredRoles.length === 0 || hasRequiredRole(requiredRoles);
    const hasPermission = requiredPermissions.length === 0 || hasRequiredPermission(requiredPermissions);

    // Un elemento es autorizado si cumple ambas condiciones (roles Y permisos)
    return hasRole && hasPermission;
}

/**
 * Aplica el control de visibilidad y estado de actividad a todos los elementos del DOM
 * marcados con `data-roles` o `data-permissions`.
 *
 * Si un elemento no está autorizado, se le aplican dos capas de control en el frontend:
 * 1. Siempre se **oculta** usando la clase `hidden-by-auth`.
 * 2. Si es un elemento interactivo, además se **deshabilita** (con `disabled-by-auth` y/o el atributo `disabled`).
 */
function applyAuthorizationVisibility() {
    // Selecciona todos los elementos del DOM que tienen atributos de roles o permisos
    const elements = document.querySelectorAll('[data-roles], [data-permissions]');

    elements.forEach((element) => {
        const authorized = isElementAuthorized(element);

        // Limpiamos cualquier estado previo para elementos que ahora están autorizados.
        // Esto asegura que si un usuario gana permisos, los elementos vuelvan a ser visibles y activos.
        if (authorized) {
            element.classList.remove('hidden-by-auth');
            element.classList.remove('disabled-by-auth');
            if (element.hasAttribute('disabled')) {
                element.removeAttribute('disabled');
            }
        } else { // Si el elemento NO está autorizado
            // Primera capa de control: Ocultar el elemento para que no sea visible.
            element.classList.add('hidden-by-auth');

            // Segunda capa de control: Deshabilitar el elemento si es interactivo.
            // Esto previene interacciones accidentales incluso si la ocultación CSS es sobreescrita.
            const isInteractive = (
                element.tagName === 'BUTTON' ||
                element.tagName === 'INPUT' ||
                element.tagName === 'SELECT' ||
                element.tagName === 'TEXTAREA' ||
                element.tagName === 'A' // Los enlaces son interactivos y se deshabilitan con pointer-events
            );

            if (isInteractive) {
                element.classList.add('disabled-by-auth');
                // Aplica el atributo 'disabled' solo a los elementos HTML que lo soportan nativamente.
                // Los enlaces (<a>) no tienen 'disabled' nativo; 'pointer-events: none' es suficiente para ellos.
                if (element.tagName !== 'A') {
                    element.setAttribute('disabled', 'true');
                }
            } else {
                // Si no es interactivo pero no autorizado, asegúrate de que no tenga la clase de deshabilitado
                // (útil si los permisos cambiaron y antes era interactivo)
                element.classList.remove('disabled-by-auth');
            }
        }
    });
}

/* ======================== Cierre de Sesión ======================== */

/**
 * Borra las cookies del navegador.
 * Nota: Esta función es muy agresiva y borrará TODAS las cookies para el dominio.
 * Para un control más granular, sería mejor borrar solo las cookies de sesión/auth por nombre.
 */
function clearCookies() {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf("=");
        const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        // Elimina la cookie del path actual, el dominio raíz y el subdominio (para asegurar la eliminación)
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=" + location.hostname;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=." + location.hostname;
    }
}

/**
 * Realiza el proceso de cierre de sesión.
 * Llama al backend para invalidar la sesión y limpia el estado local.
 */
async function logout() {
    showLoadingSpinner(); // Muestra el spinner de carga
    try {
        // Intenta llamar al endpoint de logout del backend a través de HTTPService
        // El backend debería invalidar el refresh token y la sesión
        await HTTPService.post('/api/authenticate/logout', {});
        console.log('Sesión cerrada en el backend.');
    } catch (error) {
        // Captura errores durante el logout (puede ser normal si el refresh token ya expiró en el backend)
        console.error('Error durante el cierre de sesión en el backend (puede ser normal si el refresh token ya expiró):', error);
    } finally {
        hideLoadingSpinner(); // Oculta el spinner de carga
        clearCookies(); // Limpia todas las cookies del navegador
        // Forzar la limpieza del token y datos de usuario en HTTPService y localStorage para asegurar el logout local
        HTTPService.setToken(null, 0); // Establece el token a nulo y expiración a 0
        HTTPService.setUserData(null); // Borra los datos del usuario
        redirectToLogin(); // Redirige al usuario a la página de inicio de sesión
    }
}

/**
 * Redirige al usuario a la página de inicio de sesión.
 */
function redirectToLogin() {
    window.location.href = '/login';
}

/* ======================== Inicialización de la Aplicación ======================== */

/**
 * Inicializa la aplicación: carga datos de usuario, maneja la navegación inicial
 * y aplica la visibilidad basada en autorización.
 */
async function initApp() {
    // Es crucial que HTTPService.initializeSanctum() se ejecute antes de cualquier
    // petición que requiera el XSRF-TOKEN (típicamente las POST, PUT, DELETE).
    // Si tu HTTPService ya lo maneja internamente al inicio de su módulo,
    // o en la primera petición, esta llamada explícita puede ser redundante pero segura.
    // Si tu backend usa Sanctum, DESCOMENTA esta línea:
    await HTTPService.initializeSanctum(); // Asegura que el CSRF token esté disponible

    updateUserDataInDOM(); // Actualiza la información del usuario en el DOM
    await handleNavigation(); // Maneja la navegación inicial basada en la URL actual (ahora async)
    // applyAuthorizationVisibility() ya se llama dentro de handleNavigation y renderComponent
    // pero una llamada aquí asegura que los elementos iniciales estén correctos antes de la navegación
    // y puede ser útil si la navegación no renderiza todo inmediatamente.
    applyAuthorizationVisibility();

    // Configura el listener para el botón de cierre de sesión
    const logoutButton = document.getElementById('logout-btn');
    if (logoutButton) {
        logoutButton.addEventListener('click', logout);
    }
}

/* ======================== Eventos de Navegación del Navegador ======================== */

// Escucha el evento 'popstate' para manejar la navegación hacia atrás/adelante del navegador
window.addEventListener('popstate', handleNavigation);
// Cuando el DOM esté completamente cargado, inicializa la aplicación
document.addEventListener('DOMContentLoaded', initApp);

// Exporta funciones y el servicio HTTP para que otros módulos puedan usarlos
export {
    logout,
    isAuthenticated,
    getFullUserData, // Renombrado para mayor claridad
    updateUserDataInDOM,
    HTTPService // Es útil exportar HTTPService para que otros módulos puedan usarlo directamente.
};