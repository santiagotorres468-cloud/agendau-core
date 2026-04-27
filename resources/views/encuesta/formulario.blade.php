{{-- resources/views/encuesta/formulario.blade.php
     Encuesta post-tutoría: 5 calificaciones + resumen + aspectos a mejorar
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de satisfacción — Agenda U</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink-800: #002845;
            --ink-700: #003A5C;
            --accent-500: #C9A227;
            --surface-50: #F8FAFC;
            --surface-100: #F1F5F9;
            --surface-200: #E2E8F0;
            --text-900: #0F172A;
            --text-700: #334155;
            --text-500: #64748B;
            --text-400: #94A3B8;
            --danger-50: #FEF2F2;
            --danger-700: #991B1B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            font-family: 'Manrope', system-ui, sans-serif;
            background: var(--surface-50);
            color: var(--text-900);
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.01em;
            min-height: 100vh;
        }

        .container {
            max-width: 720px;
            margin: 0 auto;
            padding: 32px 16px 64px;
        }

        /* Header con identidad */
        .topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .logo {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: var(--ink-800);
            color: var(--accent-500);
            display: grid; place-items: center;
            font-weight: 800;
            font-size: 16px;
        }
        .brand-name { font-size: 14px; font-weight: 700; color: var(--ink-800); }
        .brand-tag { font-size: 11px; color: var(--text-500); }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(10,22,40,0.06);
            border: 1px solid var(--surface-200);
            overflow: hidden;
        }

        .card-header {
            background: var(--ink-800);
            color: white;
            padding: 32px 36px;
        }
        .card-header .eyebrow {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent-500);
            margin-bottom: 10px;
        }
        .card-header h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        .card-header p {
            font-size: 14px;
            color: rgba(255,255,255,0.75);
            font-weight: 500;
        }

        .info-bar {
            background: var(--surface-50);
            border-bottom: 1px solid var(--surface-200);
            padding: 18px 36px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.06em;
            color: var(--text-500);
            margin-bottom: 4px;
        }
        .info-item .value {
            font-size: 14px;
            font-weight: 600;
            color: var(--ink-800);
        }

        .form-body { padding: 36px; }

        .section-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--accent-500);
            margin: 0 0 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--surface-200);
        }

        .pregunta { margin-bottom: 28px; }
        .pregunta:last-of-type { margin-bottom: 0; }

        .pregunta .q-label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-900);
            margin-bottom: 4px;
        }
        .pregunta .q-help {
            display: block;
            font-size: 13px;
            color: var(--text-500);
            margin-bottom: 12px;
            font-weight: 500;
        }

        /* Estrellas */
        .estrellas {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 4px;
        }
        .estrellas input[type="radio"] { display: none; }
        .estrellas label {
            font-size: 32px;
            color: var(--surface-200);
            cursor: pointer;
            transition: color 0.12s, transform 0.1s;
            line-height: 1;
        }
        .estrellas label:hover,
        .estrellas label:hover ~ label,
        .estrellas input:checked ~ label {
            color: var(--accent-500);
        }
        .estrellas label:hover { transform: scale(1.1); }

        .escala-labels {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-400);
            font-weight: 500;
            margin-top: 6px;
            max-width: 200px;
        }

        /* Textareas */
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--surface-200);
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-900);
            resize: vertical;
            min-height: 90px;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: white;
        }
        textarea:focus {
            outline: none;
            border-color: var(--ink-800);
            box-shadow: 0 0 0 3px rgba(0,40,69,0.08);
        }

        .char-count {
            font-size: 11px;
            color: var(--text-400);
            text-align: right;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Bloque destacado para feedback */
        .feedback-block {
            background: var(--surface-50);
            border: 1px solid var(--surface-200);
            border-left: 3px solid var(--accent-500);
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 18px;
        }
        .feedback-block .icon-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: var(--ink-800);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .feedback-block .description {
            font-size: 12px;
            color: var(--text-500);
            margin-bottom: 12px;
            font-weight: 500;
        }

        .submit-row {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--surface-200);
        }
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-primary {
            background: var(--ink-800);
            color: white;
        }
        .btn-primary:hover {
            background: var(--ink-700);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,40,69,0.2);
        }
        .btn-ghost {
            background: white;
            color: var(--text-700);
            border: 1.5px solid var(--surface-200);
        }
        .btn-ghost:hover { background: var(--surface-50); color: var(--ink-800); }

        .error-banner {
            background: var(--danger-50);
            border: 1px solid #FECACA;
            color: var(--danger-700);
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .container { padding: 16px 12px 48px; }
            .card-header { padding: 24px; }
            .form-body { padding: 24px; }
            .info-bar { padding: 16px 24px; grid-template-columns: 1fr; gap: 12px; }
            .estrellas label { font-size: 28px; }
        }
    </style>
</head>
<body>

<div class="container">
    {{-- Identidad de marca --}}
    <div class="topbar">
        <div class="logo">A</div>
        <div>
            <div class="brand-name">Agenda U</div>
            <div class="brand-tag">Sistema de asesorías</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="eyebrow">Encuesta de satisfacción</div>
            <h1>¿Cómo te fue en la asesoría?</h1>
            <p>Tu opinión es anónima para los demás estudiantes y nos ayuda a mejorar el servicio.</p>
        </div>

        <div class="info-bar">
            <div class="info-item">
                <div class="label">Materia</div>
                <div class="value">{{ $reserva->horario->curso_nombre }}</div>
            </div>
            <div class="info-item">
                <div class="label">Docente</div>
                <div class="value">{{ $reserva->horario->docente_nombre }}</div>
            </div>
            <div class="info-item">
                <div class="label">Fecha</div>
                <div class="value">{{ \Carbon\Carbon::parse($reserva->fecha)->locale('es')->isoFormat('DD [de] MMMM') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('encuesta.guardar', $reserva->id) }}" class="form-body">
            @csrf

            @if($errors->any())
                <div class="error-banner">
                    Por favor revisa que hayas respondido todas las preguntas obligatorias.
                </div>
            @endif

            {{-- ═══════════ CALIFICACIONES ═══════════ --}}
            <div class="section-label">Califica la sesión</div>

            @php
                $preguntas = [
                    ['name' => 'p1_claridad',     'titulo' => '1. Claridad en las explicaciones',   'help' => '¿Qué tan claro fue el docente al explicar los temas?'],
                    ['name' => 'p2_puntualidad',  'titulo' => '2. Puntualidad y cumplimiento',      'help' => '¿El docente fue puntual y respetó el horario acordado?'],
                    ['name' => 'p3_dominio_tema', 'titulo' => '3. Dominio del tema',                'help' => '¿Demostró conocimiento profundo del tema tratado?'],
                    ['name' => 'p4_utilidad',     'titulo' => '4. Utilidad de la sesión',           'help' => '¿La asesoría fue útil para tu aprendizaje?'],
                    ['name' => 'p5_ambiente',     'titulo' => '5. Ambiente y condiciones',          'help' => '¿El espacio o plataforma virtual fue adecuado?'],
                ];
            @endphp

            @foreach($preguntas as $p)
                <div class="pregunta">
                    <label class="q-label">{{ $p['titulo'] }}</label>
                    <span class="q-help">{{ $p['help'] }}</span>
                    <div class="estrellas">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" name="{{ $p['name'] }}" id="{{ $p['name'] }}_{{ $i }}" value="{{ $i }}" {{ old($p['name']) == $i ? 'checked' : '' }} required>
                            <label for="{{ $p['name'] }}_{{ $i }}" title="{{ $i }} estrella(s)">★</label>
                        @endfor
                    </div>
                    <div class="escala-labels"><span>Muy malo</span><span>Excelente</span></div>
                </div>
            @endforeach

            {{-- ═══════════ RESUMEN DE LA SESIÓN ═══════════ --}}
            <div class="section-label" style="margin-top: 32px;">Resumen de la sesión</div>

            <div class="feedback-block">
                <div class="icon-wrap">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Resumen de lo trabajado
                </div>
                <div class="description">¿Qué temas o ejercicios trabajaron en esta asesoría? (opcional)</div>
                <textarea name="resumen_sesion" maxlength="1000" placeholder="Por ejemplo: Repasamos derivadas e integrales aplicadas a problemas de física, resolvimos 3 ejercicios del taller y aclaré dudas sobre el examen final.">{{ old('resumen_sesion') }}</textarea>
            </div>

            {{-- ═══════════ ASPECTOS A MEJORAR ═══════════ --}}
            <div class="feedback-block" style="border-left-color: var(--ink-800);">
                <div class="icon-wrap">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Aspectos a mejorar
                </div>
                <div class="description">Si hay algo que el docente podría mejorar, compártelo de forma constructiva (opcional)</div>
                <textarea name="aspectos_mejorar" maxlength="1000" placeholder="Por ejemplo: Sería útil contar con más ejemplos prácticos antes de los ejercicios, o tener material complementario para repasar después.">{{ old('aspectos_mejorar') }}</textarea>
            </div>

            {{-- ═══════════ COMENTARIO ADICIONAL ═══════════ --}}
            <div class="pregunta" style="margin-top: 16px;">
                <label class="q-label" style="font-size: 14px;">Comentario adicional</label>
                <span class="q-help">Cualquier otra cosa que quieras compartir (opcional)</span>
                <textarea name="comentario" maxlength="500" placeholder="Escribe aquí...">{{ old('comentario') }}</textarea>
            </div>

            <div class="submit-row">
                <a href="/estudiante" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    Enviar encuesta
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>