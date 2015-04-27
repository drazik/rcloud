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
