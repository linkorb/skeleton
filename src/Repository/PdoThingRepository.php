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
            'id' => $id,
        ));
        $row = $statement->fetch();
        return $row ? $this->rowToObject($row) : null;
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

        return $row ? $this->rowToObject($row) : null;
    }

    public function getAll()
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM thing"
        );
        $statement->execute();
        $rows = $statement->fetchAll();
        $objects = array();
        foreach ($rows as $row) {
            $objects[] = $this->rowToObject($row);
        }

        return $objects;
    }

    public function add(Thing $thing)
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO thing () VALUES ()'
        );
        $statement->execute();
        $thing->setId($this->pdo->lastInsertId());
        $this->update($thing);

        return true;
    }

    public function update(Thing $thing)
    {
        $statement = $this->pdo->prepare(
            "UPDATE thing
             SET name=:name, email=:email, description=:description
             WHERE id=:id"
        );
        $statement->execute(
            [
                'id' => $thing->getId(),
                'name' => $thing->getName(),
                'email' => $thing->getEmail(),
                'description' => $thing->getDescription(),
            ]
        );

        return $thing;
    }

    private function rowToObject($row)
    {
        if (!$row) {
            return null;
        }
        $obj = new Thing();
        $obj->setId($row['id'])
            ->setName($row['name'])
            ->setEmail($row['email'])
            ->setDescription($row['description']);

        return $obj;
    }
}
