Proyecto ISW-613 – Sistema Aventones

Manual del Script de Notificación de Reservas
Este documento describe el funcionamiento del script notificar_reservas.php, incluido en el sistema
Aventones. El objetivo de este script es automatizar el envío de correos electrónicos de recordatorio a
los choferes cuando existen reservas pendientes de aprobación que superan un tiempo determinado
(X minutos) desde su creación.

Ubicación del archivo
Ruta del script: C:\ISW613\httpdocs\Proyecto1\scripts\notificar_reservas.php
Ruta del log generado: C:\ISW613\httpdocs\Proyecto1\scripts\notificar_reservas.log

Descripción del proceso
1. El script se ejecuta desde consola (XAMPP Shell o CMD) con el comando:
php notificar_reservas.php
2. Conecta a la base de datos isw613_proyecto1.
3. Busca todas las reservas con estado PENDIENTE que tengan más de X minutos desde su creación.
4. Por cada reserva encontrada, se identifica el chofer correspondiente y se envía un correo de
notificación mediante PHPMailer.
5. Registra en el archivo notificar_reservas.log la fecha, hora y resultado del envío.
Ejemplo de salida en consola

■ Iniciando script de notificación... Script de notificación de reservas pendientes
Ejecutado: 2025-11-03 23:15:12 Tiempo de referencia: 1 minutos ■ Reservas encontradas:
1 ■ Notificación enviada a: choferprueba@gmail.com (viaje: Ride 1) Proceso completado.
Total de notificaciones enviadas: 1 Archivo de log:
C:\ISW613\httpdocs\Proyecto1\scripts\notificar_reservas.log
===============================================
Ejemplo de registro en el archivo LOG
[2025-11-03 23:15:12] Correo enviado a choferprueba@gmail.com (viaje: Ride 1)
[2025-11-03 23:15:13] Correo enviado a choferdemo@gmail.com (viaje: Viaje San José -
Alajuela)
Notas importantes
- El tiempo de referencia se puede modificar editando la variable $MINUTOS dentro del script.
- El envío de correos se realiza mediante la función enviarCorreoNotificacion() de PHPMailer,
usando la misma configuración SMTP que el sistema web.
- El script se puede programar para ejecución automática usando el Programador de Tareas de
Windows.
