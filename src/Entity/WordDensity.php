<?php

namespace App\Entity;

use App\Repository\WordDensityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=WordDensityRepository::class)
 */
class WordDensity
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?UserInterface $user;

    /**
     * @Assert\Url
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected ?string $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $notes;

    /**
     * @ORM\Column(name="words_limit", type="integer", nullable=true)
     */
    private int $limit;

    /**
     * @ORM\Column(name="words_count", type="integer", nullable=true)
     */
    private int $wordsCount = 0;

    /**
     * @var ArrayCollection|Job[]
     *
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="wordDensity", orphanRemoval=true)
     */
    private $jobs;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="wordDensity")
     */
    private ?Job $lastJob;

    public function __construct(UserInterface $user, ?string $url = null, ?int $limit = 20)
    {
        $this->user = $user;
        $this->url = $url;
        $this->limit = $limit;
        $this->jobs = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     *
     * @return WordDensity
     */
    public function setUser(?UserInterface $user): WordDensity
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return WordDensity
     */
    public function setUrl(?string $url): WordDensity
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return WordDensity
     */
    public function setNotes(?string $notes): WordDensity
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     * @return WordDensity
     */
    public function setLimit(?int $limit): WordDensity
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getWordsCount(): int
    {
        return $this->wordsCount;
    }

    /**
     * @param int $wordsCount
     * @return WordDensity
     */
    public function setWordsCount(int $wordsCount): WordDensity
    {
        $this->wordsCount = $wordsCount;

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setWordDensity($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getWordDensity() === $this) {
                $job->setWordDensity(null);
            }
        }

        return $this;
    }

    /**
     * @return Job|null
     */
    public function getLastJob(): ?Job
    {
        return $this->lastJob;
    }

    /**
     * @param Job|null $lastJob
     * @return WordDensity
     */
    public function setLastJob(?Job $lastJob): WordDensity
    {
        $this->lastJob = $lastJob;

        return $this;
    }
}
