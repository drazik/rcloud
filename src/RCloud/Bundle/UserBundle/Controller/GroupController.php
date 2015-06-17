<?php
// src/RCloud/Bundle/UserBundle/Controller/GroupController.php

namespace RCloud\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\GroupController as BaseController;

class GroupController extends BaseController
{
    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('RCloudUserBundle:Group');

        $groups = $repository->findBy(array('owner' => $user));

        return $this->render('FOSUserBundle:Group:list.html.twig', array(
            'groups' => $groups
        ));
    }
}
