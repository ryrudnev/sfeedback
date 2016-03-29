<?php

namespace Drupal\sfeedback\Storage;
use Drupal\Core\Database\Database;

class FeedbackStorage
{
    private static $_instance;

    protected $tableName = 'sfeedback';
    protected $connection;

    public function getInstance()
    {
        return is_null(static::$_instance) ? (static::$_instance = new static()) : static::$_instance;
    }

    protected function __construct()
    {
        $this->connection = Database::getConnection();
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    public function getAll()
    {
        $tableName = $this->tableName;
        return $this->connection->query("SELECT * FROM {$tableName}")->fetchAllAssoc('id');
    }

    public function exists($id)
    {
        $tableName = $this->tableName;
        $result = $this->connection->query("SELECT 1 FROM {$tableName} WHERE id = :id", [':id' => $id])->fetchField();
        return (bool) $result;
    }

    public function add(array $data)
    {
        return $this->connection->insert($this->tableName)->fields($data)->execute();
    }

    public function delete($id)
    {
        return $this->connection->delete($this->tableName)->condition('id', $id)->execute();
    }
}