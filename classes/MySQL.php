<?php

class MySQL
{
    private $mysqli;

    /**
     * MySQL constructor
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->mysqli = mysqli_connect($config->getMySQLHost(), $config->getMySQLUser(), $config->getMySQLPass(), $config->getMySQLDB());
        if (!$this->mysqli) {
            die("Can't connect to MySQL: " . mysqli_connect_error());
        }
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
