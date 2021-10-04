<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
{
    use TimestampableEntity;

    public const TYPE_WORD_DENSITY = 'word-density';

    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=WordDensity::class, inversedBy="job")
     */
    private ?WordDensity $wordDensity;

    /**
     * @var ArrayCollection|WordDensityJobResult[]
     *
     * @ORM\OneToMany(targetEntity=WordDensityJobResult::class, mappedBy="job", orphanRemoval=true)
     */
    private $results;

    public function __construct(UserInterface $user, string $name, string $type, $status = self::STATUS_NEW)
    {
        $this->user = $user;
        $this->name = $name;
        $this->type = $type;
        $this->status = $status;
        $this->results = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
     * @return Job
     */
    public function setUser(?UserInterface $user): Job
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return Job
     */
    public function setContent(?string $content): Job
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param mixed|string $status
     *
     * @return Job
     */
    public function setStatus(string $status): Job
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Job
     */
    public function setType(string $type): Job
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return ?WordDensity
     */
    public function getWordDensity(): ?WordDensity
    {
        return $this->wordDensity;
    }

    /**
     * @param ?WordDensity $wordDensity
     * @return Job
     */
    public function setWordDensity(?WordDensity $wordDensity): Job
    {
        $this->wordDensity = $wordDensity;
        if ($wordDensity) {
            $wordDensity->addJob($this);
        }

        return $this;
    }

    /**
     * @return WordDensityJobResult[]|ArrayCollection
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param WordDensityJobResult[]|ArrayCollection $results
     * @return Job
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @param WordDensityJobResult $result
     */
    public function addResult(WordDensityJobResult $result)
    {
        $this->results->add($result);
        // uncomment if you want to update other side
        $result->setJob($this);
    }

    /**
     * @param WordDensityJobResult $result
     */
    public function removeResult(WordDensityJobResult $result)
    {
        $this->results->removeElement($result);
        // uncomment if you want to update other side
        $result->setJob(null);
    }
}
