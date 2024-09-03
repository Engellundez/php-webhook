<?php

use Dotenv\Dotenv;
// Cargar las variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return [
	'WEBSOCKET_PORT' => $_ENV['WEBSOCKET_PORT'] ?? 'localhost',
	'SERVER_IP' => $_ENV['SERVER_IP'] ?? '8010',

	'JWT_SECRET' => $_ENV['JWT_SECRET'] ?? 'your_jwt_secret_key',

	'sqlsrv' => [
		'driver' => 'sqlsrv',
		'url' => $_ENV['DATABASE_URL'] ?? NULL,
		'host' => $_ENV['DB_HOST'] ?? 'localhost',
		'port' => $_ENV['DB_PORT'] ?? '1433',
		'database' => $_ENV['DB_DATABASE'] ?? 'forge',
		'username' => $_ENV['DB_USERNAME'] ?? 'forge',
		'password' => $_ENV['DB_PASSWORD'] ?? '',
		'charset' => 'utf8',
		'prefix' => '',
		'prefix_indexes' => true,
		// 'encrypt' =>$_ENV['DB_ENCRYPT', 'yes'),
		// 'trust_server_certificate' =>$_ENV['DB_TRUST_SERVER_CERTIFICATE', 'false'),
	],
];
