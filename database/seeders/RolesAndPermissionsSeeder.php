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
        //
        $business = Role::create(['name' => 'EMPRESA']);
        $administrator = Role::create(['name' => 'ADMINISTRADOR']);
        $reader = Role::create(['name' => 'USUARIO']);

        Permission::create(['name' => 'dashboard.page', 'description' => 'Inicio'])->syncRoles([$administrator, $reader, $business]);

        // Asumiendo que ya tienes definidos los roles $administrator y $reader

        // Entidades
        Permission::create(['name' => 'api.entities.index', 'description' => 'Ver listado de entidades'])->syncRoles([$administrator]);
        Permission::create(['name' => 'api.entity.store', 'description' => 'Crear entidad'])->syncRoles([$administrator]);
        Permission::create(['name' => 'api.entity.show', 'description' => 'Ver detalle de entidad'])->syncRoles([$administrator]);
        Permission::create(['name' => 'api.entity.update', 'description' => 'Actualizar entidad'])->syncRoles([$administrator]);
        Permission::create(['name' => 'api.entity.destroy', 'description' => 'Eliminar entidad'])->syncRoles([$administrator]);

        // Departamentos
        Permission::create(['name' => 'api.departments.index', 'description' => 'Ver listado de departamentos'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.department.store', 'description' => 'Crear departamento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.department.show', 'description' => 'Ver detalle de departamento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.department.update', 'description' => 'Actualizar departamento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.department.destroy', 'description' => 'Eliminar departamento'])->syncRoles([$administrator, $business]);

        // Oficinas
        Permission::create(['name' => 'api.offices.index', 'description' => 'Ver listado de oficinas'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.office.store', 'description' => 'Crear oficina'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.office.show', 'description' => 'Ver detalle de oficina'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.office.update', 'description' => 'Actualizar oficina'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.office.destroy', 'description' => 'Eliminar oficina'])->syncRoles([$administrator, $business]);

        // Recepción
        Permission::create(['name' => 'api.reception.index', 'description' => 'Ver listado de recepciones'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.reception.store', 'description' => 'Crear recepción'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.reception.show', 'description' => 'Ver detalle de recepción'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.reception.update', 'description' => 'Actualizar recepción'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.reception.destroy', 'description' => 'Eliminar recepción'])->syncRoles([$administrator, $business]);

        // Transferencia de correspondencia
        Permission::create(['name' => 'api.correspondence.transfer.index', 'description' => 'Ver listado de transferencias de correspondencia'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.correspondence.transfer.store', 'description' => 'Crear transferencia de correspondencia'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.correspondence.transfer.show', 'description' => 'Ver detalle de transferencia de correspondencia'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.correspondence.transfer.update', 'description' => 'Actualizar transferencia de correspondencia'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.correspondence.transfer.destroy', 'description' => 'Eliminar transferencia de correspondencia'])->syncRoles([$administrator, $business]);

        // Respuesta a solicitudes
        Permission::create(['name' => 'api.mailbox.index', 'description' => 'Ver listado de respuestas a solicitudes'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.mailbox.store', 'description' => 'Crear respuesta a solicitud'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.mailbox.show', 'description' => 'Ver detalle de respuesta a solicitud'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.mailbox.update', 'description' => 'Actualizar respuesta a solicitud'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.mailbox.destroy', 'description' => 'Eliminar respuesta a solicitud'])->syncRoles([$administrator, $business]);

        // Envío de documentos
        Permission::create(['name' => 'api.document.sendings.index', 'description' => 'Ver listado de envíos de documentos'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.document.sendings.store', 'description' => 'Crear envío de documento'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.document.sendings.show', 'description' => 'Ver detalle de envío de documento'])->syncRoles([$administrator, $business, $reader]);
        Permission::create(['name' => 'api.document.sendings.update', 'description' => 'Actualizar envío de documento'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.document.sendings.destroy', 'description' => 'Eliminar envío de documento'])->syncRoles([$administrator, $business]);

        Permission::create(['name' => 'api.users.index', 'description' => 'Ver listado de envíos de usarios'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.users.store', 'description' => 'Crear envío de usuario'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.users.show', 'description' => 'Ver detalle de envío de usuario'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.users.update', 'description' => 'Actualizar envío de usuario'])->syncRoles([$administrator, $business]);
        Permission::create(['name' => 'api.users.destroy', 'description' => 'Eliminar envío de usuario'])->syncRoles([$administrator, $business]);

    }
}
