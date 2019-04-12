<?php

class MySQL
{
    private $mysqli;

    /**
     * MySQL constructor
     * @param mysqli $mysqli
     */
    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Execute a SQL in the database
     * @param string $sql SQL
     * @param string|null $parameter_types Parameter types used by bind_params
     * @param mixed ...$parameters Parameters
     * @return false|mysqli_result
     */
    public function execute(string $sql, string $parameter_types = null, ...$parameters)
    {
        $statement = $this->mysqli->prepare($sql);
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
