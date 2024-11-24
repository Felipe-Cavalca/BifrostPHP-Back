<?php

//! MODELO DE EXEMPLO

namespace Bifrost\Model;

use Bifrost\Core\Database;

/**
 * Classe de representação do usuário
 *
 * Classe responsável por realizar a comunicação com o banco de dados
 *
 * @package Bifrost\Model
 */
class User
{
    private string $table = "user";
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getById(int $id)
    {
        return $this->search(["id" => $id])[0] ?? [];
    }

    public function getByEmail(string $email)
    {
        return $this->search(["email" => $email])[0] ?? [];
    }

    public function getAll()
    {
        return $this->database->list("SELECT * FROM $this->table");
    }

    public function search(array $conditions)
    {
        $sql = "SELECT * FROM $this->table WHERE " . $this->database->where($conditions);
        return $this->database->list($sql, $conditions);
    }

    public function insert(array $data)
    {
        return $this->database->insert($this->table, $data);
    }
}
