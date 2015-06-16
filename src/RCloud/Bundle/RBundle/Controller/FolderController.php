<?php

namespace RCloud\Bundle\RBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $em = $this->getDoctrine()->getManager();
        // Récupération des Folders du user connecté
        $user = $this->get('security.context')->getToken()->getUser();
        $folderRepository = $em->getRepository('RCloudRBundle:Folder');
        $folders = $folderRepository->getFolders($user, $id);

        $scriptRepository = $em->getRepository('RCloudRBundle:Script');

        // Le Folder courant est-il la racine (null) ou un Folder existant ?
        $currentFolder = $id === null ? null : $folderRepository->find($id);

        // Initialisation des objets du breadcrumb
        $breadcrumbItems = array();

        // Si on n'est pas à la racine, alors on a des objets à mettre dans le breadcrumb
        if ($currentFolder !== null) {
            $breadcrumbItems[] = $currentFolder;

            // Tant que le Folder courant a des parents, on les met dans le breadcrumb
            $folder = $currentFolder->getParent();

            if ($folder !== null) {
                do {
                    $breadcrumbItems[] = $folder;
                } while (($folder = $folder->getParent()) !== null);
            }

            // On reverse le tableau pour que les items soient dans le sens parent > enfant
            // et non pas dans le sens enfant > parent
            $breadcrumbItems = array_reverse($breadcrumbItems);
        }

        if ($currentFolder === null) {
            $currentScripts = $scriptRepository->findBy(array(
                'folder' => null,
                'owner' => $user
            ));
        } else {
            $currentScripts = $currentFolder->getScripts();
        }

        return array(
            'folders' => $folders,
            'currentFolder' => $currentFolder,
            'currentScripts' => $currentScripts,
            'breadcrumbItems' => $breadcrumbItems
        );
    }

    /**
     * @Route("/folder/add", name="folder_add")
     */
    public function addAction(Request $request) {
        // On récupère le Folder parent du Folder qu'on veut créer
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('RCloudRBundle:Folder');
        $folderRequest = $request->request->get('folder');
        $parentFolder = $repository->find(intval($folderRequest['parentId']));

        // On récupère le user connecté
        $user = $this->get('security.context')->getToken()->getUser();

        // On créée le nouveau Folder
        $newFolder = new Folder();
        $newFolder->setName($folderRequest['name']);
        $newFolder->setParent($parentFolder);
        $newFolder->setOwner($user);

        $em->persist($newFolder);
        $em->flush();

        return new JsonResponse(array(
            'meta' => array('code' => 201),
            'data' => array(
                'id' => $newFolder->getId(),
                'name' => $newFolder->getName(),
                'href' => $this->generateUrl('folders_list', array('id' => $newFolder->getId()))
            )
        ));
    }

    /**
     * oute("/folder/edit/{id}", name="folder_edit")
     */
    /*public function saveAction($id = null){
        //on récupère l'entity manager
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if ($id == null) {
            $folder = new Folder;
        } else {
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
    }*/

}
