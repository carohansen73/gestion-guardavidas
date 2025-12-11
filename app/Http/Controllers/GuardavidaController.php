<?php

namespace App\Http\Controllers;

use App\Models\Guardavida;
use App\Http\Requests\StoreGuardavidaRequest;
use App\Http\Requests\UpdateGuardavidaRequest;
use App\Models\CambioDeTurno;
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
    public function index(Request $request)
    {
        $user = Auth::user();
        $playas = Playa::all();
        //Encargados solo de su playa? ->los de  claro/Dunamar pueden verse?
        //if ($user->hasRole('admin')) {

        // Paso los filtros de busqueda al back porque en el front se rompe con el paginado
        $guardavidas = $this->getlifeguardsLeaked($request);

        // Para mobile - solo muestro habilitados
        $guardavidasHabilitados = $this->getlifeguardsLeaked($request, true);

        return view('ui.guardavidas.index')
        ->with('registros', $guardavidas)
        ->with('playas', $playas)
        ->with('guardavidasHabilitados', $guardavidasHabilitados);
    }

    public function getlifeguardsLeaked($request, $enabledOnly = false){
        // Posibles filtros
        $search    = $request->input('search');
        $playaId   = $request->input('playa_id');
        $sortOrder = $request->input('sort', 'asc'); // asc o desc

        $query = Guardavida::select('guardavidas.*')
            ->join('playas', 'playas.id', '=', 'guardavidas.playa_id')
            ->join('puestos', 'puestos.id', '=', 'guardavidas.puesto_id')
            ->join('users', 'users.id', '=', 'guardavidas.user_id')
            ->with(['playa', 'puesto', 'user']);

        // FILTRO SOLO PARA HABILITADOS
        if ($enabledOnly) {
            $query->whereHas('user', function ($q) {
                $q->where('enabled', true);
            });
        }

        /* ----------------------
        FILTRO POR PLAYA
        ----------------------- */
        if ($playaId && $playaId !== "all") {
            $query->where('guardavidas.playa_id', $playaId);
        }

        /* ----------------------
        BÚSQUEDA GENERAL
        ----------------------- */
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('guardavidas.nombre', 'LIKE', "%$search%")
                ->orWhere('guardavidas.apellido', 'LIKE', "%$search%")
                ->orWhere('users.email', 'LIKE', "%$search%")
                ->orWhere('puestos.nombre', 'LIKE', "%$search%")
                ->orWhere('playas.nombre', 'LIKE', "%$search%");
            });
        }

        /* ----------------------
        ORDEN
        ----------------------- */
        $query->orderBy('guardavidas.apellido', $sortOrder)
            ->orderBy('guardavidas.nombre', $sortOrder);

        /* ----------------------
        PAGINACIÓN
        ----------------------- */
        $registros = $query->paginate(20)->appends([
            'search'   => $search,
            'playa_id' => $playaId,
            'sort'     => $sortOrder
        ]);

        return $registros;
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
        $user = Auth::user();
        if ($user->hasAnyRole(['guardavida', 'encargado']) ){
            $playas = Playa::where('id', $user->guardavida->playa_id)->get();
            $puestos = Puesto::where('playa_id', $user->guardavida->playa_id)->get();
        } else {
            $playas = Playa::all();
            $puestos = Puesto::orderBy('nombre')->get();
        }

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
                'must_change_password' => true,
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
                    'turno'   => $validated['turno'],
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

    /**
     * Esta funcion exige que el guardavidas, al loguearse por primera vez,
     *  si es que no tiene turno asignado (por lo que el puesto tmp esta corroborado),
     * Exige que el usuario actualize esta información.
     *
     * @param Request $request
     * @return void
     */
    public function setup(Request $request){
        //Valida puesto y turno para su actualización
        $request->validate([
            'puesto_id' => 'required|exists:puestos,id',
            'turno'     => 'required|in:M,T',
        ]);

        $user = Auth::user();
        if($user && $user->guardavida){
            $guardavida = $user->guardavida;

            // Actualiza puesto y turno
            $guardavida->update([
                'puesto_id' => $request->puesto_id,
                'turno'     => $request->turno,
            ]);

            // Limpia el popup (para no volver a mostrar)
            session()->forget('show_guardavida_setup');

            return redirect()->route('home')
                ->with('success', 'Puesto y turno configurados correctamente.');
        }


    }

    public function getAll(){
        $guardavidas = Guardavida::select('id', 'nombre', 'apellido')->get();

        return response()->json($guardavidas);
    }


    /**
     * Ver perfil de un guardavidas específico
     * Puede ser visto  por el propio guardavidas (loguarse como guardavidas)
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

    /*
            // Cargar relaciones necesarias
            $guardavida->load(['playa', 'puesto', 'turnos', 'funciones', 'user']);
    */
            // Cargar relaciones necesarias
            $guardavida->load(['playa', 'puesto','user']);
        // Obtener listas para los selects (solo si es admin)
        $playas = $esAdmin ? Playa::all() : null;
        $puestos = $esAdmin ? Puesto::all() : null;
        $turnos = $esAdmin ? CambioDeTurno::all() : null;
        return view('profile.profile', compact(
            'guardavida',
            'puedeEditar',
            'esAdmin',
            'playas',
            'puestos',
            'turnos'
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
 * Actualizar perfil del guardavidas logueado
 */
public function updateProfile(Request $request, Guardavida $guardavida)
{
    $user = Auth::user();

    // Verificar permisos                     //ajustar nombre si no coincide con el permiso
    $esAdmin = $user->hasRole('admin') || $user->hasRole('encargado');
    $esPropietario = $user->guardavida && $user->guardavida->id === $guardavida->id;

    if (!$esAdmin && !$esPropietario) {
        abort(403, 'No tenés permisos para editar este perfil.');
    }

        // Validación
        // Validación base (lo que puede editar cualquier usuario)
        $rules = [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'dni' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'piso_dpto' => 'nullable|string|max:10',
        ];
    // Solo admin puede cambiar playa/puesto/función/turno
    if ($esAdmin) {
        $rules['playa_id'] = 'required|exists:playas,id';
        $rules['puesto_id'] = 'required|exists:puestos,id';

        // agregar cuando haya tabla de funciones $rules['funcion'] = 'nullable|string';
    }

    $validated = $request->validate($rules);
        // Si no es admin, remover campos que no puede editar
        if (!$esAdmin) {


            unset($validated['playa_id'],
             $validated['puesto_id']);

        }
    if ($guardavida->update($validated)) {
            // También actualizar el usuario asociado si cambió nombre/apellido
            if ($guardavida->user) {
                $guardavida->user->update([
                    'name' => $validated['nombre'],
                    'lastname' => $validated['apellido'],
                    'email' => $validated['email'] ?? $guardavida->user->email,
                ]);
            }

            // Si es una petición AJAX, retornar JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'titulo' => 'Éxito',
                    'detalle' => 'Perfil actualizado correctamente.'
                ]);
            }

            return back()->with('success', [
                'titulo' => 'Éxito',
                'detalle' => 'Perfil actualizado correctamente.'
            ]);
        }

    return back()->withErrors('No se pudo actualizar el perfil.');

}

    public function obtenerPuestos($playa_id)
    {
        $puestos = Puesto::where('playa_id', $playa_id)->get();
        return response()->json($puestos);
    }
}
