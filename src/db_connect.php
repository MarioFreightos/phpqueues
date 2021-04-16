<?php

class DbConnect
{
    private $host = '192.168.1.21';
    private $dbname = 'docker_database';
    private $user = 'root';
    private $pass = '12345';

    public function connect()
    {
        try {
            $connection_string = 'mysql:host=' . $this->host . '; dbname=' . $this->dbname;
            $conn = new PDO($connection_string, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }

}

?>