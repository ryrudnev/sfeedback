<?php

namespace Drupal\sfeedback\Storage;
use Drupal\Core\Database\Database;

class FeedbackStorage
{
    private static $_instance;

    protected $tableName = 'sfeedback';
    protected $connection;

    public static function getInstance()
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
        return $this->connection
            ->select($this->tableName, 'sf')->fields('sf')
            ->execute()->fetchAllAssoc('id');
    }

    public function exists($id)
    {
        $res = $this->connection->select($this->tableName)->condition('id', $id)->countQuery()->execute()->fetchField();
        return (bool) $res;
    }

    public function get($id)
    {
        return $this->connection
            ->select($this->tableName, 'sf')->fields('sf')
            ->condition('sf.id', $id)->execute()->fetchAssoc();
    }

    public function add(array $data)
    {
        return $this->connection->insert($this->tableName)->fields($data)->execute();
    }

    public function update($id, $data)
    {
        return $this->connection->update($this->tableName)->fields($data)->condition('id', $id)->execute();
    }

    public function delete($id)
    {
        return $this->connection->delete($this->tableName)->condition('id', $id)->execute();
    }
}