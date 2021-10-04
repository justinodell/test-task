<?php

namespace App\Processors\Processor;

use App\Entity\Job;

interface WordDensityProcessorInterface
{
    /**
     * @param Job $job
     * @return mixed
     */
    public function process(Job $job): void;

    /**
     * @param Job $job
     * @return bool
     */
    public function supports(Job $job): bool;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function isActive(): bool;
}
