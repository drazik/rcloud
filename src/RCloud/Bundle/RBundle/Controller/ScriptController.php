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

class ScriptController extends Controller
{
    /**
     * @Route("/script/run", name="run_script_ajax")
     * @Method({"POST"})
     * @Template()
     */
    public function runAction(Request $request)
    {
        $script = 'options(device="png");' . "\r\n" . $request->request->get('script');
        $user = $this->get('security.context')->getToken()->getUser();

        $personalDir = 'upload/' . $user->getUsername();
        $inputFileName = $personalDir . '/input.R';
        $outputFileName = $personalDir . '/output.res';

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

        // écriture de input.R
        $inputFile = fopen($inputFileName, 'a');
        fputs($inputFile, $script);
        fclose($inputFile);

        // exécution du script
        exec('cd ' . $personalDir . ' && R CMD BATCH --save --quiet input.R output.res');

        // lecture de output.res
        $outputFile = fopen($outputFileName, 'r');

        $result = '';

        while ($line = fgets($outputFile)) {
            $result .= nl2br($line);
        }

        fclose($outputFile);

        unlink($inputFileName);
        unlink($outputFileName);

        // graphes
        $directory = opendir($personalDir);
        $graphes = array();
        while ($file = readdir($directory)) {
            if (substr($file, -3) == 'png') {
                $graphes[] = $personalDir . '/' . $file;
            }
        }

        return array(
            'result' => $result,
            'graphes' => $graphes
        );
        // TODO utiliser une JsonResponse
        // return new JsonResponse(array(
        //     'result' => $result,
        //     'graphes' => $graphes
        // ));
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
}
