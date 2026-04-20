<?php

class UsuarioController{

    private $usuario;
    private $cita;

    public function __construct() {
            $this->usuario = new Usuario();
            $this->cita = new Cita();

            //Si no esta con sesion iniciada, a login
            if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;

        }
    }

    //Verifica los datos que deberian llegar, si no se rellenan vacios
    public function guardar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? null;
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellido_1 = $_POST['apellido_1'] ?? '';
            $apellido_2 = $_POST['apellido_2'] ?? null;
            $is_admin   = isset($_POST['is_admin']) ? true : false; //Si no envia nada, falso por defecto
            $url_foto   = $_POST['url_foto'] ?? null;

        

            //Si detecta el id, pasa a actualizarlo, si no pasa a crearlo
            if ($id) {
                $this->usuario->actualizarUsuario($id, $nombre, $apellido_1, $apellido_2, $is_admin, $url_foto);
                return 'Usuario Actualizado';
            }
                $this->usuario->crearUsuario($username, $password, $nombre, $apellido_1, $apellido_2, $is_admin, $url_foto);
                return 'Usuario Creado';


        }
        
    } 

    public function mostrarPanelAdmin($fechaSeleccionada = null) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: index.php?error=Acceso+denegado");
        exit;
    }

    $fecha = $fechaSeleccionada ?? date('Y-m-d');
    
    $listaBarberos = $this->usuario->listarBarberos();
    
    $citasBrutas = $this->cita->citasTodosLosBarberosPorFecha($fecha);
    $totalHoy = $this->cita->citasTotalesHoy();
    
    $misCitasConteo = $this->cita->citasHoy($_SESSION['user_id']);

    $nombresBarberos = [];
        foreach($listaBarberos as $b) {
            $nombresBarberos[] = strtoupper($b['nombre']);
        }

    $datos = [
        'totales'      => $totalHoy,
        'mis_citas'    => $misCitasConteo,        
        'barberos'     => $nombresBarberos,
        'citas_grid'   => $this->formatearCitasParaGrid($citasBrutas, $listaBarberos),
        'fecha_actual' => $fecha
    ];

    return $datos;
}

private function formatearCitasParaGrid($citas, $listaBarberos) {
    $formateadas = [];

    // Creamos un diccionario rápido [id => NOMBRE EN MAYÚSCULAS]
    $diccionarioBarberos = [];
        foreach($listaBarberos as $b) {
            $diccionarioBarberos[$b['id']] = strtoupper($b['nombre']);
        }

    foreach ($citas as $c) {
        $hora_inicio = date('H:i', strtotime($c['fecha_cita']));
        // Si no viene hora_fin, asumimos que dura 30 minutos
        $hora_fin = isset($c['hora_fin']) ? date('H:i', strtotime($c['hora_fin'])) : date('H:i', strtotime($hora_inicio . ' + 30 minutes'));
        $formateadas[] = [
            'barbero'     => $diccionarioBarberos[$c['id_usuario']] ?? 'DESCONOCIDO',
            'hora_inicio' => $hora_inicio,
            'hora_fin'    => $hora_fin,
            'cliente'     => $c['cliente_nombre'] ?? 'Cliente',
            'servicio'    => $c['servicio_nombre'] ?? 'Servicio',
            'color'       => $c['color'] ?? null
        ];
    }
    return $formateadas;
}

}