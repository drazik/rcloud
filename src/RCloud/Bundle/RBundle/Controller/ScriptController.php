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
        $script = $request->request->get('script');

        return new JsonResponse(array('result' => $script));
    }

    /**
     * @Route("/script/save", name="save_script_ajax")
     * @Method({"POST"})
     */
    public function saveScript(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $scriptId = $request->request->get('id');
        $scriptContent = $request->request->get('content');
        $scriptName = $request->request->get('name');
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
