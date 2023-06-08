<?php

class query extends MySQL
{
    private $DB_NAME = "member";
    private $nameQuery;
    public readonly bool $isDone;
    public $fetchAll;
    public $rowCount = 0;
    public $fetch;
    public readonly string|array $messages;
    function __construct(?string $DB_NAME = NULL)
    {
        $this->DB_NAME = $DB_NAME ?? $this->DB_NAME;
        parent::__construct($this->DB_NAME);
    }
    #-------------------------------------------------------------------------
    # $query->find_bool("fuczer.users", "ID", "1")
    #-------------------------------------------------------------------------
    public function find_bool(string $tableName, $columnName, $value): bool
    {
        $smt = $this->PDO->prepare("SELECT * FROM $tableName WHERE `$columnName` = '$value';");
        if ($smt->execute()) {
            if ($smt->rowCount() != 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    private function exec($smt)
    {
        if ($smt->execute()) {
            switch ($this->nameQuery) {
                case "read":
                    if ($rowCount = $smt->rowCount() != 0) {
                        $this->rowCount = $rowCount;
                        $this->fetch = $smt->fetch();
                        $this->fetchAll = $smt->fetchAll();
                    } else {
                        $this->fetch = null;
                        $this->fetchAll = null;
                    }
                    break;
                default:
                    $this->fetch = null;
                    $this->fetchAll = null;
                    break;
            }
            return true;
        } else {
            $this->messages = $this->nameQuery . " TEXT";
            return false;
        }
    }
    private function callable_func(callable $func, $values)
    {
        # table name 
        $table_name = $values["table_name"] ?? "";
        # where Column
        $WColumn = $values['WColumn'] ?? "";
        # set Column
        $SColumn = $values['SColumn'] ?? "";
        # insert Column array ( key => vlaue )  $values['INSERT'] 

        switch ($this->nameQuery) {
            case "read":
                $this->isDone = $func($table_name, $WColumn);
                break;
            case "write":
                $array = new ArrayIterator($values['INSERT']);
                do {
                    if ($value = $array->current()) {
                        $column = $array->key();
                    }
                    $COLUMNS .= "`$column`";
                    $VALUES .= $value;
                    $array->next();
                    if ($array->current()) {
                        $COLUMNS .= ",";
                        $VALUES .= ",";
                    }

                } while ($array->current());

                $this->isDone = $func($table_name, $COLUMNS, $VALUES);
                break;
            case "update":
                $this->isDone = $func($table_name, $SColumn, $WColumn);
                break;
            case "delete":
                $this->isDone = $func($table_name, $WColumn);
                break;
            default:
                $this->isDone = $func($values);
                break;
        }
    }
    #-------------------------------------------------------------------------
    # $query->unique("fuczer.users", "ID", "1")
    #-------------------------------------------------------------------------
    function unique(string $tableName, string $name, string $value): bool
    {
        $user = $this->PDO->prepare("SELECT * FROM $tableName WHERE $name = ?;");
        $user->bindValue(1, $value, PDO::PARAM_STR);
        $user->execute();
        if ($user->rowCount() != 0) {
            return true;
        }
        return false;
    }
    public function execute(string $func, mixed $values)
    {
        $this->nameQuery = $func;
        $this->callable_func([$this, $func], $values);
    }
    function read(string $table_name, ?string $WColumn = null)
    {
        $this->nameQuery = __FUNCTION__;

        if ($WColumn) {
            $WColumn = "WHERE $WColumn";
        } else {
            $WColumn = "";
        }
        $smtSQL = "SELECT * FROM $table_name $WColumn;";
        $smt = $this->PDO->prepare($smtSQL);
        return $this->exec($smt);

    }

    function write(string $table_name, string $COLUMNS, string $VALUES)
    {
        $this->nameQuery = __FUNCTION__;

        $smtSQL = "INSERT INTO $table_name($COLUMNS) VALUES($VALUES);";
        $smt = $this->PDO->prepare($smtSQL);
        echo $smtSQL;
        return $this->exec($smt);
    }

    function update(string $table_name, string $SColumn, string $WColumn)
    {
        $this->nameQuery = __FUNCTION__;

        $smtSQL = "UPDATE $table_name SET $SColumn WHERE $WColumn;";
        $smt = $this->PDO->prepare($smtSQL);
        echo $smtSQL;
        return $this->exec($smt);
    }

    function delete(string $table_name, string $WColumn)
    {
        $this->nameQuery = __FUNCTION__;

        $smtSQL = "DELETE FROM $table_name WHERE $WColumn;";
        $smt = $this->PDO->prepare($smtSQL);
        echo $smtSQL;
        return $this->exec($smt);
    }
}

?>