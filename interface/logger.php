<?php

class Log
{
	const LOG_DIR = __DIR__ . '/logs';

	static private function saveLogMessage($message)
	{
		if (!is_dir(self::LOG_DIR)) {
			// Construimos la carpeta de logs en caso de no existir
			mkdir(self::LOG_DIR, 0777, true);
		}
		// obtenemos la fecha actual
		$datetime = new \DateTime();
		$currentDay = $datetime->format('d');
		$currentMonth = $datetime->format('m');

		// nombre del archivo con el día actual
		$logFile = self::LOG_DIR . '/log_' . $currentDay . '.txt';

		// Verificar si el archivo existe y si pertenece al mes actual
		if (file_exists($logFile)) {
			$firstLine = fgets(fopen($logFile, 'r'));
			preg_match('/\[(\d{4})-(\d{2})-(\d{2})/', $firstLine, $matches);

			// si el archivo pertenece a un mes diferente, se sobrescribe
			if ($matches && $matches[2] !== $currentMonth) {
				file_put_contents($logFile, '', LOCK_EX);
			}
		}

		// generamos el mensaje con microsegundos
		$logMessage = '[' . $datetime->format('Y-m-d H:i:s.u') . '] ' . $message . PHP_EOL;

		file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
	}

	static public function info($message)
	{
		$messageUpdate = 'INFO: ' . $message;
		self::saveLogMessage($messageUpdate);
	}

	static public function error($message)
	{
		$messageUpdate = 'ERROR: ' . $message;
		self::saveLogMessage($messageUpdate);
	}

	static public function warning($message)
	{
		$messageUpdate = 'WARNING: ' . $message;
		self::saveLogMessage($messageUpdate);
	}
}
