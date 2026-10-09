@extends('layouts.app')
@section('content')

@php
    $pasos = [
        ['n' => 1, 'icono' => 'calendar', 'titulo' => 'Crear la temporada', 'donde' => 'Temporada → Temporadas → Nueva'],
        ['n' => 2, 'icono' => 'users', 'titulo' => 'Se abre la inscripción', 'donde' => 'Automático, por fechas'],
        ['n' => 3, 'icono' => 'clipboard-check', 'titulo' => 'Revisar inscripciones', 'donde' => 'Temporada → Postulaciones'],
        ['n' => 4, 'icono' => 'shield-check', 'titulo' => 'Seleccionar (en tandas)', 'donde' => 'Postulaciones → Selección'],
        ['n' => 5, 'icono' => 'flag', 'titulo' => 'Cerrar la selección', 'donde' => 'Postulaciones → Selección → Cierre'],
        ['n' => 6, 'icono' => 'life-buoy', 'titulo' => 'Durante la temporada', 'donde' => 'Personal → Guardavidas'],
    ];
    $aviso = 'rounded-md bg-blue-50 dark:bg-blue-900/30 p-3 text-sm text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800';
    $alerta = 'rounded-md bg-yellow-50 dark:bg-yellow-900/30 p-3 text-sm text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800';
    $lista = 'list-disc pl-5 space-y-1 text-sm';
@endphp

<section class="sm:px-4 sm:py-10">
    <x-form-tarjeta tag="div">

        <x-form-encabezado titulo="Guía: temporada, postulaciones y selección" icono="life-buoy" etiqueta="Ayuda para administradores">
            Cómo se arma una temporada de principio a fin. Leela una vez de corrido; después sirve para consultar.
        </x-form-encabezado>

        {{-- Resumen --}}
        <x-form-seccion titulo="El circuito en 6 pasos" icono="clipboard-check">
            <ol class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pasos as $p)
                    <li>
                        <a href="#paso-{{ $p['n'] }}" class="flex h-full items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-sky-600 text-sm font-semibold text-white">{{ $p['n'] }}</span>
                            <span>
                                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $p['titulo'] }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $p['donde'] }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </x-form-seccion>

        {{-- 1 --}}
        <div id="paso-1" class="scroll-mt-24">
            <x-form-seccion titulo="1. Crear la temporada" icono="calendar" descripcion="Menú Temporada → Temporadas → Nueva.">
                <p class="text-sm">Hay dos pares de fechas, que no son lo mismo:</p>
                <ul class="{{ $lista }}">
                    <li><strong>Ventana de postulación:</strong> cuándo se puede completar el formulario (normalmente meses antes).</li>
                    <li><strong>Ventana operativa:</strong> la temporada en sí. Fuera de esas fechas, guardavidas y encargados no pueden cargar intervenciones, banderas, licencias, etc. (los admin sí). El fichaje por QR no se bloquea.</li>
                    <li><strong>Declaración jurada:</strong> subí el PDF modelo de ese año. Los postulantes lo descargan, lo firman y lo vuelven a subir. Si no subís ninguno, no se les pide.</li>
                </ul>
                <div class="{{ $aviso }}">
                    <strong>La inscripción se abre y se cierra sola</strong> según las fechas de postulación: no hay que hacer nada el día que empieza ni el que termina.
                    Pasada la fecha final nadie puede enviar ni modificar su inscripción. Si necesitás extender el plazo, editá la temporada y cambiá "Postulación hasta".
                </div>
            </x-form-seccion>
        </div>

        {{-- 2 --}}
        <div id="paso-2" class="scroll-mt-24">
            <x-form-seccion titulo="2. Qué hace el postulante" icono="users" descripcion="Mientras la inscripción está abierta.">
                <p class="text-sm">
                    Entra con su usuario (los nuevos se registran; los guardavidas actuales usan el aviso <strong>"Postularme"</strong> de su panel)
                    y completa 4 pasos: datos personales → playas y disponibilidad → documentos → revisar y enviar.
                </p>
                <ul class="{{ $lista }}">
                    <li>Puede guardar de a poco. <strong>Recién cuando toca "Enviar" la ves vos.</strong></li>
                    <li>Los datos fijos (talles, domicilio, etc.) se precargan de la vez anterior.</li>
                    <li>Mientras esté <strong>pendiente</strong> y dentro de la ventana puede corregir. Una vez <strong>aceptada o rechazada</strong> queda bloqueada.</li>
                </ul>
            </x-form-seccion>
        </div>

        {{-- 3 --}}
        <div id="paso-3" class="scroll-mt-24">
            <x-form-seccion titulo="3. Revisar inscripciones" icono="clipboard-check" descripcion="Menú Temporada → Postulaciones.">
                <p class="text-sm">
                    Arriba elegís la temporada; las solapas muestran cuántas hay por estado. Podés filtrar por playa o buscar por nombre/DNI.
                    Entrás a una inscripción para ver sus datos y abrir los documentos, y abajo elegís:
                </p>
                <ul class="{{ $lista }}">
                    <li><strong>Aceptada:</strong> queda habilitada para ser seleccionada.</li>
                    <li><strong>Rechazada:</strong> no avanza.</li>
                    <li><strong>Observaciones:</strong> el motivo o qué falta. <strong>La persona lo ve en su pantalla</strong>, redactalo pensando en que lo va a leer ella.</li>
                </ul>
                <p class="text-sm">Podés cambiar de opinión (de rechazada a aceptada, por ejemplo) hasta que la persona sea seleccionada.</p>
                <div class="{{ $aviso }}">
                    <strong>Aceptar no es seleccionar.</strong> Se pueden aceptar 320 inscripciones y seleccionar solo 200. Aceptar solo dice "está en condiciones".
                </div>
            </x-form-seccion>
        </div>

        {{-- 4 --}}
        <div id="paso-4" class="scroll-mt-24">
            <x-form-seccion titulo="4. Seleccionar (se puede hacer en tandas)" icono="shield-check" descripcion="Menú Temporada → Postulaciones → Selección de postulantes.">
                <p class="text-sm">
                    La selección se puede repetir cuantas veces haga falta: <strong>cada confirmación es independiente</strong>.
                    Sirve para el esquema real: una preselección chica en noviembre (los que arrancan primero) y el resto en diciembre o enero.
                </p>
                <ol class="list-decimal pl-5 space-y-2 text-sm">
                    <li><strong>Filtrá y tildá</strong> a las personas (por playa, por quienes ya fueron guardavidas, por nombre). La lista recuerda lo tildado aunque cambies de página o de filtro. Los ya seleccionados se ocultan solos.</li>
                    <li>
                        <strong>Continuá a la revisión</strong> y completá por persona:
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li><strong>Playa</strong> (obligatoria; viene con su primera opción).</li>
                            <li><strong>Puesto</strong> y <strong>turno</strong> (opcionales; sin puesto, lo elige el propio guardavida al ingresar).</li>
                            <li><strong>Encargado</strong> (tilde, si corresponde).</li>
                            <li><strong>Fecha de inicio</strong> de la tanda: desde ahí cuenta para el presentismo.</li>
                        </ul>
                    </li>
                    <li><strong>Confirmá.</strong> Cada persona queda como guardavida (o encargado) con acceso al sistema. Si ya existía de otro año, se actualiza su ficha, no se duplica.</li>
                </ol>
                <p class="text-sm">
                    ¿Seleccionaste a alguien por error? En la lista de seleccionados usá <strong>Sacar de la selección</strong>: vuelve a ser postulante y se borra su alta de esa temporada.
                    Si ya trabajó, no uses eso: usá la baja (paso 6).
                </p>
            </x-form-seccion>
        </div>

        {{-- 5 --}}
        <div id="paso-5" class="scroll-mt-24">
            <x-form-seccion titulo="5. Cerrar la selección (una sola vez, al final)" icono="flag" descripcion="Menú Postulaciones → Selección → Cierre.">
                <p class="text-sm">
                    Hasta que cierres, <strong>todos los guardavidas de la temporada anterior siguen activos</strong>.
                    El cierre pasa a <em>postulante</em> a los que <strong>no fueron seleccionados</strong> esta temporada.
                    Se conserva su ficha y todo su historial; solo dejan de aparecer en listados y asistencias.
                </p>
                <ul class="{{ $lista }}">
                    <li>Te muestra la lista; <strong>destildá a quien no quieras dar de baja</strong>, por ejemplo el <strong>personal del aeródromo</strong> u otro personal que no pasa por la inscripción.</li>
                    <li>Pedís una <strong>fecha de baja</strong> y confirmás con la casilla.</li>
                    <li>El sistema no deja cerrar si todavía no seleccionaste a nadie.</li>
                </ul>
                <div class="{{ $alerta }}">
                    <strong>No cierres entre tandas.</strong> Cerrá recién cuando terminaste de seleccionar a todos; si no, dejás afuera a quienes aún no elegiste.
                    <br><strong>Antes de confirmar el cierre, revisá la lista y destildá al personal del aeródromo.</strong>
                </div>
            </x-form-seccion>
        </div>

        {{-- 6 --}}
        <div id="paso-6" class="scroll-mt-24">
            <x-form-seccion titulo="6. Durante la temporada" icono="life-buoy" descripcion="Bajas, reincorporaciones y altas fuera de la inscripción.">
                <p class="text-sm font-semibold">Un guardavida deja de trabajar antes de que termine la temporada</p>
                <ul class="{{ $lista }}">
                    <li>En <strong>Guardavidas</strong>, usá el interruptor del listado: pide la <strong>fecha de baja</strong> (el último día que trabajó) y un motivo.</li>
                    <li>No se borra nada: conserva su historial, deja de figurar en listados y <strong>no se le computan faltas desde esa fecha</strong>. También se le cierra el acceso desde el celular.</li>
                    <li>No hace falta esperar al final de la temporada ni deshabilitarlo a mano.</li>
                </ul>

                <p class="text-sm font-semibold pt-2">Volver a incorporar a alguien dado de baja</p>
                <ul class="{{ $lista }}">
                    <li><strong>Guardavidas → Dados de baja → Reincorporar</strong>, con la fecha desde la que vuelve. Mantiene su playa y puesto anteriores.</li>
                </ul>

                <p class="text-sm font-semibold pt-2">Alta fuera de la inscripción</p>
                <ul class="{{ $lista }}">
                    <li>Se puede crear manualmente desde <strong>Guardavidas → Agregar</strong>. Si es posible, mejor que pase por la inscripción para tener sus documentos.</li>
                </ul>
            </x-form-seccion>
        </div>

        {{-- Qué transmitir --}}
        <x-form-seccion titulo="Qué tenés que transmitir" icono="users">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">A los postulantes</p>
                    <ul class="{{ $lista }}">
                        <li>Las fechas de inscripción: pasado el último día no pueden enviar ni corregir.</li>
                        <li><strong>Guardar no es enviar:</strong> hay que llegar al paso 4 y tocar "Enviar".</li>
                        <li>Tener a mano: foto personal, DNI frente y dorso, currículum, libreta, antecedentes penales, declaración jurada firmada y, si corresponde, licencia motonáutica.</li>
                        <li>Estar <strong>aceptado no es estar seleccionado</strong>; la selección puede llegar por tandas.</li>
                        <li>Revisar las observaciones si la inscripción vuelve con comentarios.</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">A los guardavidas de la temporada anterior</p>
                    <ul class="{{ $lista }}">
                        <li><strong>También tienen que postularse</strong> cada año (aviso "Postularme" en su panel). Si no, pueden quedar afuera en el cierre.</li>
                        <li>Hasta el cierre siguen trabajando normalmente.</li>
                    </ul>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">A los seleccionados</p>
                    <ul class="{{ $lista }}">
                        <li>Ya pueden ingresar con su usuario; si no se les asignó puesto, lo eligen en su primer ingreso.</li>
                        <li>Fuera de las fechas de la temporada solo pueden fichar, no cargar otros registros.</li>
                    </ul>
                </div>
            </div>
        </x-form-seccion>

        {{-- Checklist --}}
        <x-form-seccion titulo="Checklist rápido" icono="clipboard-check">
            <ul class="space-y-2 text-sm">
                @foreach ([
                    'Temporada creada con fechas de postulación y operativa',
                    'PDF de declaración jurada subido',
                    'Avisaste las fechas a guardavidas y postulantes',
                    'Revisaste y aceptaste/rechazaste las inscripciones',
                    'Seleccionaste por tandas (con playa y fecha de inicio)',
                    'Cerraste la selección (una sola vez, al final)',
                    'Bajas durante la temporada con la fecha real del último día',
                ] as $item)
                    <li class="flex items-start gap-2">
                        <x-form-icono nombre="shield-check" class="mt-0.5 h-4 w-4 shrink-0 text-sky-600" />
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </x-form-seccion>

    </x-form-tarjeta>
</section>
@endsection
