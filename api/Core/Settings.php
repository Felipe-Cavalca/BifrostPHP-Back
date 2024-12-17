<?php

/**
 * It is responsible for managing the system settings.
 *
 * @category Core
 * @copyright 2024
 */

namespace Bifrost\Core;

use Bifrost\Class\HttpError;

/**
 * It is responsible for managing the system settings.
 *
 * @package Bifrost\Core
 * @author Felipe dos S. Cavalca
 */
final class Settings
{
    /** It is responsible for controlling the initialization of the settings. */
    private static bool $initialized = false;

    /**
     * It is responsible for initializing the settings.
     *
     * @uses Settings::iniSet()
     * @return void
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * It is responsible for returning the value of the requested property.
     *
     * @param string $name The name of the property to be returned.
     * @uses Settings::getSettingsDatabase()
     * @uses Settings::getEnv()
     * @return mixed
     */
    public function __get($name): mixed
    {
        switch ($name) {
            default:
                return $this->getEnv($name);
        }
    }

    /**
     * It is responsible for returning the value of the requested property of the environment.
     *
     * @param string $param The name of the property to be returned.
     * @param bool $required Indicates whether the property is required.
     * @uses HttpError::__construct()
     * @return mixed
     */
    protected static function getEnv(string $param, bool $required = false): mixed
    {
        if ($required && !getenv($param)) {
            throw HttpError::internalServerError("The environment variable '{$param}' is required.");
        }

        return getenv($param) ?: null;
    }

    /**
     * It is responsible for setting the headers of the response.
     *
     * @return void
     */
    private static function setHeaders(): void
    {
        header("X-Powered-By: PHP/" . phpversion());
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Expose-Headers: Authorization");
    }

    /**
     * It is responsible for setting the PHP configuration.
     *
     * @uses Settings::getEnv()
     * @return void
     */
    private static function iniSet(): void
    {
        ini_set("display_errors", (bool)static::getEnv("PHP_DISPLAY_ERRORS") ?? false);
        ini_set("display_startup_errors", (bool)static::getEnv("PHP_DISPLAY_STARTUP_ERRORS") ?? false);
        ini_set('session.save_handler', static::getEnv("PHP_SESSION_SAVE_HANDLER") ?? "files");
        ini_set('session.save_path', static::getEnv("PHP_SESSION_SAVE_PATH") ?? "/var/lib/php/sessions");
        ini_set('session.gc_maxlifetime', static::getEnv("PHP_SESSION_GC_MAXLIFETIME") ?? 1440);
        ini_set('session.cookie_lifetime', static::getEnv("PHP_SESSION_COOKIE_LIFETIME") ?? 0);
    }

    /**
     * It is responsible for initializing the settings.
     *
     * @uses Settings::iniSet()
     * @uses Settings::setHeaders()
     * @uses Settings::$initialized
     * @return void
     */
    public static function init(): void
    {
        // Valida se já foi inicializado
        if (self::$initialized) {
            return;
        }

        static::iniSet();
        static::setHeaders();

        self::$initialized = true;
    }

    /**
     * It is responsible for returning the database settings.
     *
     * @param string $databaseName The prefix name of the database in the .env file
     * @uses Settings::getEnv()
     * @return array
     */
    public function getSettingsDatabase(string $databaseName = null): array
    {
        if (!empty($databaseName)) {
            $databaseName = strtoupper($databaseName) . "_";
        }

        $isNotSqlite = static::getEnv("{$databaseName}SQL_DRIVER", true) !== "sqlite";

        return [
            "driver" => static::getEnv("{$databaseName}SQL_DRIVER", true),
            "host" => static::getEnv("{$databaseName}SQL_HOST", $isNotSqlite),
            "port" => static::getEnv("{$databaseName}SQL_PORT", $isNotSqlite),
            "database" => static::getEnv("{$databaseName}SQL_DATABASE", true),
            "username" => static::getEnv("{$databaseName}SQL_USER", $isNotSqlite),
            "password" => static::getEnv("{$databaseName}SQL_PASSWORD", $isNotSqlite),
        ];
    }
}
