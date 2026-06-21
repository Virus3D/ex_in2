<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'app_admin')]
final class DashboardController extends AbstractDashboardController
{
    private const string ICON_LIST = 'fas fa-list';

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }// end __construct()

    /**
     * @inheritDoc
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }// end index()

    /**
     * @inheritDoc
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->translator->trans('title'))
            ->setTranslationDomain('admin')
            ->useEntityTranslations();
    }// end configureDashboard()

    /**
     * @inheritDoc
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(CardCategoryCrudController::class, 'Card Category', self::ICON_LIST);
        yield MenuItem::linkTo(CardCrudController::class, 'Card', self::ICON_LIST);
        yield MenuItem::subMenu('Communal services', 'fas fa-cogs')->setSubItems(
            [
                MenuItem::linkTo(PlaceCrudController::class, 'Place', self::ICON_LIST),
                MenuItem::linkTo(ServiceCrudController::class, 'Services', self::ICON_LIST),
            ]
        );
        yield MenuItem::linkTo(SubscriptionCrudController::class, 'Subscriptions', self::ICON_LIST);
    }// end configureMenuItems()
}// end class
