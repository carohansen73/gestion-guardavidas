<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Roles que se pueden editar desde esta pantalla. superadmin queda afuera
     * a propósito: no se puede tocar desde acá (siempre a mano por SQL), así
     * nadie puede auto-sacarse o sacarle a otro el acceso a esta pantalla.
     */
    private const ROLES_EDITABLES = ['guardavida', 'encargado', 'admin'];

    /**
     * Permiso que jamás se puede asignar desde esta pantalla, porque es
     * justamente el que da acceso a esta misma pantalla. Solo lo tiene el
     * rol superadmin, asignado a mano por SQL.
     */
    private const PERMISO_PROTEGIDO = 'abm_roles_y_permisos';

    /**
     * Muestra la matriz rol x permiso.
     */
    public function index()
    {
        $roles = Role::whereIn('name', self::ROLES_EDITABLES)
            ->with('permissions')
            ->get()
            ->keyBy('name');

        $permisos = Permission::where('name', '!=', self::PERMISO_PROTEGIDO)
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permiso) {
                return $this->recursoDelPermiso($permiso->name);
            });

        return view('admin.permisos.index', compact('roles', 'permisos'));
    }

    /**
     * Guarda qué permisos tiene cada rol editable.
     */
    public function update(Request $request)
    {
        $roles = Role::whereIn('name', self::ROLES_EDITABLES)->get();

        // Casteamos a int: los checkboxes llegan como strings ("34"), y
        // syncPermissions() solo reconoce un id automáticamente si es un int
        // real de PHP — si le llega un string intenta buscarlo por NOMBRE
        // ("34") y explota con PermissionDoesNotExist.
        $idsPermitidos = Permission::where('name', '!=', self::PERMISO_PROTEGIDO)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        foreach ($roles as $role) {
            $seleccionados = collect($request->input("permisos.{$role->id}", []))
                ->map(fn ($id) => (int) $id)
                ->intersect($idsPermitidos)
                ->values()
                ->all();

            $role->syncPermissions($seleccionados);
        }

        return redirect()->route('permisos.index')->with('success', 'Permisos actualizados correctamente.');
    }

    /**
     * "agregar_bandera" -> "bandera", "ver_asistencia_propia" -> "asistencia",
     * agrupa por el recurso al que pertenece el permiso para mostrarlo
     * ordenado en la vista.
     */
    private function recursoDelPermiso(string $nombrePermiso): string
    {
        $prefijos = ['agregar_', 'editar_', 'eliminar_', 'ver_'];

        foreach ($prefijos as $prefijo) {
            if (str_starts_with($nombrePermiso, $prefijo)) {
                $recurso = substr($nombrePermiso, strlen($prefijo));
                return str_replace('_propia', '', $recurso);
            }
        }

        return 'otros';
    }
}
