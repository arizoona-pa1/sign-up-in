<?php
include_once("../autoloader.inc.php");
const ACCESS_LEVEL = [
    "read",
    "write",
    "update",
    "delete",
];
const ACCESS_USER = [
    "self",
    "anyone"
];
const DATABASE_TABLES = [
    "address",
    "authentication",
    "paymentmethod",
    "paymentstatus",
    "personal_info",
    "rankuser",
    "secure_b",
    "transaction",
    "users",
    "wallet",
    "walletcard",
    "withdrawal"
];
class access extends MySQL
{
    private ?string $DB_NAME = 'member';
    private string $tableName = "everyone";
    public $name;
    public $member_access;
    public $DATABASE_TABLES;
    function __construct(?string $DB_NAME = null)
    {
        $this->DB_NAME = $DB_NAME ?? $this->DB_NAME;
        parent::__construct($this->DB_NAME);
        $smt = $this->PDO->prepare("SELECT table_name FROM information_schema.tables
        WHERE table_schema = '" . $this->DB_NAME . "';");

        if ($smt->execute()) {
            $PHP_array = $smt->fetchAll();
            foreach ($PHP_array as $x => $x_value) {
                $array[] = $x_value->TABLE_NAME;
            }
            $this->DATABASE_TABLES = $array or DATABASE_TABLES;
        }
    }
    // update and insert
    function set(
        string $name,
        ?array $self,
        ?array $anyone,
        ?string $tableName = null,
        ?string $DB_NAME = null
    ): void {
        $this->name = strtolower($name);
        $self = $self ?? [1, 0, 0, 0];
        $anyone = $anyone ?? [0, 0, 0, 0];
        $both[] = $self;
        $both[] = $anyone;
        $tableName = $tableName ?? $this->tableName;
        $DB_NAME = $DB_NAME ?? $this->DB_NAME;
        $ACCESS_LEVEL = [];
        foreach ($both as $list_ACCESS => $ACCESS_LEVEL_construct) {
            foreach ($ACCESS_LEVEL_construct as $list => $value) {
                $ACCESS_LEVEL["member"][ACCESS_USER[$list_ACCESS]][ACCESS_LEVEL[$list]] = $value;
            }
        }
        $ACCESS_LEVEL["member"]["@attributes"]["rank"] = $name;
        $ACCESS_LEVEL["member"]["@attributes"]["table"] = $tableName;
        $ACCESS_LEVEL["member"]["@attributes"]["database"] = $DB_NAME;
        $this->member_access[] = $ACCESS_LEVEL;
    }
    function save()
    {
        foreach ($this->member_access as $member_access) {
            #--------Unique----------
            $attr['attr'] = $member_access["member"]["@attributes"];
            $attr['tag'] = "member";
            #--------Unique----------
            $XML = new XML("role");
            $XML->appendXML("role", $member_access, $attr);
        }
    }
}
$access = new access();
$access->set("user", [1, 0, 0, 0], [0, 0, 0, 0]);
$access->set("admin", [1, 0, 0, 0], [1, 1, 1, 0]);
$access->set("owner", [1, 1, 1, 1], [1, 1, 1, 1]);
// echo "<pre>";
// print_r($access->member_access);
// echo "</pre>";
$access->save();
?>