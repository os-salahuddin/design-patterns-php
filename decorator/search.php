<?php
interface SearchQueryInterface
{
    public function execute();
}

class BaseSearchQuery implements SearchQueryInterface
{
    protected $pdo;
    protected $tableName;
    protected $query;
    protected $params;

    public function __construct(PDO $pdo, $tableName)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->query = "SELECT * FROM {$this->tableName} WHERE 1=1";
        $this->params = [];
    }

    public function addCondition($condition, $param)
    {
        $this->query .= " AND " . $condition;
        $this->params[] = $param;
    }

    public function execute()
    {
        $stmt = $this->pdo->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class CountryNameSearchDecorator extends BaseSearchQuery implements SearchQueryInterface
{
    protected $decorated;
    protected $name;

    public function __construct(SearchQueryInterface $decorated, $name)
    {
        $this->decorated = $decorated;
        $this->name = $name;
    }

    public function execute()
    {
        if ($this->name) {
            $this->decorated->addCondition("name LIKE ?", "%" . $this->name . "%");
        }
        return $this->decorated->execute();
    }
}

class CountryCodeSearchDecorator extends BaseSearchQuery implements SearchQueryInterface
{
    protected $decorated;
    protected $code;

    public function __construct(SearchQueryInterface $decorated, $code)
    {
        $this->decorated = $decorated;
        $this->code = $code;
    }

    public function execute()
    {
        if ($this->code) {
            $this->decorated->addCondition("code = ?", $this->code);
        }
        return $this->decorated->execute();
    }
}

// Usage example
$pdo = new PDO("mysql:host=localhost;dbname=testDb", "root", "root1234");
$baseQuery = new BaseSearchQuery($pdo, "country");
$nameSearchDecorator = new CountryNameSearchDecorator($baseQuery, 'Bangladesh');
$query = new CountryCodeSearchDecorator($nameSearchDecorator, 'BD');
$results = $query->execute();
var_dump($results);