<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardCategory;
use App\Entity\Place;
use App\Form\FilterType;
use App\Form\PdfUploadType;
use App\Helper\FilterDataHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    /**
     * Главная страница.
     */
    #[Route('/', name: 'app_main', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, FilterDataHelper $filterData): Response
    {
        return $this->render(
            'main/index.html.twig',
            [
                'formFilter'    => $this->filterForm($request, $filterData),
                'formPDFUpload' => $this->createForm(
                    PdfUploadType::class,
                    null,
                    [
                        'action' => $this->generateUrl('parse_pdf'),
                        'method' => 'POST',
                    ]
                ),
                'categories'    => $entityManager->getRepository(CardCategory::class)->findAll(),
                'placeList'     => $entityManager->getRepository(Place::class)->findAll(),
            ]
        );
    }//end index()

    /**
     * Сохраняет фильтр
     */
    #[Route('/', name: 'app_main_save', methods: ['POST'])]
    public function filterFormSave(Request $request): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filterData = $form->getData();

            $session = $request->getSession();
            $session->set('filter_data', $filterData);
        }

        return $this->redirectToRoute('app_main');
    }//end filterFormSave()

    /**
     * Возвращает форму фильтра.
     */
    private function filterForm(Request $request, FilterDataHelper $filterData): FormInterface
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        $form->setData($filterData->toArray());

        return $form;
    }//end filterForm()
}//end class
