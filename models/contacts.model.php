<?php

class ContactsData
{
    Public function get_contacts()
    {
        $data_set = $this->conn->prepare("CALL GetArticlesDropdown()");
        $data_set->execute();
        return $this->collection($data_set);
    }
}