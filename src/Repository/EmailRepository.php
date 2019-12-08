<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class EmailRepository extends EntityRepository
{
    public function findEmail(int $id)
    {
        return $this->find($id);
    }
}
