<?php

namespace RCloud\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="show_dashboard")
     * @Template()
     */
    public function showAction()
    {
        $dashboardItems = array();
        $dashboardItems[] = array(
            'title' => 'Editeur',
            'href' => $this->generateUrl('show_editor'),
            'icon' => 'code'
        );

        $dashboardItems[] = array(
            'title' => 'Vos scripts',
            'href' => $this->generateUrl('scripts_list'),
            'icon' => 'list-ul'
        );

        $dashboardItems[] = array(
            'title' => 'Profil',
            'href' => $this->generateUrl('fos_user_profile_show'),
            'icon' => 'user'
        );

        $dashboardItems[] = array(
            'title' => 'Paramètres',
            'href' => '#',
            'icon' => 'cogs'
        );

        $dashboardItems[] = array(
            'title' => 'Déconnexion',
            'href' => $this->generateUrl('fos_user_security_logout'),
            'icon' => 'sign-out'
        );

        return array(
            'dashboardItems' => $dashboardItems
        );
    }
}
