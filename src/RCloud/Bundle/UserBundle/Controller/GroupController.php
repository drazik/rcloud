<?php
// src/RCloud/Bundle/UserBundle/Controller/GroupController.php

namespace RCloud\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\GroupController as BaseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/{groupName}/invite", name="r_cloud_user_group_invite")
     * @Template()
     */
    public function inviteAction(Request $request, $groupName)
    {
        $em = $this->getDoctrine()->getManager();
        $groupRepository = $em->getRepository('RCloudUserBundle:Group');
        $group = $groupRepository->findBy(array('name' => $groupName));

        $form = $this->createFormBuilder()
            ->add('user', 'text')
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            // récupérer le user, lui ajouter le groupe
            // persister le user

            return $this->redirectToRoute('fos_user_group_show', array(
                'groupName' => $groupName
            ));
        }

        return array(
            'form' => $form->createView()
        );
    }
}
