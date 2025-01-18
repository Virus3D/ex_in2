<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Place;
use App\Entity\ServiceAccount;
use App\Entity\ServicePayment;
use App\Entity\Spend;
use App\Form\ServiceAccountType;
use App\Form\ServicePaymentType;
use App\Helper\FilterDataHelper;
use App\Service\ServiceAccountService;
use App\Service\ServicePaymentService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

final class PlaceController extends AbstractController
{
    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ServiceAccountService $serviceAccountService,
        private ServicePaymentService $servicePaymentService,
    ) {}//end __construct()

    #[Route('/place/{id}', name: 'app_place_view')]
    public function index(int $id, Request $request): Response
    {
        FilterDataHelper::getFilterData($request);
        $place = $this->entityManager->getRepository(Place::class)->findWithService($id);

        return $this->renderTable($place);
    }//end index()

    private function renderTable(Place $place): Response
    {
        $services   = $place->getServices();
        $account    = [];
        $payment    = [];
        $total      = [];
        $totalMonth = array_fill(1, 12, 0);
        $accountDB  = $this->serviceAccountService->handle($place, FilterDataHelper::$year);
        $paymentDB  = $this->servicePaymentService->handle($place, FilterDataHelper::$year);

        foreach ($services as $service)
        {
            /** @var int */
            $serviceId = $service->getId();

            $account[$serviceId] = ($accountDB[$serviceId] ?? []) + array_fill(1, 12, 0);
            $payment[$serviceId] = ($paymentDB[$serviceId] ?? []) + array_fill(1, 12, []);

            $value = array_column($payment[$serviceId], 'amount');

            $totalService      = array_sum($account[$serviceId]);
            $total[$serviceId] = [
                'total' => $totalService,
                'debt'  => (int) $totalService - (int) array_sum($value),
            ];
            foreach ($account[$serviceId] as $month => $value)
            {
                $totalMonth[$month] += $value;
            }
        }

        return $this->render(
            'place/view.html.twig',
            [
                'place'      => $place,
                'accounts'   => $account,
                'payments'   => $payment,
                'total'      => $total,
                'totalMonth' => $totalMonth,
            ]
        );
    }//end renderTable()

    #[Route('/place/{id}/form', name: 'app_place_form')]
    public function viewForm(int $id, Request $request): Response
    {
        FilterDataHelper::getFilterData($request);
        $place = $this->entityManager->getRepository(Place::class)->findWithService($id);

        return $this->render(
            'place/form.html.twig',
            [
                'place'       => $place,
                'formAccount' => $this->formAccount($place->getId()),
                'formPayment' => $this->formPayment($place->getId()),
            ]
        );
    }//end viewForm()

    private function formAccount(int $id): FormInterface
    {
        return $this->createForm(
            ServiceAccountType::class,
            null,
            [
                'action' => $this->generateUrl('app_place_service_account', ['id' => $id]),
            ]
        );
    }//end formAccount()

    private function formPayment(int $id): FormInterface
    {
        return $this->createForm(
            ServicePaymentType::class,
            null,
            [
                'action' => $this->generateUrl('app_place_service_payment', ['id' => $id]),
            ]
        );
    }//end formPayment()

    #[Route('/place/{id}/account', name: 'app_place_service_account', methods: ['POST'])]
    public function account(int $id, Request $request): Response
    {
        FilterDataHelper::getFilterData($request);
        $place = $this->entityManager->getRepository(Place::class)->findWithService($id);

        $formAccount = $this->formAccount($place->getId());

        $formAccount->handleRequest($request);

        if ($formAccount->isSubmitted() && $formAccount->isValid())
        {
            $serviceAccount = new ServiceAccount();
            $serviceAccount
                ->setPlace($place)
                ->setService($formAccount->get('service')->getData())
                ->setYear(FilterDataHelper::$year)
                ->setMonth($formAccount->get('month')->getData())
                ->setAmount($formAccount->get('amount')->getData())
            ;
            $this->entityManager->persist($serviceAccount);
            $this->entityManager->flush();
        }//end if

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
        {
            return $this->renderTable($place);
        }

        return $this->redirectToRoute('app_main', [], 303);
    }//end account()

    #[Route('/place/{id}/payment', name: 'app_place_service_payment', methods: ['POST'])]
    public function payment(int $id, Request $request): Response
    {
        FilterDataHelper::getFilterData($request);
        $place = $this->entityManager->getRepository(Place::class)->findWithService($id);

        $formPayment = $this->formPayment($place->getId());

        $formPayment->handleRequest($request);

        if ($formPayment->isSubmitted() && $formPayment->isValid())
        {
            $date = new DateTime();
            $servicePayment = new ServicePayment();
            $servicePayment
                ->setPlace($place)
                ->setService($formPayment->get('service')->getData())
                ->setYear(FilterDataHelper::$year)
                ->setMonth($formPayment->get('month')->getData())
                ->setAmount($formPayment->get('amount')->getData())
                ->setDate($date)
            ;
            $this->entityManager->persist($servicePayment);

            $spend = new Spend();
            $spend
                ->setDate($date)
                ->setCard($formPayment->get('card')->getData())
                ->setBalance($formPayment->get('amount')->getData())
                ->setComment('Service: '.$formPayment->get('service')->getData()->getName())
            ;
            $this->entityManager->persist($spend);

            $this->entityManager->flush();
        }//end if

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
        {
            return $this->renderTable($place);
        }

        return $this->redirectToRoute('app_main', [], 303);
    }//end payment()
}//end class
