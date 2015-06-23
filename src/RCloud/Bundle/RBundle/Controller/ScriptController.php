<?php

namespace RCloud\Bundle\RBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use RCloud\Bundle\RBundle\Entity\Graph;
use RCloud\Bundle\RBundle\Entity\Script;
use RCloud\Bundle\UserBundle\Entity\User;

use Kachkaev\PHPR\RCore;
use Kachkaev\PHPR\Engine\CommandLineREngine;
use Kachkaev\PHPR\ROutputParser;



class ScriptController extends Controller
{
    /**
     * @Route("/script/run", name="run_script_ajax")
     * @Method({"POST"})
     * @Template()
     */
    public function runAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();

        $personalDir = 'upload/' . $user->getUsername();

        // on regarde si il y a bien un dossier pour l'utilisateur, si non, on le crée
        if (!is_dir($personalDir)) {
            mkdir($personalDir);
        }
        $directory = opendir($personalDir);
        $graphes = array();
        // Si d'anciennes images existent encore, on les supprime
        while ($file = readdir($directory)) {
            if (substr($file, -3) == 'png') {
                unlink($personalDir . '/' . $file);
            }
        }


        $script  = 'setwd("'.$personalDir.'");' . "\r\n"   ;
        $script .= 'options(device="png");' . "\r\n";
        $script .= $request->request->get('script'). "\r\n";
        $r = new RCore(new CommandLineREngine('/usr/bin/R'));
        $rProcess = $r->createInteractiveProcess();
        $rProcess->start();

        $rOutputParser = new ROutputParser();
        // $rProcess->setErrorSensitive(true);
        $rProcess->write($script);
        $results = $rProcess->getAllResult(true);



        // graphes
        $directory = opendir($personalDir);
        $graphes = array();
        while ($file = readdir($directory)) {
            if (substr($file, -3) == 'png') {
                $graphes[] = $personalDir . '/' . $file . '?' . mt_rand();
            }
        }
        return array(
            'results' => $results,
            'graphes' => $graphes
        );
    }


    /**
     * @Route("/script/save", name="save_script_ajax")
     * @Method({"POST"})
     */
    public function saveScript(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $folderRepository = $em->getRepository('RCloudRBundle:Folder');

        $scriptId = $request->request->get('id');
        $scriptContent = $request->request->get('content');
        $scriptName = $request->request->get('name');
        $parentId = $request->request->get('parentId');
        $user = $this->get('security.context')->getToken()->getUser();
        $folder =  $parentId === null ? null : $folderRepository->find($parentId);

        $response = array();

        if ($scriptId == null) {
            $script = new Script();
            $script->setName($scriptName);
            $script->setContent($scriptContent);
            $script->setOwner($user);
            $script->setFolder($folder);

            $em->persist($script);
            $em->flush();

            $response['meta']['code'] = 201;

            
            $permissionsManager = $this->get('r_cloud_r.permissionsmanager');
            $permissionsManager->setOwnerPermissions($script, $user);

            $folderParent = $script->getFolder();
            if ($folderParent) {
                $permissionsManager->inheritPermissions($script, $folderParent);
            }


        } else {
            $repository = $em->getRepository('RCloudRBundle:Script');
            $script = $repository->find($scriptId);
            $script->setContent($scriptContent);

            $response['meta']['code'] = 200;

            $em->flush();
        }


        $response['data']['scriptName'] = $script->getName();
        $response['data']['scriptId'] = $script->getId();
        $response['data']['editHref'] = $this->generateUrl('show_editor', array('scriptId' => $script->getId()));
        $response['data']['removeHref'] = $this->generateUrl('script_remove', array('scriptId' => $script->getId()));

        return new JsonResponse($response);
            
    }



    /**
     * @Route("/scripts/", name="scripts_list")
     * @Method({"GET"})
     * @Template()
     */
    public function listAction()
    {
       
        $user = $this->get('security.context')->getToken()->getUser();

        $scripts = $user->getScripts();

        return array(
            'scripts' => $scripts
        );
    }

    /**
     * @Route("/script/remove/{scriptId}", name="script_remove")
     * @Method({"GET"})
     */
    public function removeAction($scriptId)
    {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('RCloudRBundle:Script');

        $script = $repository->find($scriptId);

        if (null === $script) {
            // On définit un message flash
            $this->get('session')->getFlashBag()->add('error', 'Ce script n\'existe pas');
        } else {
            $em->remove($script);
            $em->flush();

            // On définit un message flash
            $this->get('session')->getFlashBag()->add('success', 'Script bien supprimé');
        }

        return $this->redirect($this->generateUrl('scripts_list'));
    }

    /**
     * @Route("/script/share/{scriptId}", name="script_share")
     */
    public function shareAction($scriptId, Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('user', 'text')            
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            //Get script
            $em = $this->getDoctrine()->getManager();
            $script = $em->getRepository('RCloudRBundle:Script')->find($scriptId);

            // Get data from form
            $data = $form->getData();
            

            $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($data['user']);            
            $securityId = UserSecurityIdentity::fromAccount($user);

            if ($user === NULL) {
                $error = "L'utilisateur n'a pas été trouvé";
            }
            else {
                $permissionsManager = $this->get('r_cloud_r.permissionsmanager');
                $permissionsManager->changePermissions($script, $securityId, MaskBuilder::MASK_EDIT);
                
                if ($script->getFolder() === NULL){
                    return $this->redirect($this->generateUrl('folders_list'));
                }
                else {
                    return $this->redirect($this->generateUrl('folders_list', array('id' => $script->getFolder()->getId())));
                }
            }         

        }

        return $this->render('RCloudRBundle::shareForm.html.twig', array(
            'form' => $form->createView(),
            'error' => isset($error)?$error:false,
        ));
    }
}
