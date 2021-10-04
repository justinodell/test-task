<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use App\Entity\WordDensity;
use App\Form\WordDensityType;
use App\Services\WordDensityService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_WORD_DENSITY_ADMIN")
 */
class WordDensityController extends AbstractController
{
    private WordDensityService $wordDensityService;

    public function __construct(WordDensityService $wordDensityService)
    {
        $this->wordDensityService = $wordDensityService;
    }

    /**
     * @Route("/word-density", name="admin_word_density")
     *
     * @param Request $request
     * @param AdminUrlGenerator $adminUrlGenerator
     *
     * @return Response
     */
    public function index(Request $request, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $wordDensity = new WordDensity($this->getUser());
        $form = $this->createForm(WordDensityType::class, $wordDensity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->wordDensityService->createWordDensityAndJob($wordDensity);
            $this->addFlash('success', 'URL has been added and a new job created!');

            return $this->redirect($adminUrlGenerator->generateUrl());
        }

        $wordDensityList = $this->wordDensityService->getWordDensityList($this->getUser());
        $messengerMessagesCount = $this->wordDensityService->getMessengerMessagesCount();

        return $this->render('Admin/wordDensity.html.twig', [
            'form' => $form->createView(),
            'wordDensityList' => $wordDensityList,
            'messengerMessagesCount' => $messengerMessagesCount,
        ]);
    }

    /**
     * @Route("/word-density/{id}/run", name="admin_word_density_run")
     *
     * @param WordDensity $wordDensity
     * @param AdminUrlGenerator $adminUrlGenerator
     *
     * @return Response
     * @throws \JsonException
     */
    public function run(WordDensity $wordDensity, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $this->wordDensityService->createAndRunJob($wordDensity);
        $this->addFlash('success', 'New job has been created!');

        return $this->redirect($adminUrlGenerator->setRoute('admin_word_density')->generateUrl());
    }

    /**
     * @Route("/word-density-job/{id}/export", name="admin_word_density_job_export")
     *
     * @param Job $job
     * @return Response
     */
    public function export(Job $job): Response
    {
        return new StreamedResponse(function () use ($job) {
            $this->wordDensityService->export($job);
        });
    }
}
