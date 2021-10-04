<?php

namespace App\Processors\Processor;

use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BaseWordDensityProcessor implements WordDensityProcessorInterface
{
    public const NAME = 'Base';

    protected HttpClientInterface $client;
    protected EntityManagerInterface $em;
    protected string $stopWordsPathEn;
    protected string $userAgent;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em, string $stopWordsPathEn, string $userAgent)
    {
        $this->client = $client;
        $this->em = $em;
        $this->stopWordsPathEn = $stopWordsPathEn;
        $this->userAgent = $userAgent;
    }

    public function process(Job $job): void
    {
        // Must be overridden from child classes
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Job $job): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(): bool
    {
        return true;
    }

    protected function getStopwords(): array
    {
        $result = [];

        try {
            $stopwords = file_get_contents($this->stopWordsPathEn);
            $result = explode("\n", $stopwords);
        } catch (\Exception $e) {
            // TODO: Should be logged
        }

        return $result;
    }
}
