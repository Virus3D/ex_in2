<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardCategory;
use App\Entity\Place;
use App\Form\FilterType;
use App\Form\ReceiptType;
use App\Form\SpendType;
use App\Form\TransferType;
use App\Helper\FilterDataHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render(
            'main/index.html.twig',
            [
                'formFilter'   => $this->filterForm($request),
                'categories'   => $entityManager->getRepository(CardCategory::class)->findAll(),
                'formReceipt'  => $this->formReceipt(),
                'formSpend'    => $this->formSpend(),
                'formTransfer' => $this->formTransfer(),
                'placeList'    => $entityManager->getRepository(Place::class)->findAll(),
            ]
        );
    }//end index()

    private function formReceipt(): FormInterface
    {
        return $this->createForm(
            ReceiptType::class,
            null,
            [
                'action' => $this->generateUrl('app_receipt_add'),
            ]
        );
    }//end formReceipt()

    private function formSpend(): FormInterface
    {
        return $this->createForm(
            SpendType::class,
            null,
            [
                'action' => $this->generateUrl('app_spend_add'),
            ]
        );
    }//end formSpend()

    private function formTransfer(): FormInterface
    {
        return $this->createForm(
            TransferType::class,
            null,
            [
                'action' => $this->generateUrl('app_transfer_add'),
            ]
        );
    }//end formTransfer()

    private function filterForm(Request $request): FormInterface
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        $filterData = FilterDataHelper::getFilterData($request);

        // Обработка формы
        if ($form->isSubmitted() && $form->isValid())
        {
            $filterData = $form->getData();
            // Сохранение фильтра в сессию
            $session = $request->getSession();
            $session->set('filter_data', $filterData);
        }

        if (! $form->isSubmitted())
        {
            $form->setData($filterData);
        }

        return $form;
    }//end filterForm()
}//end class
