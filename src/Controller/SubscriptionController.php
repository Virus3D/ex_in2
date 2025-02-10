<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Spend;
use App\Entity\Subscription;
use App\Entity\SubscriptionPayment;
use App\Form\SubscriptionPaymentType;
use App\Service\DueSubscriptionService;
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

final class SubscriptionController extends AbstractController
{
    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ServiceAccountService $serviceAccountService,
        private ServicePaymentService $servicePaymentService,
        private DueSubscriptionService $dueSubscriptionService,
    ) {}//end __construct()

    #[Route('/subscription', name: 'app_subscription_view')]
    public function index(): Response
    {
        $this->dueSubscriptionService->execute();
        $subscriptions = $this->entityManager->getRepository(Subscription::class)->findBy([], ['nextPaymentDate' => 'asc']);

        return $this->render(
            'subscription/view.html.twig',
            [
                'subscriptions' => $subscriptions,
            ]
        );
    }//end index()

    #[Route('/subscription/form', name: 'app_subscription_form')]
    public function viewForm(): Response
    {
        return $this->render(
            'subscription/form.html.twig',
            [
                'formSubscription' => $this->formPayment(),
            ]
        );
    }//end viewForm()

    private function formPayment(): FormInterface
    {
        return $this->createForm(
            SubscriptionPaymentType::class,
            null,
            [
                'action' => $this->generateUrl('app_subscription_payment'),
            ]
        );
    }//end formPayment()

    #[Route('/subscription/payment', name: 'app_subscription_payment', methods: ['POST'])]
    public function payment(Request $request): Response
    {
        $formPayment = $this->formPayment();

        $formPayment->handleRequest($request);

        if ($formPayment->isSubmitted() && $formPayment->isValid())
        {
            $date = new DateTime();

            $amount         = $formPayment->get('amount')->getData();
            $subscription   = $formPayment->get('subscrip')->getData();
            $servicePayment = new SubscriptionPayment();
            $servicePayment
                ->setCard($formPayment->get('card')->getData())
                ->setSubscrip($subscription)
                ->setAmount($amount)
                ->setDate($date)
            ;
            $this->entityManager->persist($servicePayment);

            $subscription->setBalance($subscription->getBalance() + $amount);

            $spend = new Spend();
            $spend
                ->setDate($date)
                ->setCard($formPayment->get('card')->getData())
                ->setBalance($amount)
                ->setComment('Sub: '.$subscription->getName())
            ;
            $this->entityManager->persist($spend);

            $this->entityManager->flush();
        }//end if

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
        {
            return $this->index();
        }

        return $this->redirectToRoute('app_main', [], 303);
    }//end payment()
}//end class
