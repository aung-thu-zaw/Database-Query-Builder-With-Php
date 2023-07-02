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

        self::$sql="select * from $tableName;";

        $db= new DB();

        $db->query(self::$sql);

        return $db;
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
$user=DB::table("users")->get();

echo "<pre/>";
print_r($user);
