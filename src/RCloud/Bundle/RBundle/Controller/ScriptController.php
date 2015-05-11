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

use RCloud\Bundle\RBundle\Entity\Graph;
use RCloud\Bundle\RBundle\Entity\Script;

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

        $scriptId = $request->request->get('scriptId');
        $scriptContent = $request->request->get('scriptContent');
        $scriptName = $request->request->get('scriptName');
        $user = $this->get('security.context')->getToken()->getUser();

        $response = array();

        if($scriptId == null) {
            $script = new Script();
            $script->setName($scriptName);
            $script->setContent($scriptContent);
            $script->setOwner($user);

            $em->persist($script);

            $response['meta']['code'] = 201;
        }
        else {
            $repository = $em->getRepository('RCloudRBundle:Script');
            $script = $repository->find($scriptId);

            $script->setContent($scriptContent);

            $response['meta']['code'] = 200;
        }

        $em->flush();

        $response['data']['scriptName'] = $script->getName();
        $response['data']['scriptId'] = $script->getId();

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
     * @Route("/exec-script/", name="exec_script")
     * @Method({"GET"})
     * @Template()
     */
    /*public function executeScriptAction()
    {

        $r = new RCore(new CommandLineREngine('/usr/bin/R'));
        $rProcess = $r->createInteractiveProcess();
        $rProcess->start();

        $rOutputParser = new ROutputParser();
        $rProcess->write('21 + 21');
        $result = $rProcess->getAllOutput();

        echo $result;

        exit();
        return $this->redirect($this->generateUrl('scripts_list'));
    }*/
}
