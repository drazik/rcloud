<?php

namespace RCloud\Bundle\RBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use RCloud\Bundle\RBundle\Entity\Script;

class EditorController extends Controller
{
    /**
     * @Route("/editor/{scriptId}", name="show_editor", defaults={"scriptId": null}, requirements={"scriptId": "\d+"})
     * @Template()
     */
    public function showAction($scriptId = null)
    {
        
        

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('RCloudRBundle:Script');

        $script = $repository->find($scriptId);

        if ($script) {
            $securityContext = $this->get('security.context');
            if ($securityContext->isGranted('EDIT', $script) === false ) { 
                throw new AccessDeniedException("Oups, vous n'êtes pas autorisé à accéder à ce script");
            }
        }

        return array(
            'script' => $script
        );
    }
}
