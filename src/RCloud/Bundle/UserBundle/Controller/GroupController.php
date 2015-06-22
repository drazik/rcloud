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
        $group = $groupRepository->findOneBy(array('name' => $groupName));

        $form = $this->createFormBuilder()
            ->add('username', 'text')
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserByUsername($formData['username']);

            if ($user !== null) {
                $group->addUser($user);
                $em->flush();

                $this->addFlash('success', $user->getUsername() . ' a bien été ajouté au groupe');
                return $this->redirectToRoute('fos_user_group_show', array('groupName' => $group->getName()));
            } else {
                $error = 'L\'utilisateur ' . $formData['username'] . ' n\'existe pas';
            }
        }

        return array(
            'form' => $form->createView(),
            'error' => isset($error) ? $error : false
        );
    }

    /**
     * @Route("/{groupName}/leave", name="r_cloud_user_group_leave")
     * @Template()
     */
    public function leaveAction(Request $request, $groupName)
    {
        $em = $this->getDoctrine()->getManager();
        $groupRepository = $em->getRepository('RCloudUserBundle:Group');
        $group = $groupRepository->findOneBy(array('name' => $groupName));

        $user = $this->get('security.context')->getToken()->getUser();

        if ($group->getOwner() === $user) {
            $this->addFlash('error', 'Impossible de quitter un groupe dont vous êtes le / la propriétaire');
            return $this->redirectToRoute('fos_user_group_list');
        }

        $form = $this->createFormBuilder()->getForm();

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $group->removeUser($user);
                $em->flush();

                $this->addFlash('success', 'Vous avez quitté le groupe' . $groupName);
                return $this->redirectToRoute('fos_user_group_list');
            }
        }

        return array(
            'form' => $form->createView()
        );
    }
}
