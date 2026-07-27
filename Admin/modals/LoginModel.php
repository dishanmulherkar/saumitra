<?php

class LoginModel
{
    private $con;

    public function __construct()
    {
        global $con;
        $this->con = $con;
    }

    public function login($username)
    {

        $stmt = $this->con->prepare("
            SELECT *
            FROM admins
            WHERE username=?
            LIMIT 1
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}