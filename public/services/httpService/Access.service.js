// Importa el servicio HTTP centralizado
import HTTPService from './HTTPService.js'; // Ajusta la ruta si es necesario

const adminPanelLink = document.getElementById('centralfile');
const OneStopShop = document.getElementById('OneStopShop');
const historic = document.getElementById('historic');
const reports = document.getElementById('reports');
const lending = document.getElementById('lending');
const settings = document.getElementById('settings');
const centralfile = document.getElementById('centralfile');

// Función auxiliar para establecer la visibilidad
function setVisibility(element, isVisible) {
    if (element) { // Asegurarse de que el elemento existe antes de manipularlo
        element.style.display = isVisible ? 'block' : 'none';
    }
}

// Lógica de visibilidad para cada elemento
// (Asumiendo que 'USUARIO', 'EMPRESA', 'ADMINISTRADOR' son permisos como los tienes)

// adminPanelLink (e.g., solo para ADMINISTRADOR)
setVisibility(adminPanelLink, HTTPService.hasPermission('ADMINISTRADOR'));

// OneStopShop (e.g., para USUARIO y EMPRESA)
setVisibility(OneStopShop, HTTPService.hasPermission('USUARIO') || HTTPService.hasPermission('reception.create'));

// historic (e.g., para todos los que puedan crear algo, como en tu ejemplo original de 'reception.create' etc.)
// O si 'ADMINISTRADOR' es suficiente para verlo
setVisibility(historic, HTTPService.hasPermission('ADMINISTRADOR') || HTTPService.hasPermission('historic.create'));

setVisibility(centralfile, HTTPService.hasPermission('ADMINISTRADOR') || HTTPService.hasPermission('centralfile.create'));

// reports (e.g., solo para ADMINISTRADOR y EMPRESA)
setVisibility(reports, HTTPService.hasPermission('ADMINISTRADOR') || HTTPService.hasPermission('EMPRESA'));

// settings (e.g., solo para ADMINISTRADOR y EMPRESA)
setVisibility(settings, HTTPService.hasPermission('ADMINISTRADOR') || HTTPService.hasPermission('EMPRESA'));

// lending (e.g., solo para EMPRESA)
setVisibility(lending, HTTPService.hasPermission('EMPRESA'));