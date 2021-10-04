<?php

namespace App\Message;

class JobMessage
{
    private int $id;

    /**
     * JobMessage constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return JobMessage
     */
    public function setId(int $id): JobMessage
    {
        $this->id = $id;

        return $this;
    }
}
