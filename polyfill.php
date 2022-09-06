<?php
// This file is fill for autocompletion purposes
// Also because there's errors and I don't want errors

class Controller {
    public ControllerLoader $load;
    public ControllerDocument $document;
}

class ControllerLoader {
    public function language(string $path) {}
    public function model(string $path) {}
    public function controller(string $path) {}
    public function view(string $path) {}
    public function library(string $path) {}
}

class ControllerDocument {
    public function setTitle(string $title) {}
    public function addStyle(string $path) {}
    public function addScript(string $path) {}
}

class Model {
    public DBConnector $db;
}

class DBConnector {
    public function escape(string $unescaped_str) {}
    /**
     * @return DBConnectorQueryResult
     */
    public function query(string $sql) {}
}

class DBConnectorQueryResult {
    public int $num_rows;
    public array $rows;
}

define("DB_PREFIX", "prefix_lol_");
define("DEV_MODE", "TRUE");
define("DIR_APPLICATION", "/workspace/admin/");
define("VERSION", "8.00.85"); // ;)