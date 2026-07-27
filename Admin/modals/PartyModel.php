<?php

class PartyModel
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function getAll()
    {
        return mysqli_query($this->con, "
            SELECT *
            FROM parties
            ORDER BY party_name ASC
        ");
    }

    public function getById($id)
    {
        $id = intval($id);
        $res = mysqli_query($this->con, "SELECT * FROM parties WHERE party_id = '$id'");
        return mysqli_fetch_assoc($res);
    }

    public function insert($data)
    {
        $pn = mysqli_real_escape_string($this->con, trim($data['party_name']));
       $status = mysqli_real_escape_string($this->con, intval($data['status']));

        // Insert without party_code
        $insert = mysqli_query($this->con, "
            INSERT INTO parties
            (party_name, party_type, status)
            VALUES
            ('$pn', '" . mysqli_real_escape_string($this->con, $data['party_type']) . "', '$status')
        ");

        if(!$insert)
        {
            return false;
        }

        return true;
    }

    public function update($id, $data)
    {
        $id  = intval($id);
        $pn  = mysqli_real_escape_string($this->con, trim($data['party_name']));
        $status = mysqli_real_escape_string($this->con, intval($data['status']));

        return mysqli_query($this->con, "
            UPDATE parties SET 
                party_name='$pn', party_type='" . mysqli_real_escape_string($this->con, $data['party_type']) . "', status='$status' 
            WHERE party_id = '$id'
        ");
    }

    public function delete($id)
    {
        $id = intval($id);
        return mysqli_query($this->con, "DELETE FROM parties WHERE party_id = '$id'");
    }

    public function checkDuplicate($name, $id = null)
    {
        $name = mysqli_real_escape_string($this->con, $name);
        $query = "SELECT party_id FROM parties WHERE party_name = '$name'";
        if ($id) $query .= " AND party_id != '" . intval($id) . "'";
        return mysqli_query($this->con, $query);
    }

    // public function insertParty($pn,  $st)
    // {
    //     $pn = mysqli_real_escape_string($this->con, $pn);
        

    //     return mysqli_query($this->con, "
    //         INSERT INTO parties (party_name, status) 
    //         VALUES ('$pn', '$st')
    //     ");
    // }

    public function exists($party_name)
    {
        $pn = mysqli_real_escape_string($this->con, $party_name);
        $res = mysqli_query($this->con, "SELECT p_id FROM parties WHERE party_name = '$pn'");
        return mysqli_num_rows($res) > 0;
    }
}