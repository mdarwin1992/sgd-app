<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles principales
        $business = Role::create(['name' => 'EMPRESA']);
        $administrator = Role::create(['name' => 'ADMINISTRADOR']);
        $reader = Role::create(['name' => 'USUARIO']);


        // Acceso al panel principal
        Permission::create(['name' => 'dashboard.page', 'description' => 'Acceder al panel principal'])->syncRoles([$administrator, $reader, $business]);

        // Gestión de Empresas
        Permission::create(['name' => 'business.index', 'description' => 'Visualizar listado de empresas'])->syncRoles([$administrator]);
        Permission::create(['name' => 'business.create', 'description' => 'Registrar nueva empresa'])->syncRoles([$administrator]);
        Permission::create(['name' => 'business.show', 'description' => 'Consultar detalles de empresa'])->syncRoles([$administrator]);
        Permission::create(['name' => 'business.update', 'description' => 'Modificar información de empresa'])->syncRoles([$administrator]);
        Permission::create(['name' => 'business.destroy', 'description' => 'Eliminar registro de empresa'])->syncRoles([$administrator]);

        // Gestión de Departamentos
        Permission::create(['name' => 'departments.index', 'description' => 'Visualizar listado de departamentos'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'departments.create', 'description' => 'Registrar nuevo departamento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'departments.show', 'description' => 'Consultar detalles de departamento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'departments.update', 'description' => 'Modificar información de departamento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'departments.destroy', 'description' => 'Eliminar registro de departamento'])->syncRoles([$administrator, $business]);

        // Gestión de Oficinas
        Permission::create(['name' => 'offices.index', 'description' => 'Visualizar listado de oficinas'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'offices.create', 'description' => 'Registrar nueva oficina'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'offices.show', 'description' => 'Consultar detalles de oficina'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'offices.update', 'description' => 'Modificar información de oficina'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'offices.destroy', 'description' => 'Eliminar registro de oficina'])->syncRoles([$administrator, $business]);

        // Entity series
        Permission::create(['name' => 'entityseries.index', 'description' => 'Visualizar listado de las series de entidad'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'entityseries.create', 'description' => 'Registrar nueva series de entidad'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'entityseries.show', 'description' => 'Consultar detalles series de entidad'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'entityseries.update', 'description' => 'Modificar series de entidad'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'entityseries.destroy', 'description' => 'Eliminar series de entidad'])->syncRoles([$administrator, $business]);

        // Gestión de Tabla de Retención Documental (TRD)
        Permission::create(['name' => 'trd.index', 'description' => 'Visualizar listado de tablas de retención documental'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'trd.create', 'description' => 'Registrar nueva tabla de retención documental'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'trd.show', 'description' => 'Consultar detalles de tabla de retención documental'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'trd.update', 'description' => 'Modificar tabla de retención documental'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'trd.destroy', 'description' => 'Eliminar tabla de retención documental'])->syncRoles([$administrator, $business]);

        // Gestión de Recepción de Documentos
        Permission::create(['name' => 'reception.index', 'description' => 'Visualizar listado de documentos recibidos'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'reception.create', 'description' => 'Registrar nuevo documento recibido'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'reception.show', 'description' => 'Consultar detalles de documento recibido'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'reception.update', 'description' => 'Modificar información de documento recibido'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'reception.destroy', 'description' => 'Eliminar registro de documento recibido'])->syncRoles([$administrator, $business]);

        // Gestión de Transferencias de Correspondencia
        Permission::create(['name' => 'transfer.index', 'description' => 'Visualizar listado de transferencias'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'transfer.create', 'description' => 'Registrar nueva transferencia'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'transfer.show', 'description' => 'Consultar detalles de transferencia'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'transfer.update', 'description' => 'Modificar información de transferencia'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'transfer.destroy', 'description' => 'Eliminar registro de transferencia'])->syncRoles([$administrator, $business]);

        // Gestión de Buzón de Respuestas
        Permission::create(['name' => 'mailbox.index', 'description' => 'Visualizar listado de respuestas'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'mailbox.create', 'description' => 'Registrar nueva respuesta'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'mailbox.show', 'description' => 'Consultar detalles de respuesta'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'mailbox.update', 'description' => 'Modificar información de respuesta'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'mailbox.destroy', 'description' => 'Eliminar registro de respuesta'])->syncRoles([$administrator, $business]);

        // Gestión de Envíos de Documentos
        Permission::create(['name' => 'sendings.index', 'description' => 'Visualizar listado de documentos enviados'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'sendings.create', 'description' => 'Registrar nuevo envío de documento'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'sendings.show', 'description' => 'Consultar detalles de documento enviado'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'sendings.update', 'description' => 'Modificar información de documento enviado'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'sendings.destroy', 'description' => 'Eliminar registro de documento enviado'])->syncRoles([$administrator, $business]);

        // Gestión de Archivo Central
        Permission::create(['name' => 'centralfile.index', 'description' => 'Visualizar contenido del archivo central'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'centralfile.create', 'description' => 'Registrar nuevo documento en archivo central'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'centralfile.show', 'description' => 'Consultar documento del archivo central'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'centralfile.update', 'description' => 'Modificar documento del archivo central'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'centralfile.destroy', 'description' => 'Eliminar documento del archivo central'])->syncRoles([$administrator, $business]);

        // Consultas del Archivo Central
        Permission::create(['name' => 'query.consultation', 'description' => 'Realizar consultas generales del archivo central'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'query.findDocument', 'description' => 'Buscar documentos específicos en archivo central'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'query.searchByBox', 'description' => 'Buscar documentos por número de caja'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'query.searchBySerial', 'description' => 'Buscar documentos por número de serie'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'query.searchByYear', 'description' => 'Buscar documentos por año'])->syncRoles([$administrator, $business]);

        // Reportes
        Permission::create(['name' => 'reports.singlewindow', 'description' => 'Generar reportes de ventanilla única'])->syncRoles([$administrator, $business]);

        // Gestión de Usuarios
        Permission::create(['name' => 'users.index', 'description' => 'Visualizar listado de usuarios'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'users.create', 'description' => 'Registrar nuevo usuario'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'users.show', 'description' => 'Consultar detalles de usuario'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'users.update', 'description' => 'Modificar información de usuario'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'users.destroy', 'description' => 'Eliminar registro de usuario'])->syncRoles([$administrator, $business]);

        // Gestión de Archivo Histórico
        Permission::create(['name' => 'historic.index', 'description' => 'Ver listado de documentos en el archivo histórico'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'historic.create', 'description' => 'Agregar nuevo documento al archivo histórico'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'historic.show', 'description' => 'Ver detalles de un documento del archivo histórico'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'historic.update', 'description' => 'Editar información de un documento del archivo histórico'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'historic.destroy', 'description' => 'Eliminar un documento del archivo histórico'])->syncRoles([$administrator, $business]);

        // Gestión para Préstamo de documento
        Permission::create(['name' => 'lending.index', 'description' => 'Ver listado de préstamos de documentos'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'lending.create', 'description' => 'Registrar nuevo préstamo de documento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'lending.show', 'description' => 'Ver detalles de un préstamo de documento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'lending.update', 'description' => 'Modificar información de un préstamo de documento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'lending.destroy', 'description' => 'Cancelar/eliminar un préstamo de documento'])->syncRoles([$administrator, $business]);

        // Gestión de Permisos y Roles
        Permission::create(['name' => 'roles.index', 'description' => 'Ver listado de roles'])->syncRoles([$administrator]);
        Permission::create(['name' => 'roles.create', 'description' => 'Crear nuevos roles'])->syncRoles([$administrator]);
        Permission::create(['name' => 'roles.show', 'description' => 'Ver detalles de un rol'])->syncRoles([$administrator]);
        Permission::create(['name' => 'roles.update', 'description' => 'Modificar roles'])->syncRoles([$administrator]);
        Permission::create(['name' => 'roles.destroy', 'description' => 'Eliminar roles'])->syncRoles([$administrator]);
        Permission::create(['name' => 'permissions.index', 'description' => 'Ver listado de permisos'])->syncRoles([$administrator]);
        Permission::create(['name' => 'permissions.create', 'description' => 'Crear nuevos permisos'])->syncRoles([$administrator]);
        Permission::create(['name' => 'permissions.show', 'description' => 'Ver detalles de un permiso'])->syncRoles([$administrator]);
        Permission::create(['name' => 'permissions.update', 'description' => 'Modificar permisos'])->syncRoles([$administrator]);
        Permission::create(['name' => 'permissions.destroy', 'description' => 'Eliminar permisos'])->syncRoles([$administrator]);
    }
}
