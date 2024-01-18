<?php 
include("storage2.php");

class UserStorage extends Storage2 {
    public function __construct() {
        parent::__construct(new JsonIO2('users.json'));
    }
}
?>