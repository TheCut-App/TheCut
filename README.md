# TheCut - Gestión Integral de Barberías

Bienvenido al repositorio de **TheCut**. Somos David Romo y Alejandro Cuesta, y este es nuestro proyecto final para el ciclo de 2º de DAW. 

Hemos desarrollado una plataforma web pensada para digitalizar por completo el día a día de una barbería. La idea surge de una necesidad real del sector: la mayoría de negocios pequeños siguen tirando de agendas de papel o de programas genéricos que no se adaptan a su forma de trabajar. Nosotros hemos creado una solución rápida, intuitiva y con una estética Premium/Vintage muy cuidada.

---

## ¿Qué hace TheCut?

Esta es la versión 1.0 (nuestro Producto Mínimo Viable) y cuenta con las siguientes funcionalidades clave:

* **Agenda inteligente:** Un calendario que calcula automáticamente cuánto dura cada servicio para que los barberos no se pisen las horas.
* **TPV unificado:** Una pantalla de venta donde se pueden cobrar servicios y productos de inventario del tirón, en un mismo ticket y calculando el IVA al vuelo.
* **Inventario dinámico:** Control de stock en tiempo real. Si te quedas sin una cera o champú, el sistema te avisa visualmente.
* **Gestión de roles:** El sistema diferencia si entra el administrador (que ve toda la facturación), un empleado, o un usuario invitado.

---

## Cómo lo hemos construido (Stack Tecnológico)

No hemos querido depender de frameworks pesados, así que hemos montado toda la arquitectura desde cero:

* **Backend:** PHP 8.2 estructurado bajo un patrón MVC (Modelo-Vista-Controlador) puro. Nada de código espagueti; las vistas por un lado y las consultas por otro.
* **Base de Datos:** PostgreSQL. Hemos usado Supabase como Backend as a Service para alojarla en la nube y nos conectamos mediante PDO con sentencias preparadas.
* **Frontend:** HTML5, CSS3 (usando Flexbox y CSS Grid a fondo para que sea 100% responsive) y Vanilla JavaScript. Hemos usado la Fetch API para hacer peticiones asíncronas (como calcular huecos de agenda) sin tener que recargar la página.
* **Prototipado:** Todo el diseño y los wireframes nacieron primero en Canva.
* **Infraestructura:** Todo el entorno está dockerizado para evitar problemas de compatibilidad entre equipos.

---

## Guía de instalación rápida

Para levantar el proyecto en tu máquina local, solo necesitas tener instalado Git y Docker Desktop. Vamos al lío:

1. Clona este repositorio en tu equipo:
   ```bash
   git clone [https://github.com/tu-organizacion/thecut.git](https://github.com/tu-organizacion/thecut.git)
   cd thecut
Levanta los contenedores con Docker Compose:

Bash
docker-compose up -d --build
Abre tu navegador de confianza y entra en:
http://localhost:8888