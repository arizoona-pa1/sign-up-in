<?php

class authentication extends MySQL
{
    private $DB_NAME = "member";
    private $SECURE_B;
    private $SECURE_A;
    private $ID;
    public readonly string $member_rank;
    public readonly mixed $rank;
    public bool $accessAllowed;
    function __construct(
        string $SECURE_B,
        string $SECURE_A,
        string $ID,
        string $DB_NAME = null
    ) {
        $this->SECURE_B = $SECURE_B;
        $this->SECURE_A = $SECURE_A;
        $this->ID = $ID;
        $this->DB_NAME = $DB_NAME ?? $this->DB_NAME;
        $this->accessAllowed = false;
        parent::__construct($this->DB_NAME);

        $smt = $this->PDO->prepare("SELECT * FROM authentication WHERE ID_browser = '$SECURE_B' AND ID_user = '$ID'");
        if ($smt->execute()) {
            if ($smt->rowCount() != 0) {
                $PHP_Object = (object) $smt->fetch();
                if ($SECURE_A == $PHP_Object->token) {
                    $smt = $this->PDO->prepare("SELECT * FROM users WHERE ID = $ID");
                    $smt->execute();
                    if ($smt->rowCount() == 0) {
                        #Error
                        echo "Error";
                        exit();
                    }
                    $PHP_Object = (object) $smt->fetch();
                    $this->member_rank = $PHP_Object->rank;
                    $this->accessAllowed = true;
                }
            }
        }
        $smt = $this->PDO->prepare("SELECT * FROM rankuser");
        if ($smt->execute()) {
            $this->rank = $smt->fetchAll();
        }
    }
}
?>