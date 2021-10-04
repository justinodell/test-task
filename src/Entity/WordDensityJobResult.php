<?php

namespace App\Entity;

use App\Repository\WordDensityJobResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=WordDensityJobResultRepository::class)
 */
class WordDensityJobResult
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WordDensity")
     * @ORM\JoinColumn(name="word_density_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private WordDensity $wordDensity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Job")
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Job $job;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $word;

    /**
     * @ORM\Column(name="word_count", type="integer", nullable=true)
     */
    private int $wordCount = 0;

    /**
     * @ORM\Column(name="word_ratio", type="decimal", precision=11, scale=10, nullable=true)
     */
    private float $wordRatio = 0;

    public function __construct(WordDensity $wordDensity, Job $job)
    {
        $this->wordDensity = $wordDensity;
        $this->job = $job;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return WordDensity
     */
    public function getWordDensity(): WordDensity
    {
        return $this->wordDensity;
    }

    /**
     * @param WordDensity $wordDensity
     * @return WordDensityJobResult
     */
    public function setWordDensity(WordDensity $wordDensity): WordDensityJobResult
    {
        $this->wordDensity = $wordDensity;

        return $this;
    }

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }

    /**
     * @param Job $job
     * @return WordDensityJobResult
     */
    public function setJob(Job $job): WordDensityJobResult
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @param string $word
     * @return WordDensityJobResult
     */
    public function setWord(string $word): WordDensityJobResult
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return int
     */
    public function getWordCount(): int
    {
        return $this->wordCount;
    }

    /**
     * @param int $wordCount
     * @return WordDensityJobResult
     */
    public function setWordCount(int $wordCount): WordDensityJobResult
    {
        $this->wordCount = $wordCount;

        return $this;
    }

    /**
     * @return float
     */
    public function getWordRatio(): float
    {
        return $this->wordRatio;
    }

    /**
     * @param float $wordRatio
     * @return WordDensityJobResult
     */
    public function setWordRatio(float $wordRatio): WordDensityJobResult
    {
        $this->wordRatio = $wordRatio;

        return $this;
    }
}
