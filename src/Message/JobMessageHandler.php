<?php

namespace App\Message;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Services\WordDensityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class JobMessageHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @var WordDensityService
     */
    private WordDensityService $wordDensityService;

    public function __construct(EntityManagerInterface $em, JobRepository $jobRepository, WordDensityService $wordDensityService)
    {
        $this->em = $em;
        $this->jobRepository = $jobRepository;
        $this->wordDensityService = $wordDensityService;
    }

    public function __invoke(JobMessage $message)
    {
        if (!$message->getId()) {
            throw new UnrecoverableMessageHandlingException('Job ID is missing.');
        }

        /** @var Job $job */
        $job = $this->jobRepository->find($message->getId());

        $job->setStatus(Job::STATUS_PROCESSING);
        $this->em->flush();

        if ($job->getType() === Job::TYPE_WORD_DENSITY) {
            $this->wordDensityService->process($job);
        }

        $job->setStatus(Job::STATUS_DONE);
        $this->em->flush();
    }
}
