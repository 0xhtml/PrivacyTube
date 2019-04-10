<?php

class MySQL
{
    private $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function execute(string $sql, string $parameter_types = null, ...$parameters): mysqli_result
    {
        $statement = $this->mysqli->prepare($sql);
        if ($parameter_types != null) {
            $statement->bind_param($parameter_types, ...$parameters);
        }
        if (!$statement->execute()) {
            die("Can't execute SQL \"$sql\": $statement->error");
        }
        $result = $statement->get_result();
        if (!$result) {
            return null;
        }
        return $result;
    }
}
