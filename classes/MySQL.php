<?php

class MySQL
{
    private $mysqli;

    public function __construct(Config $config)
    {
        $this->mysqli = mysqli_connect($config->getMySQLHost(), $config->getMySQLUser(), $config->getMySQLPass(), $config->getMySQLDB());
        if (!$this->mysqli) {
            die("Can't connect to MySQL: " . mysqli_connect_error());
        }
    }

    public function execute(string $sql, string $parameter_types = null, ...$parameters)
    {
        if (!($statement = $this->mysqli->prepare($sql))) {
            die("Can't execute SQL \"$sql\": " . $this->mysqli->error);
        }
        if ($parameter_types != null) {
            $statement->bind_param($parameter_types, ...$parameters);
        }
        if (!$statement->execute()) {
            die("Can't execute SQL \"$sql\": $statement->error");
        }
        $result = $statement->get_result();
        return $result;
    }
}
