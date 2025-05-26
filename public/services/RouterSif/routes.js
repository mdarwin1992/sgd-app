export const routes = [
    // Rutas para visualización de archivos y documentos
    {
        path: "/dashboard/show-file/:id/:id",
        component: "viewer.file",
        name: "Visor de Archivo",
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: "/dashboard/show-response/:id/:id",
        component: "viewer.response",
        name: "Visor de Respuesta",
        meta: {
            requiresAuth: false,
        },
    },

    // Rutas para gestión de tickets
    {
        path: "/reportes/tabla-de-retencion-documental/:id",
        component: "viewer.file",
        name: "Visor de Ticket",
        meta: {
            requiresAuth: false,
        },
    },
    // Rutas para gestión de tickets
    {
        path: "/dashboard/ticket/:id/:id",
        component: "viewer.file",
        name: "Visor de Ticket",
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: "/dashboard/ticket/qr/:id/:id",
        component: "viewer.file",
        name: "Visor de Ticket QR",
        meta: {
            requiresAuth: false,
        },
    },

    // Rutas de autenticación y errores
    {
        path: "/login",
        component: "login",
        name: "Iniciar Sesión",
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: "/not-found",
        component: "components.errors.not-found",
        name: "Página No Encontrada",
        meta: {
            requiresAuth: false,
        },
    },
    {
        path: "/forbidden",
        component: "errors.forbidden",
        name: "Acceso Denegado",
        meta: {
            requiresAuth: false,
        },
    },

    // Rutas del dashboard y empresa
    {
        path: "/dashboard",
        component: "dashboard",
        name: "Dashboard Principal",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["dashboard.page"],
        },
    },
    {
        path: "/dashboard/empresa",
        component: "dashboard",
        name: "Gestión de Empresa",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["business.index"],
        },
    },
    {
        path: "/dashboard/empresa/crear",
        component: "dashboard",
        name: "Crear Empresa",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["business.create"],
        },
    },
    {
        path: "/dashboard/empresa/actualizar/:id",
        component: "dashboard",
        name: "Actualizar Empresa",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["business.update"],
        },
    },

    // Rutas de gestión de departamentos
    {
        path: "/dashboard/departamentos",
        component: "dashboard",
        name: "Gestión de Departamentos",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["departments.index"],
        },
    },
    {
        path: "/dashboard/departamento/crear",
        component: "dashboard",
        name: "Crear Departamento",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["departments.create"],
        },
    },
    {
        path: "/dashboard/departamento/actualizar/:id",
        component: "dashboard",
        name: "Actualizar Departamento",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["departments.update"],
        },
    },

    // Rutas de gestión de oficinas
    {
        path: "/dashboard/oficinas",
        component: "dashboard",
        name: "Gestión de Oficinas",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["offices.index"],
        },
    },
    {
        path: "/dashboard/oficina/crear",
        component: "dashboard",
        name: "Crear Oficina",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["offices.create"],
        },
    },
    {
        path: "/dashboard/oficina/actualizar/:id",
        component: "dashboard",
        name: "Actualizar Oficina",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["offices.update"],
        },
    },

    // Rutas de ventanilla única y gestión de correspondencia
    {
        path: "/dashboard/ventanilla-unica/reportes",
        component: "components.reports.singlewindow.singlewindow",
        name: "Reportes de Ventanilla Única",
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/dashboard/ventanilla-unica/recepcion",
        component: "components.recepcion.index",
        name: "Recepción de Ventanilla Única",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["reception.create"],
        },
    },
    {
        path: "/dashboard/ventanilla-unica/transferir-correspondencias-recibidas",
        component: "components.transfer.index",
        name: "Transferencia de Correspondencias Recibidas",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["transfer.index"],
        },
    },
    {
        path: "/dashboard/ventanilla-unica/transferir-correspondencias/:id",
        component: "components.transfer.store",
        name: "Transferir Correspondencia",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["transfer.create"],
        },
    },

    // Rutas de gestión de buzón y mensajes
    {
        path: "/dashboard/ventanilla-unica/mi-buzon",
        component: "components.mailbox.index",
        name: "Mi Buzón",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["mailbox.index"],
        },
    },
    {
        path: "/dashboard/ventanilla-unica/mi-buzon/responder/:id",
        component: "components.mailbox.create",
        name: "Responder Mensaje",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["mailbox.create"],
        },
    },
    {
        path: "/dashboard/ventanilla-unica/enviar",
        component: "components.sendings.create",
        name: "Enviar Documento",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["sendings.create"],
        },
    },

    // Rutas de gestión documental
    {
        path: "/dashboard/tabla-de-retencion-documental",
        component: "components.trd.create",
        name: "Tabla de Retención Documental",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["trd.create"],
        },
    },
    {
        path: "/dashboard/archivo-central",
        component: "components.centralfile.create",
        name: "Archivo Central",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["centralfile.create"],
        },
    },
    {
        path: "/dashboard/archivo-central/consultas",
        component: "components.query.consultation",
        name: "Consultas de Archivo Central",
        meta: {
            requiresAuth: true,
            roles: ["USUARIO", "ADMINISTRADOR", "EMPRESA"],
            permissions: ["query.consultation"],
        },
    },

    // Rutas de gestión de usuarios
    {
        path: "/dashboard/usuarios",
        component: "components.users.index",
        name: "Gestión de Usuarios",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["users.index"],
        },
    },
    {
        path: "/dashboard/usuario/crear",
        component: "components.users.create",
        name: "Crear Usuario",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["users.create"],
        },
    },
    {
        path: "/dashboard/usuarios/actualizar/:id",
        component: "components.users.update",
        name: "Actualizar Usuario",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["users.update"],
        },
    },
    // Rutas de Archivo Histórico
    {
        path: "/dashboard/archivo-historico/crear",
        component: "components.historicFile.create",
        name: "Crear Archivo Histórico",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["historic.create"],
        },
    },
    {
        path: "/dashboard/archivo-historico",
        component: "components.historicFile.index",
        name: "Gestión Archivo Histórico",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["historic.index"],
        },
    },

    // Rutas de prestamos de documentos
    {
        path: "/dashboard/prestamos-documental",
        component: "components.loans.index",
        name: "Crear prestamos de documentos",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["historic.create"],
        },
    },

    {
        path: "/dashboard/prestamos-documental/archivo-central",
        component: "components.loans.historicalarchive",
        name: "Crear prestamos de documentos",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["historic.create"],
        },
    },
    {
        path: "/dashboard/prestamos-documental/archivo-historico",
        component: "components.loans.centralarchive",
        name: "Crear prestamos de documentos",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["historic.create"],
        },
    },
    {
        path: "/reportes/tiquete/:id",
        component: "viewer.file",
        name: "imprimir tiket de prestamos",
        meta: {
            requiresAuth: true,
            roles: ["ADMINISTRADOR", "EMPRESA"],
            permissions: ["historic.create"],
        },
    },
];
