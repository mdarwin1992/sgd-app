export const routes = [// Ruta para mostrar archivo con visor
    {
        path: '/dashboard/show-file/:id/:id', component: 'viewer.file', name: 'Visor del documento', meta: {
            requiresAuth: false
        }
    },
    // Ruta para visualizar ticket con visor
    {
        path: '/dashboard/reportes', component: 'reports.index', name: 'Visor del documento', meta: {
            requiresAuth: false
        }
    },
    // Ruta para visualizar ticket con visor
    {
        path: '/dashboard/ticket/:id/:id', component: 'viewer.file', name: 'Visor del documento', meta: {
            requiresAuth: false
        }
    }, // Ruta para visualizar QR con visor
    {
        path: '/dashboard/ticket/qr/:id/:id', component: 'viewer.file', name: 'Visor del documento', meta: {
            requiresAuth: false
        }
    }, // Ruta para mostrar una respuesta
    {
        path: '/dashboard/show-response/:id/:id', component: 'viewer.response', name: 'Visor del respuesta', meta: {
            requiresAuth: false
        }
    }, // Ruta para iniciar sesión
    {
        path: '/login', component: 'login', name: 'Iniciar Sesión', meta: {
            requiresAuth: false
        }
    }, // Ruta para página no encontrada
    {
        path: '/not-found', component: 'components.errors.not-found', name: 'Página No Encontrada', meta: {
            requiresAuth: false
        }
    }, // Ruta para acceso denegado
    {
        path: '/forbidden', component: 'errors.forbidden', name: 'Acceso Denegado', meta: {
            requiresAuth: false
        }
    }, // Ruta del dashboard principal
    {
        path: '/dashboard', component: 'dashboard', name: 'Inicio', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['dashboard.page']
        }
    }, // Ruta para ver departamentos
    {
        path: '/dashboard/departamentos', component: 'dashboard', name: 'Inicio', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.departments.index']
        }
    }, // Ruta para crear un departamento
    {
        path: '/dashboard/departamento/crear', component: 'dashboard', name: 'Inicio', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.department.store']
        }
    }, // Ruta para actualizar un departamento
    {
        path: '/dashboard/departamento/actualizar/:id', component: 'dashboard', name: 'Inicio', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.department.update']
        }
    }, // Ruta para ver oficinas
    {
        path: '/dashboard/oficinas', component: 'dashboard', name: 'Inicio', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.offices.index']
        }
    }, // Ruta para crear una oficina
    {
        path: '/dashboard/oficina/crear', component: 'dashboard', name: 'Inicio', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.office.store']
        }
    }, // Ruta para actualizar una oficina
    {
        path: '/dashboard/oficina/actualizar/:id', component: 'dashboard', name: 'Inicio', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.office.update']
        }
    }, // Ruta para ver recepción
    {
        path: '/dashboard/recepcion', component: 'components.recepcion.index', name: 'Empresas', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.reception.index']
        }
    }, // Ruta para transferir correspondencias recibidas
    {
        path: '/dashboard/transferir-correspondencias-recibidas',
        component: 'components.transfer.index',
        name: 'Crear Empresa',
        meta: {
            requiresAuth: true,
            roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'],
            permissions: ['api.correspondence.transfer.index']
        }
    }, // Ruta para transferir correspondencias
    {
        path: '/dashboard/transferir-correspondencias/:id',
        component: 'components.transfer.store',
        name: 'Crear Empresa',
        meta: {
            requiresAuth: true,
            roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'],
            permissions: ['api.correspondence.transfer.store']
        }
    }, // Ruta para ver el buzón personal
    {
        path: '/dashboard/mi-buzon', component: 'components.mailbox.index', name: 'Crear Empresa', meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.mailbox.index']
        }
    }, // Ruta para responder a un mensaje en el buzón
    {
        path: '/dashboard/mi-buzon/responder/:id',
        component: 'components.mailbox.create',
        name: 'Crear Empresa',
        meta: {
            requiresAuth: true, roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'], permissions: ['api.mailbox.store']
        }
    }, // Ruta para enviar un documento
    {
        path: '/dashboard/enviar', component: 'components.sendings.create', name: 'Crear Empresa', meta: {
            requiresAuth: true,
            roles: ['USUARIO', 'ADMINISTRADOR', 'EMPRESA'],
            permissions: ['api.document.sendings.index']
        }
    }];
