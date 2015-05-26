<?php

namespace RCloud\Bundle\RBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use RCloud\Bundle\RBundle\Entity\Folder;
use RCloud\Bundle\RBundle\Form\FolderType;

class FolderController extends Controller
{
	/**
     * @Route("/folders/{id}", name="folders_list")
     * @Template()
     */
    public function listAction($id = null)
    {
        $user = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
        $folders = $em->getRepository('RCloudRBundle:Folder')->getFolders($user, $id);

        return array(
            'folders' => $folders
        );
    }

	/**
     * @Route("/folder/edit/{id}", name="folder_edit")
     */
    public function saveAction($id = null){
        //on récupère l'entity manager
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if ($id == null) {
            $folder = new Folder;
        }
        else {
            $folder = $em->findOneById($id);
        }

        $form = $this->createForm(new FolderType, $folder);
        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $form->bind($request);

            if($form->isValid()) {
            	$folder->setOwner($user);
                $em->persist($folder);
                $em->flush();

                return $this->redirect($this->generateUrl('folders_list', array('id' => $folder->getId())));
            }

        }

        return $this->render('RCloudRBundle:Folder:edit.html.twig', array('form' => $form->createView(), 'folder' => $folder));
    }

}
