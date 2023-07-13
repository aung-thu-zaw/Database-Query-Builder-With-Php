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

        return $this;
    }

    public function whereNotIn($column, $values)
    {
        $stringValues=implode(",", $values);

        self::$sql.=" where $column not in ($stringValues)";

        $this->query();

        return $this;
    }

    public function whereDate($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql .= " where DATE($column) $operator '$value'";

        } elseif(func_num_args()===2) {

            self::$sql .= " where DATE($column) = '$operator'";

        }

        $this->query();

        return $this;
    }

    public function whereTime($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql .= " where TIME($column) $operator '$value'";

        } elseif(func_num_args()===2) {

            self::$sql .= " where TIME($column) = '$operator'";

        }

        $this->query();

        return $this;
    }

    public function whereDay($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql .= " where DAY($column) $operator $value";

        } elseif(func_num_args()===2) {

            self::$sql .= " where DAY($column) = $operator";

        }

        $this->query();

        return $this;
    }

    public function whereMonth($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql .= " where MONTH($column) $operator $value";

        } elseif(func_num_args()===2) {

            self::$sql .= " where MONTH($column) = $operator";

        }

        $this->query();

        return $this;
    }

    public function whereYear($column, $operator, $value="")
    {
        if(func_num_args()===3) {

            self::$sql .= " where YEAR($column) $operator $value";

        } elseif(func_num_args()===2) {

            self::$sql .= " where YEAR($column) = $operator";

        }

        $this->query();

        return $this;
    }

    public function orderBy($column, $direction)
    {
        self::$sql.=" order by $column $direction";

        $this->query();

        return $this;
    }

    public function latest()
    {
        self::$sql.=" order by created_at desc";

        $this->query();

        return $this;
    }

    public function inRandomOrder()
    {
        self::$sql.=" order by RAND()";

        $this->query();

        return $this;
    }

    public function take($limit)
    {
        self::$sql .= " limit $limit";

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

    public function insert($data)
    {
        $table=self::$tableName;

        $columns=implode(",", array_keys($data));

        $values=array_values($data);

        $questionMarkValues="";

        foreach($data as $value) {
            $questionMarkValues.="?,";
        }

        $questionMarkValues = rtrim(str_repeat("?,", count($data)), ",");

        self::$sql="insert into $table ($columns) values ($questionMarkValues)";

        $this->query($values);

        $id=self::$dbh->lastInsertId();

        $data=DB::table($table)->findOrFail($id);

        return $data;
    }

    public function insertOrIgnore($data)
    {
        $table=self::$tableName;

        $columns=implode(",", array_keys($data));

        $values=array_values($data);

        $questionMarkValues="";

        foreach($data as $value) {
            $questionMarkValues.="?,";
        }

        $questionMarkValues = rtrim(str_repeat("?,", count($data)), ",");

        self::$sql="insert ignore into $table ($columns) values ($questionMarkValues)";

        $this->query($values);

        $id=self::$dbh->lastInsertId();

        if($id) {

            $data=DB::table($table)->findOrFail($id);

            return $data;
        } else {

            return 0;

        }

    }

    public function insertGetId($data)
    {
        $table=self::$tableName;

        $columns=implode(",", array_keys($data));

        $values=array_values($data);

        $questionMarkValues="";

        foreach($data as $value) {
            $questionMarkValues.="?,";
        }

        $questionMarkValues = rtrim(str_repeat("?,", count($data)), ",");

        self::$sql="insert into $table ($columns) values ($questionMarkValues)";

        $this->query($values);

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

    public function truncate()
    {
        $table=self::$tableName;

        self::$sql="truncate table $table";

        $this->query();

        return true;
    }

    public function paginate($per_page)
    {
        $page_no = isset($_GET["page"]) ? $_GET["page"] : 1;

        // Total Data Count
        $this->query();
        $count = self::$result->rowCount();

        // Calculate Total Pages
        $total_pages = ceil($count / $per_page);

        // Validate Current Page
        if ($page_no < 1) {
            $page_no = 1;
        } elseif ($page_no > $total_pages) {
            $page_no = $total_pages;
        }

        // Paginate Data
        $index = ($page_no - 1) * $per_page;
        self::$sql .= " limit $index, $per_page";
        $this->query();
        self::$data = self::$result->fetchAll(PDO::FETCH_OBJ);

        // Prev Page
        $prev_page = "page=" . ($page_no - 1);

        // Next Page
        $next_page = "page=" . ($page_no + 1);

        // Formatted Return Paginate Data
        return [
            "next_page" => $next_page,
            "prev_page" => $prev_page,
            "data" => self::$data,
            "total" => $count,
            "current_page" => $page_no,
            "total_page" => $total_pages
        ];
    }

}



// $user = DB::table("users")->insertOrIgnore([
// "name"=>"Ksadfsafo dfddfKo Mfffaung",
// "email"=>"kodsdfsdafadkomaung@gmail.com",
// "password"=>"Password!",
// "created_at"=>"2019-09-01 10:09:24",
// "updated_at"=>"2019-09-01 10:09:24",
// ]);

$user=DB::table("users")->paginate(5);

echo "<pre/>";
print_r($user);
