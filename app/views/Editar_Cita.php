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
            margin: 0; padding: 60px 20px; box-sizing: border-box; 
            min-height: 100vh; display: flex; align-items: flex-start; /* Evita que el título se suba */
            justify-content: center;
        }
        
        .editar-container {
            width: 100%; max-width: 1200px; height: auto; 
            background: var(--verde-bg); border: 2px solid var(--dorado); border-radius: 8px; 
            position: relative; padding: 40px; display: flex; flex-direction: column;
        }

        .editar-container::before {
            content: ''; position: absolute; top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 1px solid var(--dorado-oc); border-radius: 4px; pointer-events: none;
        }

        .header-edit { text-align: center; border-bottom: 1px solid var(--dorado-oc); padding-bottom: 20px; margin-bottom: 40px; }
        .header-edit h1 { color: var(--dorado); font-size: 2.2rem; letter-spacing: 4px; margin: 0; text-transform: uppercase; }

        .layout-grid { display: grid; grid-template-columns: 350px 1fr; gap: 60px; }

        /* Scrollbar humanizado para servicios */
        .lista-servicios-edit { background: transparent; max-height: 250px; overflow-y: auto; margin-bottom: 30px; padding-right: 10px; }
        .lista-servicios-edit::-webkit-scrollbar { width: 6px; }
        .lista-servicios-edit::-webkit-scrollbar-thumb { background: var(--dorado); border-radius: 10px; }

        .servicio-fila { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #222; cursor: pointer; }
        .servicio-fila.principal { border: 1px solid var(--dorado); color: var(--dorado); background: rgba(212, 175, 55, 0.05); }

        .barberos-tabs { display: flex; gap: 10px; margin-bottom: 40px; min-height: 60px; }
        .tab-barbero { 
            flex: 1; padding: 15px; text-align: center; border: 1px solid #444; border-radius: 4px; 
            cursor: pointer; font-size: 0.9rem; color: #aaa; text-transform: uppercase;
            display: flex; align-items: center; justify-content: center;
        }
        .tab-barbero.activo { background: var(--dorado); color: black; font-weight: bold; border-color: var(--dorado); }

        .section-label { color: var(--dorado); font-size: 0.9rem; font-weight: bold; margin-bottom: 15px; display: block; text-transform: uppercase; }
        
        .datetime-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .input-edit { width: 100%; background: transparent; border: 1px solid #444; color: white; padding: 15px; border-radius: 4px; font-size: 1.1rem; color-scheme: dark; }

        .footer-btns { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(139, 115, 61, 0.3); padding-top: 30px; margin-top: 40px; }
        .btn-link-del { color: #ff6b6b; background: transparent; border: 1px solid #ff6b6b; padding: 12px 25px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-guardar { background: var(--dorado); color: black; border: none; padding: 15px 40px; border-radius: 4px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="editar-container">
    <div class="header-edit"><h1>EDITAR CITA</h1></div>

    <form action="index.php?accion=actualizar_cita" method="POST">
        <input type="hidden" name="id_cita" value="<?= $datos['cita']['id'] ?>">
        
        <div class="layout-grid">
            <div class="col-izq">
                <span class="section-label">Cliente</span>
                <div class="avatar-circulo" style="width:120px; height:120px; border-radius:50%; border:2px solid var(--dorado); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:2.5rem;">
                    <?= strtoupper(substr($datos['cita']['cliente_nombre'], 0, 1) . substr($datos['cita']['cliente_apellido'], 0, 1)) ?>
                </div>
                <div style="text-align:center;">
                    <h2 style="margin:0;"><?= $datos['cita']['cliente_nombre'] ?></h2>
                    <p style="color:#888;"><?= $datos['cita']['cliente_tlf'] ?></p>
                </div>
                <span class="section-label" style="margin-top:30px;">Notas Internas</span>
                <textarea name="notas" class="notas-area" style="width:100%; height:100px; background:transparent; border:1px solid #444; color:white; padding:10px;"><?= htmlspecialchars($datos['cita']['notas'] ?? '') ?></textarea>
            </div>

            <div class="col-der">
                <span class="section-label">Servicios</span>
                <div class="lista-servicios-edit">
                    <?php 
                    $idsContratados = array_column($datos['cita']['servicios_contratados'], 'id');
                    foreach($datos['servicios'] as $srv): 
                        $marcado = in_array($srv['id'], $idsContratados);
                    ?>
                        <label class="servicio-fila <?= $marcado ? 'principal' : '' ?>">
                            <input type="checkbox" name="servicios[]" value="<?= $srv['id'] ?>" data-duracion="<?= $srv['duracion'] ?>" <?= $marcado ? 'checked' : '' ?> style="display:none;" class="check-servicio">
                            <span><?= $srv['nombre'] ?></span>
                            <span><?= $srv['precio'] ?>€</span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <span class="section-label">Profesional</span>
                <div class="barberos-tabs" id="contenedorBarberos">
                    <?php foreach($datos['barberos'] as $b): ?>
                        <div class="tab-barbero <?= $b['id'] == $datos['cita']['id_usuario'] ? 'activo' : '' ?>" onclick="seleccionarBarbero(<?= $b['id'] ?>, this)">
                            <?= strtoupper($b['nombre']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="id_usuario" id="inputBarbero" value="<?= $datos['cita']['id_usuario'] ?>">

                <span class="section-label">Fecha y Hora</span>
                <div class="datetime-grid">
                    <input type="date" name="fecha" id="inputFecha" class="input-edit" value="<?= date('Y-m-d', strtotime($datos['cita']['fecha_cita'])) ?>">
                    <div>
                        <input type="time" name="hora" id="inputHoraInicio" class="input-edit" value="<?= date('H:i', strtotime($datos['cita']['fecha_cita'])) ?>">
                        <div style="text-align:right; font-size:0.8rem; margin-top:5px; color:#888;">Fin est.: <span id="horaFin" style="color:var(--dorado);">--:--</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-btns">
            <button type="button" class="btn-link-del" onclick="eliminarCita()">ELIMINAR CITA</button>
            <div>
                <a href="index.php?accion=admin" style="color:#aaa; text-decoration:none; margin-right:20px;">CANCELAR</a>
                <button type="submit" class="btn-guardar">GUARDAR CAMBIOS</button>
            </div>
        </div>
    </form>
</div>

<script>
    function seleccionarBarbero(id, el) {
        document.querySelectorAll('.tab-barbero').forEach(t => t.classList.remove('activo'));
        el.classList.add('activo');
        document.getElementById('inputBarbero').value = id;
    }

    // Actualización dinámica de peluqueros
    document.getElementById('inputFecha').addEventListener('change', async function() {
        const res = await fetch(`index.php?accion=api_barberos_fecha&fecha=${this.value}`);
        const barberos = await res.json();
        const contenedor = document.getElementById('contenedorBarberos');
        const inputId = document.getElementById('inputBarbero');
        
        contenedor.innerHTML = '';
        barberos.forEach(b => {
            const div = document.createElement('div');
            div.className = 'tab-barbero' + (b.id == inputId.value ? ' activo' : '');
            div.innerText = b.nombre.toUpperCase();
            div.onclick = () => seleccionarBarbero(b.id, div);
            contenedor.appendChild(div);
        });
    });

    function calcularFin() {
        let mins = 0;
        document.querySelectorAll('.check-servicio:checked').forEach(c => mins += parseInt(c.dataset.duracion));
        const inicio = document.getElementById('inputHoraInicio').value;
        if (!inicio || mins === 0) return;
        let f = new Date();
        f.setHours(...inicio.split(':'));
        f.setMinutes(f.getMinutes() + mins);
        document.getElementById('horaFin').innerText = f.getHours().toString().padStart(2,'0') + ':' + f.getMinutes().toString().padStart(2,'0');
    }

    document.querySelectorAll('.check-servicio').forEach(c => c.addEventListener('change', function() {
        this.parentElement.classList.toggle('principal', this.checked);
        calcularFin();
    }));
    document.getElementById('inputHoraInicio').addEventListener('input', calcularFin);
    calcularFin();

    function eliminarCita() {
        if(confirm('¿Borrar cita?')) window.location.href = 'index.php?accion=eliminar_cita&id=<?= $datos['cita']['id'] ?>';
    }
</script>
</body>
</html>