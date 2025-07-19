<?php

namespace Bifrost;

require_once __DIR__ . '/Core/Autoload.php';

use Bifrost\Core\Queue;

syslog(LOG_INFO, 'Worker started');

while (true) {
    $queue = new Queue();
    $task = $queue->getNextTask();

    if ($task) {
        try {
            if ($task->run()) {
                // Ao terminar, remova da fila de processamento
                $queue->acknowledgeTask($task);
            } else {
                $queue->requeueTask($task);
            }
        } catch (\Throwable $e) {
            // Log, alertar, reempurrar a task para a fila original, etc.
            error_log('Erro na tarefa: ' . $e->getMessage());

            // Opcional: reencaminhar para a fila original para retry
            $queue->requeueTask($task);
        }
    } else {
        sleep(1);
    }
}
