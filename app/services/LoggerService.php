<?php

namespace App\Services;

class LoggerService
{
    const LOG_DIR = __DIR__ . '/../../logs';
    private static function writeLog($level, $message)
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
        $logFile = self::LOG_DIR . '/log_' . $currentDay . '.log';

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
        $logMessage = '[' . $datetime->format('Y-m-d H:i:s.u') . '] ' . $level . ': ' . $message . PHP_EOL;

        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    public static function info($message)
    {
        self::writeLog('INFO', $message);
    }

    public static function error($message)
    {
        self::writeLog('ERROR', $message);
    }

    public static function warning($message)
    {
        self::writeLog('WARNING', $message);
    }
}
