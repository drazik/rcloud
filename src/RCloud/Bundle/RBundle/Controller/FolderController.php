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

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

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
        //$folders = $folderRepository->getFolders($user, $id);

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

        $aclProvider = $this->get('security.acl.provider');

        //Get items owned by user
        $currentScripts = $this->listOwnedItems($user, 'RCloud\Bundle\RBundle\Entity\Script', $scriptRepository, $currentFolder);
        $folders = $this->listOwnedItems($user, 'RCloud\Bundle\RBundle\Entity\Folder', $folderRepository, $currentFolder);
        
        //Get items shared with user
        $sharedScripts = $this->listSharedItems($user, 'RCloud\Bundle\RBundle\Entity\Script', $scriptRepository, $currentFolder);
        $sharedFolders = $this->listSharedItems($user, 'RCloud\Bundle\RBundle\Entity\Folder', $folderRepository, $currentFolder);
        

        return array(
            'folders' => isset($folders)?$folders:false,
            'currentFolder' => $currentFolder,
            'currentScripts' => isset($currentScripts)?$currentScripts:false,
            'breadcrumbItems' => $breadcrumbItems,
            'sharedScripts' => isset($sharedScripts)?$sharedScripts:false,            
            'sharedFolders' => isset($sharedFolders)?$sharedFolders:false,
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

       
        $permissionsManager = $this->get('r_cloud_r.permissionsmanager');
        $permissionsManager->setOwnerPermissions($newFolder, $user);

        $folderParent = $newFolder->getParent();
        if ($folderParent) {
            $permissionsManager->inheritPermissions($newFolder, $folderParent);
        }

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
     * @Route("folder/share/{folderId}", name="folder_share")
     */
    public function shareAction($folderId, Request $request) {
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $groupsCurrentUser = $currentUser->getGroups();
        
        $form = $this->createFormBuilder()
            ->add('user', 'text', array(
                'required' => false)) 
            ->add('group', 'entity', array(
                'choices'   => $groupsCurrentUser,
                'required'  => false,
                'class' => 'RCloud\Bundle\UserBundle\Entity\Group',
                'property' => 'name'
            ))       
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            //Get script
            $em = $this->getDoctrine()->getManager();
            $folder = $em->getRepository('RCloudRBundle:Folder')->find($folderId);

            // Get data from form
            $data = $form->getData();

          /*  $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($data['user']);

            if ($user === NULL) {
                $error = "L'utilisateur n'a pas été trouvé";
            }
            else {
                $permissionsManager = $this->get('r_cloud_r.permissionsmanager');
                $this->shareFolder($folder, $user, $permissionsManager);

                return $this->redirect($this->generateUrl('folders_list', array('id' => $folder->getId())));                
            } */ 


            /////// NEW

            if ($data['user'] === NULL && $data['group'] === NULL) {
                $error = "Veuillez renseigner un user ou un groupe";
            }
            else {

                $permissionsManager = $this->get('r_cloud_r.permissionsmanager');
                if ($data['user'] != NULL) {
                    $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($data['user']);            
                    $securityId = UserSecurityIdentity::fromAccount($user);

                    if ($user === NULL) {
                        $error = "L'utilisateur n'a pas été trouvé";
                    }
                    else {                        
                        $this->shareFolder($folder, $user, $permissionsManager);               
                    } 
                } 

                if ($data['group'] != NULL) {
                    $group = $data['group'];
                    foreach ($group->getUsers() as $groupUser) {
                        if ($groupUser != $currentUser) {
                            $this->shareFolder($folder, $groupUser, $permissionsManager);                
                        }

                    }
                }

            }
            if (!isset($error)) {
                return $this->redirect($this->generateUrl('folders_list', array('id' => $folder->getId())));
                
            }        

        }

        return $this->render('RCloudRBundle::shareForm.html.twig', array(
            'form' => $form->createView(),
            'error' => isset($error)?$error:false,
        ));
    }

    private function shareFolder($currentFolder, $user, $permissionsManager) {

        $securityId = UserSecurityIdentity::fromAccount($user);
        $permissionsManager->changePermissions($currentFolder, $securityId, MaskBuilder::MASK_EDIT);

        if($currentFolder->getScripts()) {
            foreach ($currentFolder->getScripts() as $script) {
                $permissionsManager->changePermissions($script, $securityId, MaskBuilder::MASK_EDIT);
            }
        }

        if($currentFolder->getFolders()) {
            foreach ($currentFolder->getFolders() as $folder) {
                $this->shareFolder($folder, $user, MaskBuilder::MASK_EDIT, $permissionsManager);
            }
        }
    }

    private function listSharedItems($user, $class, $repository, $currentFolder) {
        $currentItems = array();
        $securityContext = $this->get('security.context');

        $aclProvider = $this->get('security.acl.provider');
        $objectIdentities = $aclProvider->findObjectIdentitiesForUser($user, MaskBuilder::MASK_VIEW, $class);

        foreach ($objectIdentities as $objectIdentity) {
            $id = $objectIdentity->getIdentifier(); // this is your database primary key
            $item = $repository->findOneById($id); 

            if ($securityContext->isGranted('OWNER', $item) === false) {    
                if ($class == 'RCloud\Bundle\RBundle\Entity\Script') {
                    $folderParent = $item->getFolder();
                    if ($item->getFolder() == $currentFolder ||                         
                        $currentFolder == null && $folderParent != null && $securityContext->isGranted('VIEW', $folderParent) === false) {
                        $currentItems[] = $item;
                    }
                }
                else if ($class == 'RCloud\Bundle\RBundle\Entity\Folder') {
                    if ($item->getParent() == $currentFolder) {
                        $currentItems[] = $item;
                    }
                } 
            }
            
        }

        return $currentItems;

    }


    private function listOwnedItems($user, $class, $repository, $currentFolder) {
        $currentItems = array();
        $securityContext = $this->get('security.context');

        $aclProvider = $this->get('security.acl.provider');
        $objectIdentities = $aclProvider->findObjectIdentitiesForUser($user, MaskBuilder::MASK_OWNER, $class);

        foreach ($objectIdentities as $objectIdentity) {
            $id = $objectIdentity->getIdentifier(); // this is your database primary key
            $item = $repository->findOneById($id); 


            
           if ($class == 'RCloud\Bundle\RBundle\Entity\Script') {
                if ($item->getFolder() == $currentFolder) {
                    $currentItems[] = $item;
                }
            }
            else if ($class == 'RCloud\Bundle\RBundle\Entity\Folder') {
                if ($item->getParent() == $currentFolder) {
                    $currentItems[] = $item;
                }
            } 
            

        }

        return $currentItems;

    }
}
