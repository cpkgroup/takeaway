<?php

namespace App\Service\Job;

class SendEmailJob
{
    /**
     * @var int
     */
    protected $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
}
