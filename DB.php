<?php

class DB
{
    private static $dbh;
    private static $result;
    private static $data;
    private static $sql;
    private static $tableName;

    public function __construct()
    {
        try {
            self::$dbh=new PDO("mysql:host=localhost;dbname=database_query_builder", "root", "Aung123580Zaw@");

            echo "Connected.";
        } catch(PDOException $error) {

            echo $error->getMessage();
        }
    }

    public function query($values=[])
    {
        self::$result=self::$dbh->prepare(self::$sql);

        self::$result->execute($values);

        return $this;
    }

    public static function table($tableName)
    {
        self::$tableName=$tableName;

        self::$sql="select * from $tableName";

        $db= new DB();

        $db->query();

        return $db;
    }

    public function select(...$columns)
    {
        $stringColumns = implode(", ", $columns);

        self::$sql = "select $stringColumns from " . self::$tableName;

        $this->query();

        return $this;
    }

    public function where($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql.=" where $column$operator'$value'";

        } elseif(func_num_args()===2) {

            self::$sql.=" where $column='$operator'";

        }

        $this->query();

        return $this;
    }

    public function andWhere($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql.=" and $column$operator'$value'";

        } elseif(func_num_args()===2) {

            self::$sql.=" and $column='$operator'";

        }

        $this->query();

        return $this;
    }

    public function orWhere($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql.=" or $column$operator'$value'";

        } elseif(func_num_args()===2) {

            self::$sql.=" or $column='$operator'";

        }

        $this->query();

        return $this;
    }

    public function whereNull($column)
    {
        self::$sql.=" where $column is null";

        $this->query();

        return $this;
    }

    public function whereNotNull($column)
    {
        self::$sql.=" where $column is not null";

        $this->query();

        return $this;
    }

    public function whereBetween($column, $values)
    {
        self::$sql.=" where $column between $values[0] and $values[1]";

        $this->query();

        return $this;
    }

    public function whereNotBetween($column, $values)
    {
        self::$sql.=" where $column not between $values[0] and $values[1]";

        $this->query();

        return $this;
    }

    public function whereIn($column, $values)
    {
        $stringValues=implode(",", $values);

        self::$sql.=" where $column in ($stringValues)";

        $this->query();

        echo self::$sql;

        return $this;
    }

    public function orderBy($column, $direction)
    {
        self::$sql.=" order by $column $direction";

        $this->query();

        return $this;
    }

    public function get()
    {
        self::$data=self::$result->fetchAll(PDO::FETCH_OBJ);

        return self::$data;
    }

    public function first()
    {
        self::$data=self::$result->fetch(PDO::FETCH_OBJ);

        return self::$data;
    }

    public function count()
    {
        return count(self::$data);
    }


    public function find($id)
    {
        self::$sql.=" where id=$id";

        $this->query();

        self::$data=self::$result->fetch(PDO::FETCH_OBJ);

        return self::$data;
    }

    public function findOrFail($id)
    {
        self::$sql.=" where id=$id";

        $this->query();

        self::$data = self::$result->fetch(PDO::FETCH_OBJ);

        if (!self::$data) {
            throw new Exception("Record not found.");
        }

        return self::$data;
    }

    public static function create($table, $data)
    {
        $db=new DB();

        $columns=implode(",", array_keys($data));

        $values=array_values($data);

        $questionMarkValues="";

        foreach($data as $value) {
            $questionMarkValues.="?,";
        }

        $questionMarkValues = rtrim(str_repeat("?,", count($data)), ",");

        self::$sql="insert into $table ($columns) values ($questionMarkValues)";

        $db->query($values);

        return self::$dbh->lastInsertId();
    }

    public static function update($table, $data, $id)
    {
        $db = new DB();

        $columns = "";

        $values=array_values($data);

        foreach ($data as $key => $value) {
            $columns .= "$key=?,";
        }

        $columns = rtrim($columns, ",");

        self::$sql="update $table set $columns where id=$id";

        $db->query($values);

        $updatedRow=DB::table($table)->findOrFail($id);

        return $updatedRow;
    }

    public static function delete($table, $id)
    {
        $db=new DB();

        self::$sql="delete from $table where id=$id";

        $db->query();
    }
}


$user=DB::table("users")->whereIn("id", [15,16,17,18,19,20])->get();


echo "<pre/>";
print_r($user);
