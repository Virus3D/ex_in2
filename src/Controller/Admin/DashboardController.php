<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Entity\CardCategory;
use App\Entity\Place;
use App\Entity\Service;
use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractDashboardController
{
    private const string ICON_LIST = 'fas fa-list';

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }//end index()

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ex In2')
        ;
    }//end configureDashboard()

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Card Category', self::ICON_LIST, CardCategory::class);
        yield MenuItem::linkToCrud('Card', self::ICON_LIST, Card::class);
        yield MenuItem::subMenu('Communal services', 'fas fa-cogs')->setSubItems(
            [
                MenuItem::linkToCrud('Place', self::ICON_LIST, Place::class),
                MenuItem::linkToCrud('Service', self::ICON_LIST, Service::class),
            ]
        );
        yield MenuItem::linkToCrud('Subscription', self::ICON_LIST, entityFqcn: Subscription::class);
    }//end configureMenuItems()
}//end class
