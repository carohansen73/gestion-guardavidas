<?php

namespace App\Http\Controllers;

use App\Enums\ColorRol;
use App\Models\Rol;
use App\Services\CatalogoRoles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Permiso que jamás se puede asignar desde esta pantalla, porque es
     * justamente el que da acceso a esta misma pantalla. Solo lo tiene el
     * rol superadmin, asignado a mano por SQL.
     */
    private const PERMISO_PROTEGIDO = 'abm_roles_y_permisos';

    /**
     * Roles que se pueden editar desde esta pantalla: los asignables, de mayor a menor rango. superadmin queda
     * afuera a propósito (se administra siempre a mano por SQL, así nadie puede auto-sacarse o sacarle a otro el
     * acceso a esta pantalla) y postulante también (no tiene permisos a propósito).
     */
    private function rolesEditables()
    {
        return Rol::where('asignable', true)->orderByDesc('rango')->orderBy('name');
    }

    /**
     * Muestra la matriz rol x permiso.
     */
    public function index()
    {
        $roles = $this->rolesEditables()
            ->with('permissions')
            ->get()
            ->keyBy('name');

        $permisos = Permission::where('name', '!=', self::PERMISO_PROTEGIDO)
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permiso) {
                return $this->recursoDelPermiso($permiso->name);
            });

        $colores = ColorRol::cases();
        $iconos = Rol::ICONOS;

        return view('permisos.index', compact('roles', 'permisos', 'colores', 'iconos'));
    }

    /**
     * Crea un rol nuevo. Nace sin permisos y sin tocar la selección de temporada (intocable) salvo que se indique;
     * después se le tildan los permisos en la matriz. El nombre es interno y no se cambia: la etiqueta sí.
     */
    public function storeRol(Request $request, CatalogoRoles $catalogo)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:40', 'regex:/^[a-z][a-z0-9_]*$/', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'etiqueta' => ['required', 'string', 'max:60'],
            'descripcion' => ['nullable', 'string', 'max:120'],
            'color' => ['required', Rule::enum(ColorRol::class)],
            'icono' => ['required', Rule::in(array_keys(Rol::ICONOS))],
            // Por debajo del administrador: solo un admin (o superior) puede asignarlo.
            'rango' => ['required', 'integer', 'between:1,79'],
        ], [
            'name.regex' => 'El nombre interno va en minúsculas, sin espacios ni tildes (letras, números y guion bajo).',
            'name.unique' => 'Ya existe un rol con ese nombre.',
        ]);

        Rol::create($datos + [
            'guard_name' => 'web',
            'es_plantel' => $request->boolean('es_plantel'),
            'usa_temporada' => $request->boolean('es_plantel') ? $request->boolean('usa_temporada') : true,
            'asignable' => true,
            // Un rol de temporada es el que la selección puede convertir en postulante; los demás quedan fuera de esos procesos.
            'intocable' => ! ($request->boolean('es_plantel') && $request->boolean('usa_temporada')),
        ]);
        $catalogo->olvidar();

        return redirect()->route('permisos.index')->with('success', "Rol «{$datos['etiqueta']}» creado. Tildale ahora los permisos que le correspondan.");
    }

    /**
     * Guarda qué permisos tiene cada rol editable.
     */
    public function update(Request $request)
    {
        $roles = $this->rolesEditables()->get();

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
