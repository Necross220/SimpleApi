<?php

class Connection
{
    protected $conn;

    public function __construct()
    {

        $host_name = 'localhost';
        $database = 'api';
        $username = 'root';
        $password = '';
        $charset = 'utf8mb4';

        $query_string = "mysql:host=$host_name;dbname=$database;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {

            $this->conn = new PDO($query_string, $username, $password, $options);

        } catch (Exception $exception) {
            echo "Please try again later: {$exception->getMessage()}";
        }
    }

    /**
     * Parses the URL address using slashes and returns params as array
     * @return array The data parameters
     */
    public function collection($data): array
    {
        $dataset = [];
        while ($data_row = $data->fetch()) {
            $dataset[] = $data_row;
        }
        return $dataset;
    }

}

$database = new Connection();