@php
    $tempPost = $postularme['temporada'];
    $miPostulacion = $postularme['postulacion'];
    $estado = $miPostulacion?->estado;                  // null = todavía no la empezó
    $enviada = $miPostulacion && $estado !== 'borrador';
    $abierta = $tempPost->postulacionAbierta();
    $enCurso = ! $enviada;                               // sin empezar o borrador: toca hacer algo (naranja + progreso)
    $comentario = $enviada ? trim((string) $miPostulacion->observaciones) : '';

    // El color lo da el estado: naranja = tenés que hacer algo; el resto, el resultado.
    $tema = [
        'pendiente' => ['from-sky-600 to-blue-500 dark:from-sky-900 dark:to-blue-800', 'text-sky-700', 'text-sky-100'],
        'incompleta' => ['from-amber-600 to-yellow-500 dark:from-amber-900 dark:to-yellow-700', 'text-amber-700', 'text-amber-100'],
        'aceptada' => ['from-emerald-600 to-green-500 dark:from-emerald-900 dark:to-green-700', 'text-emerald-700', 'text-emerald-100'],
        'rechazada' => ['from-red-600 to-rose-500 dark:from-red-900 dark:to-rose-700', 'text-red-700', 'text-red-100'],
    ][$estado] ?? ['from-orange-600 to-amber-500 dark:from-orange-900 dark:to-amber-700', 'text-orange-700', 'text-amber-100'];

    $titulo = [
        'pendiente' => 'INSCRIPCIÓN ENVIADA',
        'incompleta' => 'FALTA COMPLETAR TU INSCRIPCIÓN',
        'aceptada' => 'INSCRIPCIÓN ACEPTADA',
        'rechazada' => 'INSCRIPCIÓN NO ACEPTADA',
    ][$estado] ?? 'INSCRIPCIÓN ABIERTA';

    $mensaje = match (true) {
        $estado === 'pendiente' => 'Recibimos tu inscripción. El equipo la está revisando.',
        $estado === 'incompleta' => 'El equipo te pidió completar o corregir algo de tu inscripción.',
        $estado === 'aceptada' && $miPostulacion->seleccionado => 'Fuiste seleccionado/a para trabajar esta temporada: ' . $miPostulacion->playaAsignada?->nombre . ($miPostulacion->puestoAsignado ? ' — ' . $miPostulacion->puestoAsignado->nombre : '') . '.',
        $estado === 'aceptada' => 'Tu inscripción fue aceptada. La selección de quienes trabajan la temporada es un paso posterior: te avisamos por acá.',
        $estado === 'rechazada' => 'Tu inscripción no fue aceptada para esta temporada.',
        default => 'Tus datos de la temporada anterior ya están precargados: solo revisalos y actualizá la documentación.',
    };

    // El botón solo existe mientras se puede empezar o corregir; ya revisada, la card es solo informativa.
    $puedeEditar = ! $miPostulacion ? $abierta : $miPostulacion->editablePorPostulante();
    $boton = ! $miPostulacion ? 'Postularme' : ($estado === 'borrador' ? 'Continuar postulación' : 'Revisar / corregir mi inscripción');
@endphp

<section class="relative overflow-hidden bg-gradient-to-r {{ $tema[0] }} rounded-3xl shadow-[var(--shadow-glass)] p-6 md:px-10 col-span-2 lg:row-span-2">
    <div class="relative grid gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(300px,.85fr)] lg:items-end">
        {{-- col 1 card --}}
        <div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold bg-white/20 text-white">
                    @if ($abierta)
                        Cierra el {{ $tempPost->fecha_fin_postulacion->format('d/m/Y') }}
                    @else
                        Inscripción cerrada
                    @endif
                </span>
            </div>

            {{-- TITULO --}}
            <div class="mt-4 flex items-start gap-4">
                <div class="bg-slate-50/10 ring-1 ring-white/20 backdrop-blur grid h-12 w-12 shrink-0 place-items-center rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="text-white  h-6 w-6" aria-hidden="true"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M12 11h4"></path><path d="M12 16h4"></path><path d="M8 11h.01"></path><path d="M8 16h.01"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold opacity-70 text-slate-100">{{ $tempPost->nombre }}</p>
                    <p class="font-display text-3xl font-bold leading-tight text-slate-50">{{ $titulo }}</p>
                </div>
            </div>

            <p class="mt-4 flex items-center gap-1.5 text-slate-100 text-sm {{ $enCurso ? 'opacity-70' : '' }}">
                {{ $mensaje }}
            </p>

            @if ($puedeEditar)
                <a href="{{ route('postulacion.paso', 1) }}"
                class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white/20 px-5 py-3.5 text-sm font-bold text-white ring-1 ring-white/30 transition hover:bg-white/30 sm:w-auto">
                    {{ $boton }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4" aria-hidden="true"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg>
                </a>
            @endif

        </div>


        {{-- col 2 card --}}
        @if ($enCurso)
            <div class="min-w-0 rounded-2xl bg-slate-50/10 p-3 ring-1 ring-white/20 backdrop-blur shadow-lg">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold opacity-70 text-slate-100">TU PROGRESO</p>

                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-white/20 text-white">
                        Estado: {{ ucfirst($estado ?? 'sin empezar') }}
                    </span>
                </div>

                @php
                    $pasos = [1 => 'Datos personales', 2 => 'Disponibilidad', 3 => 'Documentación', 4 => 'Revisar y enviar'];
                    // Sin inscripción empezada, todos los pasos figuran pendientes y el primero invita a arrancar.
                    $hechos = $miPostulacion?->pasosCompletos() ?? [1 => false, 2 => false, 3 => false, 4 => false];
                    $actual = array_search(false, $hechos, true); // primer paso incompleto (false si ya está todo)
                @endphp

                <ol class="mt-4 space-y-2">
                    @foreach ($pasos as $n => $label)
                        <li class="flex min-w-0 items-center gap-2 text-xs text-white">
                            @if ($hechos[$n])
                                <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full font-bold bg-emerald-400/25 text-emerald-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                                </span>
                                <span class="truncate opacity-70">{{ $label }}</span>
                            @elseif ($n === $actual)
                                <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full font-bold bg-white text-orange-700">{{ $n }}</span>
                                <span class="truncate font-semibold text-white">{{ $label }}</span>
                                <span class="ml-auto shrink-0 font-semibold text-amber-100">{{ $miPostulacion ? 'En curso' : 'Para empezar' }}</span>
                            @else
                                <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full font-bold bg-white/15 text-white/70">{{ $n }}</span>
                                <span class="truncate opacity-70">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @elseif ($comentario !== '')
            {{-- Comentario del equipo, en el mismo card de color --}}
            <div class="min-w-0 rounded-2xl bg-slate-50/10 p-4 ring-1 ring-white/20 backdrop-blur shadow-lg">
                <p class="text-sm font-semibold opacity-70 text-slate-100">COMENTARIO DEL EQUIPO</p>
                <p class="mt-2 whitespace-pre-line text-sm text-white">{{ $comentario }}</p>
            </div>
        @endif

    </div>
</section>
