<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TheCut - Punto de Venta</title>
    <link rel="stylesheet" href="public/assets/css/style_admin.css">
    <style>
        .venta-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            height: 75vh;
            margin-top: 20px;
        }
        .col-seccion {
            background-color: rgba(26, 29, 32, 0.8);
            border: 1px solid var(--dorado-artdeco);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .seccion-titulo {
            background: var(--gris-carbon);
            color: var(--dorado-artdeco);
            padding: 15px;
            text-transform: uppercase;
            font-size: 1.1rem;
            border-bottom: 1px solid var(--dorado-artdeco);
            text-align: center;
        }
        .lista-citas {
            overflow-y: auto;
            flex-grow: 1;
            padding: 10px;
        }
        .tarjeta-cita {
            background: #2a2a2a;
            border: 1px solid var(--dorado-artdeco);
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tarjeta-cita:hover { background: rgba(197, 160, 89, 0.1); }
        .tarjeta-cita.activo { background: var(--dorado-artdeco); color: var(--azul-profundo); }
        
        /* Zona del Ticket */
        .ticket-zona {
            padding: 30px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .ticket-info { margin-bottom: 20px; font-size: 1.2rem; }
        .ticket-total {
            font-size: 3rem;
            color: var(--dorado-artdeco);
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
        }
        .grid-botones-pago {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: auto;
        }
        .btn-pago {
            padding: 20px;
            font-size: 1.2rem;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-transform: uppercase;
        }
        .btn-efectivo { background: #0f664a; color: white; }
        .btn-tarjeta { background: #2b5797; color: white; }
    </style>
</head>
<body>
    <div class=\"admin-contenedor\">
        <header class=\"admin-cabecera\">
            <div class=\"cabecera-izq\">
                <img src=\"public/img/logo.png\" class=\"logo-pequeno\">
                <h1 class=\"titulo-admin\">PUNTO DE VENTA</h1>
            </div>
            <a href=\"index.php?accion=admin\" class=\"boton-dorado\" style=\"text-decoration: none; padding: 10px 20px;\">VOLVER AL PANEL</a>
        </header>

        <div class=\"venta-layout\">
            
            <div class=\"col-seccion\">
                <div class=\"seccion-titulo\">1. SELECCIONA CITA A COBRAR</div>
                <div class=\"lista-citas\" id=\"listaPendientes\">
                    <?php if (empty($datos['citas_pendientes'])): ?>
                        <p style=\"text-align: center; color: gray; margin-top: 20px;\">No hay citas pendientes de cobro para hoy.</p>
                    <?php else: ?>
                        <?php foreach($datos['citas_pendientes'] as $cita): ?>
                            <div class=\"tarjeta-cita\" 
                                 data-id=\"<?php echo $cita['id']; ?>\"
                                 data-cliente=\"<?php echo htmlspecialchars($cita['cliente_nombre'] . ' ' . $cita['cliente_apellido']); ?>\"
                                 data-barbero=\"<?php echo htmlspecialchars($cita['barbero_nombre']); ?>\"
                                 data-servicios=\"<?php echo htmlspecialchars($cita['servicios_nombres']); ?>\"
                                 data-total=\"<?php echo number_format($cita['precio_total'], 2); ?>\">
                                 
                                <div>
                                    <strong style=\"font-size: 1.1rem;\"><?php echo date('H:i', strtotime($cita['fecha_cita'])); ?> - <?php echo strtoupper($cita['cliente_nombre']); ?></strong><br>
                                    <small style=\"opacity: 0.8;\"><?php echo $cita['servicios_nombres']; ?></small>
                                </div>
                                <div style=\"font-size: 1.2rem; font-weight: bold;\">
                                    <?php echo number_format($cita['precio_total'], 2); ?> €
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class=\"col-seccion\">
                <div class=\"seccion-titulo\">2. DETALLE DEL COBRO</div>
                <div class=\"ticket-zona\">
                    <div id=\"ticketVacio\" style=\"text-align: center; color: gray; margin-top: 50px;\">
                        Selecciona un cliente de la lista para ver el total.
                    </div>
                    
                    <div id=\"ticketLleno\" style=\"display: none;\">
                        <div class=\"ticket-info\">
                            <p style=\"color: var(--dorado-artdeco); margin: 0 0 5px 0;\">CLIENTE:</p>
                            <p id=\"txtCliente\" style=\"margin: 0 0 15px 0; font-weight: bold;\"></p>
                            
                            <p style=\"color: var(--dorado-artdeco); margin: 0 0 5px 0;\">BARBERO:</p>
                            <p id=\"txtBarbero\" style=\"margin: 0 0 15px 0;\"></p>

                            <p style=\"color: var(--dorado-artdeco); margin: 0 0 5px 0;\">SERVICIOS:</p>
                            <p id=\"txtServicios\" style=\"margin: 0 0 15px 0;\"></p>
                        </div>

                        <div class=\"ticket-total\" id=\"txtTotal\">0.00 €</div>

                        <form action=\"index.php?accion=procesar_cobro\" method=\"POST\" id=\"formCobro\">
                            <input type=\"hidden\" name=\"id_cita\" id=\"inputIdCita\">
                            <input type=\"hidden\" name=\"metodo_pago\" id=\"inputMetodoPago\">
                            
                            <div class=\"grid-botones-pago\">
                                <button type=\"button\" class=\"btn-pago btn-efectivo\" onclick=\"realizarCobro('Efectivo')\">EFECTIVO</button>
                                <button type=\"button\" class=\"btn-pago btn-tarjeta\" onclick=\"realizarCobro('Tarjeta')\">TARJETA</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tarjetas = document.querySelectorAll('.tarjeta-cita');
            const ticketVacio = document.getElementById('ticketVacio');
            const ticketLleno = document.getElementById('ticketLleno');

            tarjetas.forEach(tarjeta => {
                tarjeta.addEventListener('click', function() {
                    // Quitar activo a todas y poner a la clickeada
                    tarjetas.forEach(t => t.classList.remove('activo'));
                    this.classList.add('activo');

                    // Rellenar datos del ticket
                    document.getElementById('txtCliente').innerText = this.dataset.cliente.toUpperCase();
                    document.getElementById('txtBarbero').innerText = this.dataset.barbero;
                    document.getElementById('txtServicios').innerText = this.dataset.servicios;
                    document.getElementById('txtTotal').innerText = this.dataset.total + ' €';
                    
                    // Preparar el input oculto para el formulario
                    document.getElementById('inputIdCita').value = this.dataset.id;

                    // Mostrar ticket
                    ticketVacio.style.display = 'none';
                    ticketLleno.style.display = 'block';
                });
            });
        });

        function realizarCobro(metodo) {
            if (confirm('¿Confirmar cobro de esta cita en ' + metodo + '?')) {
                document.getElementById('inputMetodoPago').value = metodo;
                document.getElementById('formCobro').submit();
            }
        }
    </script>
</body>
</html>