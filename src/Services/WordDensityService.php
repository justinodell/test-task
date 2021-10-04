<?php

namespace App\Services;

use App\Entity\Job;
use App\Entity\WordDensity;
use App\Message\JobMessage;
use App\Processors\Model\WordDensity as WordDensityModel;
use App\Processors\Processor\WordDensityProcessor;
use App\Repository\WordDensityRepository;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Exception\InvalidSheetNameException;
use Box\Spout\Writer\Exception\SheetNotFoundException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class WordDensityService
{
    private EntityManagerInterface $em;
    private WordDensityRepository $repository;
    private WordDensityProcessor $processor;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $em, WordDensityRepository $repository, WordDensityProcessor $processor, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->processor = $processor;
        $this->bus = $bus;
    }

    public function getWordDensityByUrl(string $url): ?WordDensity
    {
        return $this->repository->findOneByUrl($url);
    }

    /**
     * @param UserInterface|null $user
     * @return WordDensity[]|ArrayCollection|array|null
     */
    public function getWordDensityList(?UserInterface $user): ?array
    {
        return $this->repository->findByUser($user, [
            'id' => 'DESC',
        ]);
    }

    public function getMessengerMessagesCount(): ?int
    {
        $messengerMessagesStmt = $this->em->getConnection()->prepare('SELECT COUNT(*) FROM messenger_messages;');
        $result = $messengerMessagesStmt->executeQuery();

        return $result->fetchOne();
    }

    public function createAndRunJob(WordDensity $wordDensity)
    {
        $job = new Job($wordDensity->getUser(), sprintf('Job %s', date('Y-m-d H:i')), Job::TYPE_WORD_DENSITY);
        $job->setContent(json_encode(new WordDensityModel($wordDensity->getUrl(), $wordDensity->getLimit()), JSON_THROW_ON_ERROR));
        $job->setWordDensity($wordDensity);
        $wordDensity->setLastJob($job);

        $this->em->persist($wordDensity);
        $this->em->persist($job);
        $this->em->flush();

        $this->bus->dispatch(new JobMessage($job->getId()));

        return $job;
    }

    public function createWordDensityAndJob(WordDensity $wordDensity): void
    {
        $wordDensityObject = $this->getWordDensityByUrl($wordDensity->getUrl());
        if (!$wordDensityObject) {
            $wordDensityObject = new WordDensity($wordDensity->getUser());
        }

        $wordDensityObject
            ->setUrl($wordDensity->getUrl())
            ->setNotes($wordDensity->getNotes())
            ->setLimit($wordDensity->getLimit());

        $this->createAndRunJob($wordDensityObject);
    }

    public function process(Job $job)
    {
        $this->processor->process($job);
    }

    /**
     * @param Job $job
     * @throws IOException
     * @throws InvalidSheetNameException
     * @throws SheetNotFoundException
     * @throws WriterNotOpenedException
     */
    public function export(Job $job)
    {
        if (!$job->getWordDensity()) {
            throw new \Exception(sprintf('Word density with ID #%d not found.', $job->getWordDensity()->getId()));
        }

        $writer = WriterEntityFactory::createXLSXWriter();

        $writer->openToBrowser(sprintf('Word-Density-Job-%d.xlsx', $job->getId()));

        // Create sheet
        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Words');
        $writer->setCurrentSheet($sheet);

        // Add Job details
        $writer->addRow(WriterEntityFactory::createRow([
            WriterEntityFactory::createCell('URL'),
            WriterEntityFactory::createCell('Total Words Count'),
            WriterEntityFactory::createCell('Date'),
        ]));
        $writer->addRow(WriterEntityFactory::createRow([
            WriterEntityFactory::createCell($job->getWordDensity()->getUrl()),
            WriterEntityFactory::createCell($job->getWordDensity()->getWordsCount()),
            WriterEntityFactory::createCell($job->getCreatedAt()->format('Y-m-d H:i')),
        ]));

        // Empty row
        $writer->addRow(WriterEntityFactory::createRow([WriterEntityFactory::createCell(''),]));

        $writer->addRow(WriterEntityFactory::createRow([
            WriterEntityFactory::createCell('Word'),
            WriterEntityFactory::createCell('Count'),
            WriterEntityFactory::createCell('Ratio'),
        ]));

        // Insert rows
        foreach ($job->getResults() as $result) {
            $dataCells = [
                WriterEntityFactory::createCell($result->getWord()),
                WriterEntityFactory::createCell($result->getWordCount()),
                WriterEntityFactory::createCell($result->getWordRatio()),
            ];

            $writer->setCurrentSheet($sheet);
            $writer->addRow(WriterEntityFactory::createRow($dataCells));
        }

        $writer->close();
    }
}
