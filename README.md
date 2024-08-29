# Activar Webhook

## Configuración

Para poder activar el webhook es necesario entrar a la carpeta del webhook y correr el siguiente comando

```bash
composer install
```

Una vez instalado las dependencias vamos a actualizar la ruta de este proyecto en el archivo `run_webhook.bat` para activar el servicio de Webhook

## Activar el Webhook

### Windows

Para poder activar la configuración del .bat es necesario activar el programador de tareas en windows _(Task Scheduler)_
Una vez abierto el Programador de tareas en el panel derecho vamos a seleccionar `crear tarea`

##### Pestaña General

-   Asignamos el nombre de la tarea mo `Inicar Weebhook PHP`
-   Seleccionamos `Ejecutar si el usuario ha iniciado sesión o no`
-   Marcamos la opción `Ejecutar con los privilegios más altos.`

#### Desencadenadores

-   Creamos una nuevo.
-   Seleccionamos `Al iniciar el sistema.`
-   y aceptamos los cambios

#### Acciones

-   Creamos una nueva acción
-   En `Programa/script` vamos a seleccionar nuestro .bat `run_webhook.bat`
-   Aceptamos los cambios

#### Condiciones

-   desmarcamos la opción `iniciar la tarea solo si el equipo está conectado a la corriente alterna`

Con esos pasos vamos a dejar la tarea programada y funcionando solo faltaría reiniciar el sistema para verificar si el webhook esta activo
(Creara un archivo en la carpeta `/logs`)

### IIS (No recomendado)

-   Configuramos el sistema de webhook como un nuevo sub-aplicativo para usar el puerto propio del proyecto
-   añadimos la ruta del webhook para que lo encuentre el IIS
-   Definimos un puerto para activarlo (Al que consultarian el proyecto (WEBHOOK Y PUERTO EN EL PROYECTO));
-   Entramos a consultar el servicio del sub aplicativo
-   Verificamos si los logs guardan el dato.

## Configuración del WebHook en el proyecto

Para poder ejecutar nuestro código es necesario activar el webhook con JS para abrir esta conexión y que se este ejecutando constantemente para escuchar lo que pasa en el sistema

```Javascript
<script>
	// ip of server for webhook
	let ip_conn = '172.16.9.86';
	let port_conn = '8010';
	// id user to connect
	let user_id = "{{ Auth()->user()->id }}";
	let url = `ws://${ip_conn}:${port_conn}?user_id=${user_id}`;

	let conn = new WebSocket(url);

	conn.onopen = function(e) {
		console.log("Conexión establecida!");
	};

	conn.onmessage = function(e) {
		console.log(e.data);
	};

	function sendMessage(message = null, type = "all_users", to_users = []) {
		data = {
			"type": type, // ["all_users","to_users"]
			"to_users": to_users,
			"message": message
		}
		conn.send(JSON.stringify(data));
	}
</script>
```

## Apagar Tarea para pruebas

vamos a ingresar nuevamente al `Programador de tareas`
y vamos a seleccionar `Biblioteca del Programador de tareas`. Esta acción nos va a abrir en el panel central las tareas activas, vamos a buscar `Inicar Weebhook PHP` y vamos a dar clic en `Finalizar` para terminar la tarea en ese momento ó
`Deshabilitar` para que no se active automáticamente hasta que lo decida el usuario.
