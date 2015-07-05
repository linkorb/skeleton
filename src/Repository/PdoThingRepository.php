<?php

namespace LinkORB\Skeleton\Repository;

use LinkORB\Skeleton\Model\Thing;
use PDO;

class PdoThingRepository
{
    private $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function getById($id)
    {
        $statement = $this->pdo->prepare(
            "SELECT *
            FROM thing
            WHERE id=:id
            LIMIT 1"
        );
        $statement->execute(array(
            'id' => $id
        ));
        $row = $statement->fetch();
        return $this->rowToObject($row);
    }

    public function getByName($name)
    {
        $statement = $this->pdo->prepare(
            "SELECT *
            FROM thing
            WHERE name=:name
            LIMIT 1"
        );
        $statement->execute(array(
            'name' => $name
        ));
        $row = $statement->fetch();
        return $this->rowToObject($row);
    }
    
    public function getAll()
    {
        $statement = $this->pdo->prepare(
            "SELECT *
            FROM thing"
        );
        $statement->execute();
        $rows = $statement->fetchAll();
        $objects = array();
        foreach ($rows as $row) {
            $objects[] = $this->rowToObject($row);
        }
        return $objects;
    }
    
    private function rowToObject($row)
    {
        if (!$row) {
            return null;
        }
        $obj = new Thing();
        $obj->setId($row['id']);
        $obj->setName($row['name']);
        $obj->setDescription($row['description']);
        
        /*
        $obj->setPictureUrl($row['picture_url']);
        $obj->setCreatedAt($row['created_at']);
        $obj->setDeletedAt($row['deleted_at']);
        */
        return $obj;
    }
}
