<?php

namespace App\Http\Controllers;

use App\Models\Guardavida;
use App\Http\Requests\StoreGuardavidaRequest;
use App\Http\Requests\UpdateGuardavidaRequest;
use App\Models\Playa;
use App\Models\Puesto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


class GuardavidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        //Encargados solo de su playa? ->los de  claro/Dunamar pueden verse?
        //if ($user->hasRole('admin')) {

        $guardavidas = Guardavida::
            select('guardavidas.*')
            ->join('playas', 'playas.id', '=', 'guardavidas.playa_id')
            ->join('puestos', 'puestos.id', '=', 'guardavidas.puesto_id')
            ->join('users', 'users.id', '=', 'guardavidas.user_id')
            ->orderByDesc('users.enabled')
            ->orderBy('playas.nombre')
            ->orderBy('puestos.nombre')
            ->orderBy('guardavidas.apellido')
            ->orderBy('guardavidas.nombre')
            ->with(['playa', 'puesto', 'user'])
            ->paginate(20);

            $guardavidasHabilitados = Guardavida::with('user', 'playa', 'puesto')
                ->whereHas('user', function ($query) {
                    $query->where('enabled', true);
                })
                ->get();



        //TODO solo habilitan/deshabilitan usuarios los admin o encargados tmb?

        $playas = Playa::all();

        return view('ui.guardavidas.index')
        ->with('registros', $guardavidas)
        ->with('playas', $playas)
        ->with('guardavidasHabilitados', $guardavidasHabilitados);
    }

    public function getAllDisabled(){
        $guardavidasDeshabilitados = Guardavida::with('user', 'playa', 'puesto')
            ->whereHas('user', function ($query) {
                $query->where('enabled', false);
            })
            ->get();

        $playas = Playa::all();

        return view('ui.guardavidas.disabled')
        ->with('guardavidasDeshabilitados', $guardavidasDeshabilitados)
        ->with('playas', $playas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();

        $guardavida = null;

        return view('ui.guardavidas.create', compact(
            'playas', 'puestos', 'guardavida'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuardavidaRequest $request)
    {
        //Validación de datos
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            // 1 Registro usuario
            $user = User::create([
                'name' => $validated['nombre'],
                'lastname' => $validated['apellido'],
                'email' => $validated['email'],
                'password' => bcrypt('123456789'),
            ]);

            // 2 Asigno rol
            if (auth()->user()->hasRole('encargado') && $validated['rol'] === "admin") {
                abort(403, 'No tenés permisos para crear un usuario con el rol administrador.');
            }
            $user->assignRole($validated['rol']);

            // 3 Si es guardavida o encargado → creo registro en tabla guardavidas
            if (in_array($validated['rol'], ['guardavida', 'encargado'])) {
                Guardavida::create([
                    'nombre'    => $validated['nombre'],
                    'apellido'  => $validated['apellido'],
                    'dni'       => $validated['dni'],
                    'telefono'  => $validated['telefono'],
                    'direccion' => $validated['direccion'],
                    'numero'    => $validated['numero'],
                    'piso_dpto' => $validated['piso_dpto'],
                    'playa_id'  => $validated['playa_id'],
                    'puesto_id' => $validated['puesto_id'],
                    'funcion'   => $validated['funcion'],
                    'user_id'   => $user->id,
                    // 'legajo' => $validated['legajo'] ?? null,
                ]);
            }
        });

        return redirect()
        ->route('guardavida.index')
        ->with('success', 'Guardavida creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guardavida $guardavida)
    {
        return view('ui.guardavidas.show-fields', compact(
           'guardavida'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guardavida $guardavida)
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;
        $rol = $guardavida->user?->getRoleNames()->first() ?? '';

        $playas = Playa::with('puestos')->get();

        return view('ui.guardavidas.edit', compact(
            'guardavidaAuth', 'rol', 'playas', 'guardavida'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuardavidaRequest $request, Guardavida $guardavida)
    {
        //Validación de datos
        $validated = $request->validated();

        //POR SI DEJO MODIFICAR NOMBRE-APELLIDO EN GUARDAVIDA TMB LO TNGO Q ACTUALIZAR EN USER
        //POR AHORA SOLO LO EDITA DE USER
        //DB::transaction(function () use ($validated, $guardavida) {
            //$user = $guardavida->user;
            //Actualiza usuario - saque nombre y apellido y lo deje unicamente en perfil
            // $user->update([
            //     'name'      => $validated['nombre'],
            //     'lastname'  => $validated['apellido'],
            // ]);
        //});

        if ($guardavida->update($validated)) {
            return redirect()->route('guardavida.edit', $guardavida->id)
                ->with('success', 'Guardavida actualizado correctamente.');
        }

        return back()->withErrors('No se pudo actualizar el guardavida. Intente nuevamente.');
    }


    /**
     * Update rol de usuario
     */
    public function updateUserRol(Request $request, User $user)
    {
        // Valida que venga un rol válido
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $nuevoRol = $validated['role'];
        $usuarioLogueado = auth()->user();

        //admin actualiza cualquier rol
        if($usuarioLogueado->hasRole('admin')){
            //Actualiza rol
            $user->syncRoles([$nuevoRol]);
            return back()->with('success', 'Rol actualizado correctamente.');
        }

        //Encargado actualiza guardavida/encargado
        if ($usuarioLogueado->hasRole('encargado')) {

            // no puede asignar o modificar admin
            if ($nuevoRol === 'admin' || $user->hasRole('admin')) {
                abort(403, 'No tenés permisos para asignar o modificar el rol administrador.');
            }

            // Solo puede actualizar entre roles permitidos
            if (in_array($nuevoRol, ['guardavida', 'encargado'])) {
                $user->syncRoles([$nuevoRol]);
                return back()->with('success', 'Rol actualizado correctamente.');
            }

            abort(403, 'No tenés permisos para asignar este rol.');
        }

        //por si otro rol quiere modificar
        abort(403, 'No tenés permisos para asignar este rol.');
    }



    /**
     * Por el momento, solo deshabilita al usuario
     */
    public function destroy(Guardavida $guardavida)
    {
        //
    }

    public function getAll(){
        $guardavidas = Guardavida::select('id', 'nombre', 'apellido')->get();

        return response()->json($guardavidas);
    }





    /**
     * Seleccion de guardavidas:
     * @param se recibe el id de identificacion del guardavidas que se desea seleccionar.
     *
     * @return  devuelve en el template el guardavidas seleccionado y encontrado por el identificador.
     *
     */

    public function seleccionarGuardavidaById($id)
    {

        $guardavida = Guardavida::showGuardavidaId($id);

        if ($guardavida != null) {
            return view('admin.usuarios.index', compact('guardavida'));
        }
    }





// AGREGAR ESTOS MÉTODOS AL FINAL DE TU GuardavidaController
// (antes del último })

/**
 * Ver perfil de un guardavidas específico
 * Puede ser visto por administradores o por el propio guardavidas
 */
public function showProfile(Guardavida $guardavida)
{
    $user = Auth::user();

    // Verificar permisos
    $esAdmin = $user->hasRole('admin') || $user->hasRole('encargado');
    $esPropietario = $user->guardavida && $user->guardavida->id === $guardavida->id;

    if (!$esAdmin && !$esPropietario) {
        abort(403, 'No tenés permisos para ver este perfil.');
    }

    $puedeEditar = $esAdmin || $esPropietario;

    // Obtener listas para los selects (solo si es admin)
    $playas = $esAdmin ? Playa::all() : null;
    $puestos = $esAdmin ? Puesto::all() : null;

    return view('ui.guardavidas.profile', compact(
        'guardavida',
        'puedeEditar',
        'esAdmin',
        'playas',
        'puestos'
    ));
}

/**
 * Mi perfil (guardavida logueado)
 */
public function myProfile()
{
    $user = Auth::user();

    if (!$user->guardavida) {
        return redirect()->route('home')
            ->with('error', 'No tenés un perfil de guardavida asignado.');
    }

    // Reutilizar el método anterior
    return $this->showProfile($user->guardavida);
}

/**
 * Actualizar perfil
 */
public function updateProfile(Request $request, Guardavida $guardavida)
{
    $user = Auth::user();

    // Verificar permisos
    $esAdmin = $user->hasRole('admin') || $user->hasRole('encargado');
    $esPropietario = $user->guardavida && $user->guardavida->id === $guardavida->id;

    if (!$esAdmin && !$esPropietario) {
        abort(403, 'No tenés permisos para editar este perfil.');
    }

    // Validación
    $rules = [
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'numero' => 'nullable|string|max:10',
        'piso_dpto' => 'nullable|string|max:10',
    ];

    // Solo admin puede cambiar playa/puesto/función
    if ($esAdmin) {
        $rules['playa_id'] = 'nullable|exists:playas,id';
        $rules['puesto_id'] = 'nullable|exists:puestos,id';
        $rules['funcion'] = 'nullable|string';
    }

    $validated = $request->validate($rules);

    if ($guardavida->update($validated)) {
        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    return back()->withErrors('No se pudo actualizar el perfil.');
}



    /*
    public function obtenerRol()
    {
        $rol = false;
        if (Auth::check() && Auth::user()->hasRole('jefe_guardavidas')) {
            $rol = true;
        }
        return response()->json($rol);
    }

*/
}
