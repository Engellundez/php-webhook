<?php
require_once 'logger.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
	protected $clients, $users;

	public function __construct()
	{
		$this->clients = new \SplObjectStorage;
		$this->users = [];
	}

	public function onOpen(ConnectionInterface $conn)
	{
		// Almacenar la nueva conexión en la lista de clientes
		$this->clients->attach($conn);
		Log::info("Nueva conexión! ({$conn->resourceId})");

		$queryParams = [];
		parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
		$userId = $queryParams['user_id'] ?? null;

		if ($userId) {
			$this->users[$conn->resourceId] = $userId;
			Log::info("Usuario {$userId} conectado ({$conn->resourceId})");
		} else {
			Log::warning("conexión sin ID de usuario ({$conn->resourceId})");
		}
	}

	public function onMessage(ConnectionInterface $from, $data)
	{
		$data = json_decode($data);
		switch ($data->type) {
			case 'all_users':
				$this->sendMessageToAllClients($data, $from->resourceId);
				break;
			case 'to_users':
				$this->sendMessageToUsers($data, $from->resourceId);
				break;

			default:
				$this->sendMessageToAllClients($data, $from->resourceId);
				break;
		}
	}

	public function onClose(ConnectionInterface $conn)
	{
		$userId = $this->users[$conn->resourceId];

		$this->clients->detach($conn);
		unset($this->users[$conn->resourceId]);

		Log::info("Usuario {$userId} desconectado ({$conn->resourceId})");
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		Log::error($e->getMessage());
		$conn->close();
	}

	// MARK: Regresar Información
	private function setStructureMessage($from = null, $message = null, $to = null)
	{
		// $to = NULL; => se envió a todos los usuarios
		// $to = [1,2,3]; => se envía los usuarios con esos ids
		$data = (object) ['from' => $from, 'message' => htmlentities($message), 'to' => $to];
		Log::info('Datos estructurados: ' . json_encode($data));
		return $data;
	}

	public function sendMessageToAllClients($data, $resourceId)
	{
		$userId = $this->users[$resourceId] ?? 'desconocido';
		$datos = $this->setStructureMessage($userId, $data->message);

		// Envía el mensaje a todos los clientes
		foreach ($this->clients as $client) {
			if ($client->resourceId != $resourceId) {
				$client->send(json_encode($datos));
			}
		}
	}

	public function sendMessageToUsers($data, $resourceId)
	{
		$userId = $this->users[$resourceId] ?? 'desconocido';
		$datos = $this->setStructureMessage($userId, $data->message, $data->to_users);


		$userToClientMap = array_flip($this->users);
		Log::info(json_encode($userToClientMap[2]));

		// Envía el mensaje a todos los usuarios especificados
		// foreach ($data->to_users as $targetUserId) {
		// 	if (isset($userToClientMap[$targetUserId])) {
		// 		$targetResourceId = $userToClientMap[$targetUserId];
		// 	}
		// }
	}
}
