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
     * @Route("/script/new/save", name="save_new_script_ajax")
     * @Method({"POST"})
     */
    public function saveNewScript(Request $request)
    {
        $scriptName = $request->request->get('scriptName');
        $scriptContent = $request->request->get('scriptContent');
        $user = $this->get('security.context')->getToken()->getUser();

        $script = new Script();
        $script->setName($scriptName);
        $script->setContent($scriptContent);
        $script->setOwner($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($script);
        $em->flush();

        return new Response($script->getId());
    }

    /**
     * @Route("script/existing/save", name="save_existing_script_ajax")
     * @Method({"POST"})
     */
    public function saveExistingScript(Request $request)
    {
        $scriptId = $request->request->get('scriptId');
        $scriptContent = $request->request->get('scriptContent');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('RCloudRBundle:Script');

        $script = $repository->find($scriptId);

        $script->setContent($scriptContent);

        $em->flush();

        return new Response('ok');
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
     * @Route("/script/remove", name="script_remove_ajax")
     * @Method({"POST"})
     */
    public function removeAction(Request $request)
    {
        $scriptId = $scriptId = $request->request->get('scriptId');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('RCloudRBundle:Script');

        $script = $repository->find($scriptId);

        $success = array(
            'success' => true
        );

        if (null === $script) {
            $success['success'] = false;
            $success['message'] = 'Ce script n\'existe pas';
        } else {
            $em->remove($script);
            $em->flush();
        }

        return new JsonResponse($success);
    }
}
