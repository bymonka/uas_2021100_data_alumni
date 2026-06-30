<?php

abstract class Model
{
    protected $conn;
    protected $table;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    abstract public function getAll();
    abstract public function getById($id);
    abstract public function create($data);
    abstract public function update($id, $data);
    abstract public function delete($id);
}
