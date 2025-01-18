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
use App\Entity\Regular;
use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractDashboardController
{
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
        yield MenuItem::linkToCrud('Card Category', 'fas fa-list', CardCategory::class);
        yield MenuItem::linkToCrud('Card', 'fas fa-list', Card::class);
        yield MenuItem::subMenu('Communal services', 'fas fa-cogs')->setSubItems(
            [
                MenuItem::linkToCrud('Place', 'fas fa-list', Place::class),
                MenuItem::linkToCrud('Service', 'fas fa-list', Service::class),
            ]
        );
        yield MenuItem::linkToCrud('Regular', 'fas fa-list', entityFqcn: Regular::class);
    }//end configureMenuItems()
}//end class
