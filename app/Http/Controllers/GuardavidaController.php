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
     * Ver perfil de un guardavidas específico
     * Puede ser visto por administradores o por el propio guardavidas dependiendo el rol por el que se ingrese
     *
     * @param int $id ID del guardavidas
     * @return view
     */
    public function verPerfil($id)
    {  //falta gregar la tabla que referencia a la funcion asi no  traemos todo junto y puede seccionarse
        $guardavida = Guardavidas::with(['puesto', 'turnos', 'playa', 'funciones'])->find($id);

        if (!$guardavida) {
            return redirect()->route('guardavidaslistado')->with('error', [
                'titulo' => '¡Error!',
                'detalle' => 'No se encontró el guardavidas solicitado.'
            ]);
        }

        // Verificar si el usuario actual es admin o es el propio guardavidas
        $esAdmin = Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('jefe_guardavidas'));
        $esPropietario = Auth::check() && Auth::id() == $guardavida->id;
        $puedeEditar = $esAdmin || $esPropietario;

        // Obtener listas para los selects (solo si puede editar)
        $puestos = $puedeEditar ? Puesto::all() : null;
        $balnearios = $puedeEditar ? Playa::all() : null;

        return view('profile.perfilGuardavidas', compact('guardavida', 'puedeEditar', 'esAdmin', 'puestos', 'balnearios'));
    }

    /**
     * Ver mi perfil (guardavidas logueado)
     *
     * @return view
     */
    public function miPerfil()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Asumiendo que el modelo User tiene relación con Guardavidas
        $guardavida = Guardavida::where('user_id', Auth::id())
            ->with(['puesto', 'turnos', 'balnearios', 'funciones'])
            ->first();

        if (!$guardavida) {
            return redirect('/')->with('error', [
                'titulo' => '¡Error!',
                'detalle' => 'No se encontró tu perfil de guardavidas.'
            ]);
        }

        $puedeEditar = true;
        $esAdmin = false;
        $puestos = null;
        $balnearios = null;

        return view('profile.perfilGuardavidas', compact('guardavida', 'puedeEditar', 'esAdmin', 'puestos', 'balnearios'));
    }






/***************************metodos para el blade vista del admin en relacion a lo que visualiza y modifica de guardavidas  */

    /**
     * Actualizar perfil de guardavidas
     *
     * @param int $id
     * @param Request $request
     * @return redirect
     */
    public function actualizarPerfil($id, Request $request)
    {
        $guardavida = Guardavidas::find($id);

        if (!$guardavida) {
            return response()->json([
                'success' => false,
                'titulo' => '¡Error!',
                'detalle' => 'No se encontró el guardavidas.'
            ], 404);
        }

        // Verificar permisos
        $esAdmin = Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('jefe_guardavidas'));
        $esPropietario = Auth::check() && Auth::id() == $guardavida->user_id;

        if (!$esAdmin && !$esPropietario) {
            return response()->json([
                'success' => false,
                'titulo' => '¡Error!',
                'detalle' => 'No tienes permisos para editar este perfil.'
            ], 403);
        }

        // Validación
        $rules = [

            'apellido' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'dni' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'ciudad' => 'nullable|string|max:255',
            'calle' => 'nullable|string|max:255',
            'direccion' => 'nullable|integer|max:20',

        ];

        // Solo admin puede cambiar puesto y balneario
        if ($esAdmin) {
            $rules['puesto_id'] = 'nullable|exists:puestos,id';
            $rules['balneario_id'] = 'nullable|exists:balnearios,id';
        }

        $validated = $request->validate($rules);

        // Actualizar datos
        $guardavida->apellido = $validated['apellido'];
        $guardavida->nombre = $validated['nombre'];
        $guardavida->email = $validated['email'] ?? null;
        $guardavida->dni = $validated['dni'];
        $guardavida->telefono = $validated['telefono'] ?? null;
        $guardavida->ciudad = $validated['ciudad'] ?? null;
        $guardavida->calle = $validated['calle'] ?? null;
        $guardavida->direccion = $validated['direccion'] ?? null;
        if ($esAdmin) {
            $guardavida->puesto_id = $validated['puesto_id'] ?? $guardavida->puesto_id;
            $guardavida->balneario_id = $validated['balneario_id'] ?? $guardavida->balneario_id;
        }

        if ($guardavida->save()) {
            return response()->json([
                'success' => true,
                'titulo' => '¡Actualizado!',
                'detalle' => 'El perfil se actualizó correctamente.'
            ]);
        }

        return response()->json([
            'success' => false,
            'titulo' => '¡Error!',
            'detalle' => 'No se pudo actualizar el perfil.'
        ], 500);
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
            return view('auth.guardavidasListado', compact('guardavida'));
        }
    }

    /**
     * Funcion de asignacion de guardavidas por puesto.
     * @param recibe $request para l avalidacion de los datos antes de la asignacion.
     * Se valida :$guardavida_id-> referencia al identificador del guardavida , $puesto_id->referencia al puesto existente en que se encunetre.
     *
     * @return mensaje de exito o falla en relacion al resultado de la asignacion
     *
     */

    public function asignarGuardavidaAPuesto(Request $request)
    {
        $validated = $request->validate([
            'guardavida_id' => 'required|exists:guardavidas,id',
            'puesto_id' => 'required|exists:puestos,id',
        ]);

        $guardavida = Guardavidas::find($validated['guardavida_id']);
        $guardavida->puesto_id = $validated['puesto_id'];
        $guardavida->save();

        return redirect()->back()->with('success', [
            'titulo' => '¡Asignado!',
            'detalle' => 'El guardavidas fue asignado correctamente al puesto.'
        ]);
    }


    /**
     * Funcion para renderizar el formulario de asignacion de guardavidas a puestos en cada playa del distrito.
     * @return devuelve dentro del template el listado de guardavidas por puestos.
     */
    //muestro las asignaciones de los guardavidas en el template
    public function showFormAsignarGuardavida()
    {
        $guardavidas = Guardavidas::all();
        $puestos = Puestos::all();

        return view('auth.guardavidasListado', compact('guardavidas', 'puestos'));
    }


    //registrar licencias (creo que va en otro controller depende quien puede cargar licencias)




    /**
     *  Ver guardavidas asignados a turnos y por puestos en cada balneario.
     *  Se obtiene desde el model (Guardavidas) el listado en relacion con puesto,turno y balneario.
     * Luego se renderiza al template donde figura el listado y se le pasa la informacion obtenida desde el model.
     *
     */

    protected function guardavidasPorPuestoyTurno()
    {
        $guardavidas = Guardavidas::with(['puesto', 'turnos', 'balnearios'])->get();

        // Si quieres agrupar por balneario o puesto, hazlo en el modelo o con Collection
        return view('auth.guardavidasListado', compact('guardavidas'));
    }



    /**
     * Saber que hace cada quien segun su funcion
     *
     * Se obtiene de desde el modelo (Guardavidas) agrupados por funcion.
     * Se envia los datos obtenidos al template del listado para guardavidas.
     */


    public function obtenerFuncion()
    {
        $guardavidas = Guardavidas::with('funciones')->get();
        return view('auth.guardavidasListado', compact('guardavidas'));
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


    /*** para futuros filtros de busquedas nombre, por balneario , puesto y turnos ***/


    /**
     * Filtro de busqueda de guardavidas por nombre
     * @param $request se recibe el nombre de guardavida que se necesita encontrar dentro del sistema.
     * Se compara con lo guardado en la bd.
     * @return se devuelve un json con  el resultado de la busqueda.
     * */
    public function filterGuardavidasByName(Request $request)
    {
        $busqueda = $request->query('busqueda');
        $guardavidas = Guardavidas::with(['balnearios', 'puestos'])
            ->where('nombre', 'LIKE', '%' . $busqueda . '%')

            ->get();
        return response()->json($guardavidas);
    }
}
