<?php

/**
 * It is responsible for managing the connection to the database.
 *
 * @category Core
 * @copyright 2024
 */

namespace Bifrost\Core;

use PDO;
use PDOException;
use Bifrost\Core\Settings;
use Bifrost\Class\HttpError;

/**
 * It is responsible for managing the connection to the database.
 *
 * @package Bifrost\Core
 */
class Database
{
    /** It is responsible for storaging the connection to the database. */
    private static array $conections;
    private PDO $conn;
    /** It is responsible for storaging the system settings. */
    private static Settings $settings;
    /** It is responsible for storaging the driver of the database. */
    public string $driver;

    /**
     * It is responsible for initializing the connection to the database.
     *
     * @param string $databaseName The prefix name of the database in the .env file
     * @uses Settings
     * @uses Database::conn()
     * @return void
     */
    public function __construct(string $databaseName = null)
    {
        if (empty(self::$settings)) {
            self::$settings = new Settings();
        }

        if (empty(self::$conections[$databaseName])) {
            self::$conections[$databaseName] = $this->conn($databaseName);
        }

        $this->conn = self::$conections[$databaseName];
    }

    /**
     * It is responsible for returning the connection to the database.
     *
     * @param string $databaseName The prefix name of the database in the .env file
     * @uses Settings
     * @uses PDO
     * @uses Database::$conn
     * @return PDO
     */
    private function conn(string $databaseName = null): PDO
    {
        $dataConn = self::$settings->getSettingsDatabase($databaseName);
        $this->driver = $dataConn["driver"];

        switch ($dataConn["driver"]) {
            case "sqlite":
                return new PDO("sqlite:" . $dataConn["database"]);
            case "mysql":
                return new PDO(
                    "mysql:host={$dataConn["host"]}:{$dataConn["port"]};dbname={$dataConn["database"]};charset=utf8",
                    $dataConn["username"],
                    $dataConn["password"]
                );
            case "pgsql":
            default:
                $pdo = new PDO(
                    "pgsql:host={$dataConn["host"]};port={$dataConn["port"]};dbname={$dataConn["database"]}",
                    $dataConn["username"],
                    $dataConn["password"]
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
        }
    }

    /**
     * It is responsible for returning the WHERE clause of the SQL query.
     *
     * @param array $conditions Array of conditions where the key is the field name and the value is the field value.
     * @return string
     *
     * @example
     * $conditions = ['id' => 1, 'name' => 'John'];<br>
     * $whereClause = $this->where($conditions);<br>
     * // Result: "id = :id AND name = :name"
     */
    public function where(array $conditions): string
    {
        $where = [];
        foreach (array_keys($conditions) as $field) {
            $where[] = "{$field} = :{$field}";
        }
        return implode(" AND ", $where);
    }

    /**
     * It is responsible for initializing the transaction.
     *
     * @uses PDO
     * @uses Database::$conn
     * @return bool
     */
    public function inicializeTransaction(): bool
    {
        if (
            $this->conn instanceof PDO &&
            !$this->conn->inTransaction()
        ) {
            return $this->conn->beginTransaction();
        }
        return false;
    }

    /**
     * It is responsible for rolling back the transaction.
     *
     * @uses PDO
     * @uses Database::$conn
     * @return bool
     */
    public function rollback(): bool
    {
        if (
            $this->conn instanceof PDO &&
            $this->conn->inTransaction()
        ) {
            return $this->conn->rollBack();
        }
        return false;
    }

    /**
     * It is responsible for saving the transaction.
     *
     * @uses PDO
     * @uses Database::$conn
     * @return bool
     */
    public function save(): bool
    {
        if (
            $this->conn instanceof PDO &&
            $this->conn->inTransaction()
        ) {
            return $this->conn->commit();
        }
        return false;
    }

    /**
     * It is responsible for executing the SQL query.
     *
     * @param string $sql The SQL query to be executed.
     * @param array $params Array of parameters where the key is the parameter name and the value is the parameter value.
     * @uses PDO
     * @return bool return of the execution of PDO::execute.
     */
    public function run(string $sql, array $params = []): bool
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        // Verifica se a consulta contém a cláusula RETURNING
        if (stripos($sql, 'RETURNING') !== false) {
            return $stmt->fetchColumn();
        }
        return true;
    }

    /**
     * It is responsible for returning the result of the SQL query.
     *
     * @param string $sql The SQL query to be executed.
     * @param array $params Array of parameters where the key is the parameter name and the value is the parameter value.
     * @uses PDO
     * @return array
     *
     * @example
     * $sql = "SELECT * FROM users WHERE id = :id";<br>
     * $params = ['id' => 1];<br>
     * $result = $this->list($sql, $params);
     */
    public function list(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * It is responsible for returning the first result of the SQL query.
     *
     * @param string $sql The SQL query to be executed.
     * @param array $params Array of parameters where the key is the parameter name and the value is the parameter value.
     * @uses PDO
     * @return array
     *
     * @example
     * $sql = "SELECT * FROM users WHERE id = :id";<br>
     * $params = ['id' => 1];<br>
     * $result = $this->listOne($sql, $params);
     */
    public function listOne(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * It is responsible for inserting data into the table.
     *
     * @param string $table The name of the table where the data will be inserted.
     * @param array $data Array of data where the key is the field name and the value is the field value.
     * @uses Database::existField()
     * @uses Database::run()
     * @return int|bool
     *
     * @example
     * <pre>
     * $table = 'users';
     * $data = [
     *     'name' => 'John Doe',
     *     'email' => 'john.doe@test.com',
     *     'password' => 'securepassword'
     * ];
     * $success = $this->insert($table, $data); <br>
     * // Result: The last insert ID if the insertion was successful, false otherwise
     * </pre>
     */
    public function insert(string $table, array $data): int|false
    {
        try {

            if ($this->existField($table, "created")) {
                $data["created"] = date("Y-m-d H:i:s");
            }
            if ($this->existField($table, "modified")) {
                $data["modified"] = date("Y-m-d H:i:s");
            }
            $fields = array_keys($data);
            $sql = "INSERT INTO {$table} (" . implode(", ", $fields) . ") VALUES (:" . implode(", :", $fields) . ") " . $this->driver = "PostgreSQL" ? "RETURNING id" : "";

            $stmt = $this->conn->prepare($sql);
            if ($stmt->execute($data)) {
                return $stmt->fetchColumn();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw HttpError::internalServerError(
                details: $e->getMessage(),
                additionalInfo: [
                    "table" => $table,
                    "data" => $data
                ]
            );
        }
    }

    /**
     * It is responsible for updating data in the table.
     *
     * @param string $table The name of the table where the data will be updated.
     * @param array $data Array of data where the key is the field name and the value is the field value.
     * @param array $where Array of conditions where the key is the field name and the value is the field value.
     * @uses Database::existField()
     * @uses Database::where()
     * @uses Database::run()
     * @return bool
     *
     * @example
     * <pre>
     * $table = 'users';
     * $data = [
     *     'name' => 'Jane Doe',
     *     'email' => 'jane.doe@example.com'
     * ];
     * $where = ['id' => 1];
     * $success = $this->update($table, $data, $where);
     * // Result: true if the update was successful, false otherwise
     * </pre>
     */
    public function update(string $table, array $data, array $where): bool
    {
        if ($this->existField($table, "modified")) {
            $data["modified"] = date("Y-m-d H:i:s");
        }

        $sql = "UPDATE {$table} SET ";

        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "{$field} = :{$field}";
        }

        $sql .= implode(", ", $fields);
        $where = $this->where($where);
        $sql .= " WHERE {$where}";

        $params = array_merge($data, $where);
        return $this->run($sql, $params);
    }

    /**
     * It is responsible for deleting data from the table.
     *
     * @param string $table The name of the table where the data will be deleted.
     * @param array $where Array of conditions where the key is the field name and the value is the field value.
     * @uses Database::where()
     * @uses Database::run()
     * @return bool
     *
     * @example
     * <pre>
     * $table = 'users';
     * $where = ['id' => 1];
     * $success = $this->delete($table, $where);
     * // Result: true if the deletion was successful, false otherwise
     * </pre>
     */
    public function delete(string $table, array $where): bool
    {
        $whereStr = $this->where($where);
        $sql = "DELETE FROM {$table} WHERE {$whereStr}";
        return $this->run($sql, $where);
    }

    /**
     * It is responsible for returning the fields of the table.
     *
     * @param string $table The name of the table to be returned.
     * @uses Database::list()
     * @return array
     */
    public function getDetTable(string $table): array
    {
        if (!in_array($table, $this->getTables())) {
            return [];
        }

        $fields = [];

        if ($this->driver = "PostgreSQL") {
            $query = $this->list("SELECT column_name AS Field, data_type AS Type, is_nullable AS Null, column_default AS Default,
                          (SELECT EXISTS (SELECT 1 FROM information_schema.table_constraints tc
                          JOIN information_schema.key_column_usage kcu
                          ON tc.constraint_name = kcu.constraint_name
                          WHERE tc.table_name = '{$table}' AND kcu.column_name = c.column_name AND tc.constraint_type = 'PRIMARY KEY')) AS pk
                          FROM information_schema.columns c
                          WHERE table_name = '{$table}'");
        } else {
            $query = $this->list("DESC {$table}");
        }

        foreach ($query as $field) {
            $fields[] = [
                "name" => $field["field"] ?? $field["Field"],
                "type" => $field["type"] ?? $field["Type"],
                "null" => ($field["null"] ?? $field["Null"]) == "YES",
                "default" => isset($field["default"]) ? $field["default"] : (isset($field["Default"]) ? $field["Default"] : null),
                "pk" => $field["pk"] ?? $field["Extra"] == "auto_increment"
            ];
        }

        return $fields;
    }

    /**
     * It is responsible for returning the tables of the database.
     *
     * @uses Database::list()
     * @return array
     */
    public function getTables(): array
    {
        $tables = [];
        if ($this->driver = "PostgreSQL") {
            $query = $this->list("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';");
            $tables = array_column($query, 'table_name');
        } else {
            $query = $this->list("SHOW TABLES");
            $tables = array_column($query, 'Tables_in_' . self::$settings->database["database"]);
        }
        return $tables;
    }

    /**
     * It is responsible for checking if the table exists.
     *
     * @param string $table The name of the table to be checked.
     * @uses Database::getTables()
     * @return bool
     */
    public function existTable(string $table): bool
    {
        return in_array($table, $this->getTables());
    }

    /**
     * It is responsible for checking if the field exists.
     *
     * @param string $table The name of the table where the field will be checked.
     * @param string $field The name of the field to be checked.
     * @uses Database::getDetTable()
     * @return bool
     */
    public function existField(string $table, string $field): bool
    {
        $fields = array_column($this->getDetTable($table), "name");
        return in_array($field, $fields);
    }

    /**
     * It is responsible for setting the system identifier.
     *
     * This function is only available for PostgreSQL.
     *
     * @param array $data Array of data
     * @return bool
     */
    public function setSystemIdentifier(array $data): bool
    {
        if ($this->driver != "PostgreSQL") {
            return false;
        }

        $systemIdentifier = json_encode($data);
        return $this->conn->exec("SET bifrost.system_identifier = '$systemIdentifier'");
    }

    /**
     * It is responsible for running the SQL query.
     *
     * @param string|array $select
     * @param string $insert
     * @param string $update
     * @param string $delete
     * @param string $from
     * @param array $set
     * @param array $values
     * @param string|array $where
     * @param string|array $join
     * @param string $order
     * @param string $limit
     * @param string|array $having
     * @param array $params
     *
     * @return array|bool
     */
    public function query(
        string|array $select = null,
        string $insert = null,
        string $update = null,
        string $delete = null,
        string $from = null,
        array $set = null,
        array $values = null,
        string|array $where = null,
        string|array $join = null,
        string $order = null,
        string $limit = null,
        string|array $having = null,
        string $group = null,
        string $returning = null,
        array $params = []
    ): array|bool {
        $query = "";

        // SELECT
        if (!empty($select)) {
            $query .= "SELECT ";
            if (is_array($select)) {
                $query .= implode(", ", $select);
            } else {
                $query .= $select;
            }
        }
        // INSERT
        else if (!empty($insert)) {
            $query .= "INSERT INTO $insert ";
            if (!empty($values)) {
                $query .= " (" . implode(", ", array_keys($values)) . ") VALUES (:" . implode(", :", array_keys($values)) . ")";
            }
            if (!empty($returning)) {
                $query .= " RETURNING $returning";
            }
        }
        // UPDATE
        else if (!empty($update)) {
            $query .= "UPDATE $update ";
            if (!empty($set)) {
                $query .= " SET ";
                $fields = [];
                foreach (array_keys($set) as $field) {
                    $fields[] = "{$field} = :{$field}";
                }
                $query .= implode(", ", $fields);
            }
        }
        // DELETE
        else if (!empty($delete)) {
            $query .= "DELETE FROM $delete ";
            $from = null;
        }

        if (!empty($from)) {
            $query .= " FROM $from ";
        }

        if (!empty($join)) {
            if (is_array($join)) {
                foreach ($join as $j) {
                    $query .= " $j";
                }
            } else {
                $query .= $join;
            }
        }

        if (!empty($where)) {
            $query .= " WHERE ";
            if (is_array($where)) {
                $query .= $this->where($where);
            } else {
                $query .= $where;
            }
        }

        if (!empty($group)) {
            $query .= " GROUP BY $group";
        }

        if (!empty($having)) {
            $query .= " HAVING ";
            if (is_array($having)) {
                $query .= $this->where($having);
            } else {
                $query .= $having;
            }
        }

        if (!empty($order)) {
            $query .= " ORDER BY $order";
        }

        if (!empty($limit)) {
            $query .= " LIMIT $limit";
        }

        if (empty($select)) {
            return $this->run($query, $params);
        } else {
            return $this->list($query, $params);
        }
    }
}
