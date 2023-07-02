<?php

class DB
{
    private static $dbh;
    private static $result;
    private static $data;

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

    public function get()
    {
        return self::$data;
    }

}


$db=new DB();

$user=$db->query("select * from users")->get();

echo "<pre/>";
print_r($user);
