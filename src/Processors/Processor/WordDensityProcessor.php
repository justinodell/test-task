<?php

namespace App\Processors\Processor;

use App\Entity\Job;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

class WordDensityProcessor
{
    /**
     * @var WordDensityProcessorInterface[]
     */
    protected $processors;

    /**
     * WordDensityProcessor constructor.
     *
     * @param $processors
     */
    public function __construct(iterable $processors)
    {
        $this->processors = iterator_to_array($processors);
    }

    /**
     * @param Job $job
     * @return bool
     */
    public function process(Job $job)
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($job)) {
                if (!$processor->isActive()) {
                    throw new UnrecoverableMessageHandlingException(sprintf('Processor %s is inactive.', $processor->getName()));
                }

                $processor->process($job);
            }
        }

        return true;
    }

    /**
     * @return WordDensityProcessorInterface[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }
}
