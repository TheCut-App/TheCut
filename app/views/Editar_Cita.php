<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Editar Cita</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
    <style>
        :root { --verde-bg: #0b1f18; --dorado: #d4af37; --dorado-oc: #8b733d; --texto: #e0e0e0; }
        body { 
            background-color: #000; color: var(--texto); font-family: 'Segoe UI', serif; 
            margin: 0; padding: 40px 20px; box-sizing: border-box; 
            /* Cambiamos height por min-height para que la página pueda crecer hacia abajo */
            min-height: 100vh; 
            display: flex; align-items: center; justify-content: center;
        }
        
        .editar-container {
            width: 100%; max-width: 1200px; 
            /* Eliminamos min-height fijo y dejamos que el contenido mande */
            height: auto; 
            margin: 0 auto; background: var(--verde-bg);
            border: 2px solid var(--dorado); border-radius: 8px; position: relative; 
            padding: 40px;
            display: flex; flex-direction: column; 
            box-sizing: border-box;
        }

        /* Este es el borde dorado interior, se ajustará siempre al tamaño del contenedor */
        .editar-container::before {
            content: ''; position: absolute; 
            top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 1px solid var(--dorado-oc); border-radius: 4px; 
            pointer-events: none;
        }

        .header-edit { text-align: center; border-bottom: 1px solid var(--dorado-oc); padding-bottom: 20px; margin-bottom: 40px; }
        .header-edit h1 { color: var(--dorado); font-size: 2.2rem; letter-spacing: 4px; margin: 0; text-transform: uppercase; }

        .layout-grid { display: grid; grid-template-columns: 350px 1fr; gap: 60px; flex-grow: 1; }

        /* Columna Izquierda */
        .col-izq { border-right: 1px solid rgba(139, 115, 61, 0.3); padding-right: 40px; display: flex; flex-direction: column; }
        .avatar-circulo {
            width: 140px; height: 140px; border-radius: 50%; background: transparent; 
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
            font-size: 3rem; color: var(--dorado); border: 2px solid var(--dorado);
        }
        .info-cliente-txt { text-align: center; margin-bottom: 40px; }
        .info-cliente-txt h2 { margin: 0; font-size: 1.6rem; color: white; }
        .info-cliente-txt p { font-size: 1rem; color: #aaa; margin: 8px 0; }
        
        .section-label { color: var(--dorado); font-size: 0.9rem; font-weight: bold; margin-bottom: 15px; display: block; text-transform: uppercase; letter-spacing: 1px; }
        .notas-area { width: 100%; background: transparent; border: 1px solid #444; color: #ccc; padding: 15px; height: 120px; border-radius: 4px; resize: none; font-family: inherit; font-size: 1rem; box-sizing: border-box; }

        /* Columna Derecha */
        .col-der { display: flex; flex-direction: column; }
        
        .lista-servicios-edit { background: transparent; max-height: 250px; overflow-y: auto; margin-bottom: 30px; padding-right: 10px; }
        .servicio-fila { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #222; cursor: pointer; transition: 0.2s; }
        .servicio-fila:hover { background: rgba(255,255,255,0.05); }
        .servicio-fila.principal { border: 1px solid var(--dorado); color: var(--dorado); background: rgba(212, 175, 55, 0.05); }

        .barberos-tabs { display: flex; gap: 10px; margin-bottom: 40px; }
        .tab-barbero { 
            flex: 1; padding: 15px; text-align: center; border: 1px solid #444; border-radius: 4px; 
            cursor: pointer; font-size: 0.9rem; transition: 0.2s; color: #aaa; text-transform: uppercase;
        }
        .tab-barbero.activo { background: var(--dorado); color: black; font-weight: bold; border-color: var(--dorado); }

        .datetime-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .input-edit { 
            width: 100%; background: transparent; border: 1px solid #444; color: white; 
            padding: 15px; border-radius: 4px; outline: none; font-size: 1.1rem; box-sizing: border-box;
            color-scheme: dark; /* Hace que el icono del calendario sea blanco */
        }

        /* Botones Abajo integrados */
        .footer-btns { 
            display: flex; justify-content: space-between; align-items: center; 
            border-top: 1px solid rgba(139, 115, 61, 0.3); 
            padding-top: 30px; 
            margin-top: 40px; /* Empuja los botones hacia abajo si hay poco contenido */
        }
        .btn-link-del { color: #ff6b6b; background: transparent; border: 1px solid #ff6b6b; padding: 12px 25px; border-radius: 4px; cursor: pointer; text-transform: uppercase; font-weight: bold; }
        .btn-link-del:hover { background: rgba(255, 107, 107, 0.1); }
        .btn-cancelar { color: #aaa; text-decoration: none; font-size: 1rem; margin-right: 30px; text-transform: uppercase; }
        .btn-guardar { 
            background: var(--dorado); color: black; border: none; padding: 15px 40px; 
            border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="editar-container">
    <div class="header-edit"><h1>EDITAR CITA</h1></div>

    <form action="index.php?accion=actualizar_cita" method="POST" style="display: flex; flex-direction: column; flex-grow: 1;">
        <input type="hidden" name="id_cita" value="<?= $datos['cita']['id'] ?>">
        
        <div class="layout-grid">
            <div class="col-izq">
                <span class="section-label">Cliente</span>
                <div class="avatar-circulo">
                    <?php 
                        $iniciales = substr($datos['cita']['cliente_nombre'], 0, 1) . substr($datos['cita']['cliente_apellido'], 0, 1);
                        echo strtoupper($iniciales);
                    ?>
                </div>
                <div class="info-cliente-txt">
                    <h2><?= $datos['cita']['cliente_nombre'] . ' ' . $datos['cita']['cliente_apellido'] ?></h2>
                    <p><?= $datos['cita']['cliente_tlf'] ?></p>
                    <p>Última visita: 12 Ene 2026</p>
                </div>

                <span class="section-label">Notas Internas</span>
                <textarea name="notas" class="notas-area" placeholder="Cliente prefiere navaja clásica..."><?= htmlspecialchars($datos['cita']['notas'] ?? '') ?></textarea>
            </div>

            <div class="col-der">
                <span class="section-label">Servicios Contratados</span>
                <div class="lista-servicios-edit">
                    <?php 
                        // Creamos un array simple con los IDs de los servicios que ya tiene contratados
                        $idsContratados = array_column($datos['cita']['servicios_contratados'], 'id');
                    ?>
                    
                    <?php foreach($datos['servicios'] as $srv): ?>
                        <?php $estaMarcado = in_array($srv['id'], $idsContratados); ?>
                        
                        <label class="servicio-fila <?= $estaMarcado ? 'principal' : '' ?>">
                            <div>
                                <input type="checkbox" name="servicios[]" class="check-servicio" value="<?= $srv['id'] ?>" data-duracion="<?= $srv['duracion'] ?>" <?= $estaMarcado ? 'checked' : '' ?> style="display: none;">
                                <span><?= htmlspecialchars($srv['nombre']) ?> (<?= $srv['duracion'] ?>m)</span>
                            </div>
                            <span style="font-weight: bold;"><?= number_format($srv['precio'], 2) ?>€</span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <span class="section-label">Profesional</span>
                <div class="barberos-tabs">
                    <?php foreach($datos['barberos'] as $b): ?>
                        <div class="tab-barbero <?= $b['id'] == $datos['cita']['id_usuario'] ? 'activo' : '' ?>" 
                             onclick="seleccionarBarbero(<?= $b['id'] ?>, this)">
                            <?= strtoupper($b['nombre']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="id_usuario" id="inputBarbero" value="<?= $datos['cita']['id_usuario'] ?>">

                <div class="datetime-grid">
                    <div>
                        <span class="section-label">Fecha</span>
                        <input type="date" name="fecha" class="input-edit" value="<?= date('Y-m-d', strtotime($datos['cita']['fecha_cita'])) ?>">
                    </div>
                    <div>
                        <span class="section-label">Hora Inicio</span>
                        <input type="time" name="hora" id="inputHoraInicio" class="input-edit" value="<?= date('H:i', strtotime($datos['cita']['fecha_cita'])) ?>">
                        <div style="text-align: right; color: #888; font-size: 0.9rem; margin-top: 10px;">
                            Hora fin estimada: <span id="horaFinEstimada" style="color: var(--dorado); font-weight: bold;">--:--</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-btns">
            <button type="button" class="btn-link-del" onclick="eliminarCita()">ELIMINAR CITA</button>
            <div>
                <a href="index.php?accion=admin" class="btn-cancelar">CANCELAR</a>
                <button type="submit" class="btn-guardar">GUARDAR CAMBIOS</button>
            </div>
        </div>
    </form>
</div>

<script>
    // Seleccionar Barbero
    function seleccionarBarbero(id, el) {
        document.querySelectorAll('.tab-barbero').forEach(t => t.classList.remove('activo'));
        el.classList.add('activo');
        document.getElementById('inputBarbero').value = id;
    }

    // Botón Eliminar
    function eliminarCita() {
        if(confirm('¿Seguro que quieres borrar esta cita definitivamente?')) {
            window.location.href = 'index.php?accion=eliminar_cita&id=<?= $datos['cita']['id'] ?>';
        }
    }

    // Calcular Hora Fin Dinámicamente
    function calcularHoraFin() {
        const inputHora = document.getElementById('inputHoraInicio');
        const spanHoraFin = document.getElementById('horaFinEstimada');
        
        let duracionTotalMinutos = 0;
        document.querySelectorAll('.check-servicio:checked').forEach(cb => {
            duracionTotalMinutos += parseInt(cb.dataset.duracion);
        });

        if (!inputHora.value || duracionTotalMinutos === 0) {
            spanHoraFin.innerText = '--:--';
            return;
        }
        
        const partesHora = inputHora.value.split(':');
        let fechaTemp = new Date();
        fechaTemp.setHours(parseInt(partesHora[0]), parseInt(partesHora[1]), 0);
        fechaTemp.setMinutes(fechaTemp.getMinutes() + duracionTotalMinutos);
        
        const hh = String(fechaTemp.getHours()).padStart(2, '0');
        const mm = String(fechaTemp.getMinutes()).padStart(2, '0');
        spanHoraFin.innerText = hh + ':' + mm;
    }

    // Eventos para recalcular el tiempo al marcar/desmarcar servicios
    document.querySelectorAll('.check-servicio').forEach(cb => {
        cb.addEventListener('change', function() {
            if(this.checked) {
                this.closest('.servicio-fila').classList.add('principal');
            } else {
                this.closest('.servicio-fila').classList.remove('principal');
            }
            calcularHoraFin();
        });
    });

    // Calcular hora y eventos iniciales
    document.getElementById('inputHoraInicio').addEventListener('input', calcularHoraFin);
    calcularHoraFin();
</script>

</body>
</html>