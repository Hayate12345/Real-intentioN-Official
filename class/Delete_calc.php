<?php

require_once '/Applications/MAMP/htdocs/Deliverables4/class/Database_calc.php';

class Delete
{
    public function intern_experience_delete($post_id)
    {
        $db_calc = new Database();

        $sql = 'DELETE FROM `intern_experience_tbl` WHERE post_id = ?';

        $argument = [];
        $argument[] = strval($post_id);

        $result = $db_calc->data_various_kinds($sql, $argument);

        return $result;
    }
}