<?php

namespace App\Processors\Processor;

use App\Entity\Job;
use App\Entity\WordDensityJobResult;
use App\Processors\Model\WordDensity;
use Html2Text\Html2Text;
use Symfony\Component\HttpFoundation\Request;

class Html2TextWordDensityProcessor extends BaseWordDensityProcessor
{
    public const NAME = 'Html2Text';

    /**
     * {@inheritDoc}
     */
    public function process(Job $job): void
    {
        $wordDensity = WordDensity::jsonDeserialize($job->getContent());
        if (!$wordDensity->getUrl()) {
            return;
        }

        if (!$job->getWordDensity()) {
            return;
        }

        $response = $this->client->request(Request::METHOD_GET, $wordDensity->getUrl());
        if ($response->getStatusCode() === 404) {
            return;
        }

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(sprintf('Could not load URL: %s', $wordDensity->getUrl()));
        }

        $content = $response->getContent(false);

        // Extract text
        $html2Text = new Html2Text($content, ['do_links' => 'none'],);
        $text = $html2Text->getText();

        // Extract, sort, and cleanup words
        $words = str_word_count($text, 1);
        $words = array_map('strtolower', $words);
        $words = array_count_values($words);
        $words = array_diff_key($words, array_flip($this->getStopwords()));
        arsort($words);

        // Keep total words count
        $totalWordsCount = count($words);
        $job->getWordDensity()->setWordsCount($totalWordsCount);
        $this->em->persist($job);

        // Limit words list
        $words = array_slice($words, 0, $wordDensity->getLimit());

        foreach ($words as $word => $wordCount) {
            $ratio = $wordCount / $totalWordsCount;

            $wordDensityJobResult = new WordDensityJobResult($job->getWordDensity(), $job);
            $wordDensityJobResult
                ->setWord($word)
                ->setWordCount($wordCount)
                ->setWordRatio($ratio);
            $this->em->persist($wordDensityJobResult);
        }

        $this->em->flush();
    }
}
