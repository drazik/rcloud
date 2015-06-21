<?php
// src/RCloud/Bundle/UserBundle/Controller/GroupController.php

namespace RCloud\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\GroupController as BaseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GroupController extends BaseController
{
    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('RCloudUserBundle:Group');

        $ownedGroups = $repository->findBy(array('owner' => $user));
        $belongToGroups = $user->getGroups();

        return $this->render('FOSUserBundle:Group:list.html.twig', array(
            'ownedGroups' => $ownedGroups,
            'belongToGroups' => $belongToGroups
        ));
    }

    /**
     * @Route("/invite", name="r_cloud_user_group_invite")
     * @Template()
     */
    public function inviteAction()
    {
        return array();
    }
}
