<?php

namespace Bifrost\Core;

use Bifrost\Interface\TaskInterface;
use Redis;

class Queue
{
    private static $redis;
    private static bool $enabled = true;
    private static string $queue = 'bifrost_queue';

    public function __construct()
    {
        if (empty(self::$redis)) {
            self::$queue = $settings->BFR_API_REDIS_QUEUE ?? 'bifrost_queue';
            self::conn();
        }
    }

    private static function conn(): void
    {
        $settings = new Settings();
        $host = $settings->BFR_API_REDIS_HOST;
        $port = $settings->BFR_API_REDIS_PORT;

        if (empty($host) || empty($port)) {
            self::$enabled = false;
            self::$redis = null;
            return;
        }

        try {
            self::$redis = new Redis();
            $connected = @self::$redis->connect($host, $port);
            if (!$connected) {
                self::$enabled = false;
                self::$redis = null;
            }
        } catch (\Throwable $e) {
            self::$enabled = false;
            self::$redis = null;
        }
    }

    /**
     * Adiciona uma tarefa no começo da fila (prioridade alta).
     */
    public function addToFront(TaskInterface $task): void
    {
        if (!self::$enabled) {
            $task->run();
            return;
        }

        $task = serialize($task);
        self::$redis->lpush(self::$queue, $task);
    }

    /**
     * Adiciona uma tarefa no final da fila (processamento normal).
     */
    public function addToEnd(TaskInterface $task): void
    {
        if (!self::$enabled) {
            $task->run();
            return;
        }

        $task = serialize($task);
        self::$redis->rpush(self::$queue, $task);
    }

    /**
     * Adiciona uma tarefa agendada para rodar daqui a X segundos.
     */
    public function addScheduledTask(TaskInterface $task, int $seconds): void
    {
        if (!self::$enabled) {
            $task->run();
            return;
        }

        $timestamp = time() + $seconds; // Calcula o tempo futuro
        $task = serialize($task);
        self::$redis->zAdd(self::$queue, $timestamp, $task);
    }

    /**
     * Obtém a próxima tarefa da fila.
     */
    public function getNextTask(): ?TaskInterface
    {
        if (!self::$enabled) {
            return null;
        }

        // Move da fila principal para a fila de processamento
        $taskData = self::$redis->rPopLPush(self::$queue, self::$queue . ':processing');

        if (!$taskData) {
            return null;
        }

        $task = unserialize($taskData);
        if (!$task instanceof TaskInterface) {
            throw new \RuntimeException('Tarefa inválida na fila');
        }

        return $task;
    }

    /**
     * Remove uma tarefa da fila de processamento, indicando que foi concluída.
     */
    public function acknowledgeTask(TaskInterface $task): void
    {
        self::$redis->lrem(self::$queue . ':processing', serialize($task), 1);
    }

    /**
     * Reinsere uma tarefa na fila principal para ser processada novamente.
     */
    public function requeueTask(TaskInterface $task): void
    {
        // Remove da fila de processamento e reinsere na fila principal
        self::$redis->lrem(self::$queue . ':processing', serialize($task), 1);
        self::$redis->lpush(self::$queue, serialize($task));
    }
}
