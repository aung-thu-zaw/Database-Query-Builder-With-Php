<?php

class DB
{
    private static $dbh;
    private static $result;
    private static $data;
    private static $sql;

    public function __construct()
    {
        try {
            self::$dbh=new PDO("mysql:host=localhost;dbname=database_query_builder", "root", "Aung123580Zaw@");

            echo "Connected.";
        } catch(PDOException $error) {

            echo $error->getMessage();
        }
    }

    public function query($sql)
    {
        self::$result=self::$dbh->prepare($sql);

        self::$result->execute();

        self::$data=self::$result->fetchAll(PDO::FETCH_OBJ);

        return $this;
    }

    public static function table($tableName)
    {
        self::$sql="select * from $tableName";

        $db= new DB();

        $db->query(self::$sql);

        return $db;
    }

    public function where($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql.=" where $column$operator$value";

        } elseif(func_num_args()===2) {

            self::$sql.=" where $column=$operator";

        }

        $this->query(self::$sql);

        return $this;
    }

    public function orderBy($column, $direction)
    {
        self::$sql.=" order by $column $direction";

        $this->query(self::$sql);

        return $this;
    }

    public function get()
    {
        return self::$data;
    }

    public function count()
    {
        return count(self::$data);
    }
}


// $db=new DB();
// $user=$db->query("select * from users")->count();
$user=DB::table("users")->where("id", 1)->orderBy("id", "desc")->get();

echo "<pre/>";
print_r($user);
