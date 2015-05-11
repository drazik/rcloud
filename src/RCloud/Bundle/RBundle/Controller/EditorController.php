<?php

namespace RCloud\Bundle\RBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class EditorController extends Controller
{
    /**
     * @Route("/editor/{scriptId}", name="show_editor", defaults={"scriptId": null}, requirements={"scriptId": "\d+"})
     * @Template()
     */
    public function showAction($scriptId = null)
    {
        if ($scriptId) {
            $user = $this->get('security.context')->getToken()->getUser();
            $scripts = $user->getScripts();

            $script = $scripts->filter(function($entry) use ($scriptId) {
                return $entry->getId() == $scriptId;
            });

            $script = $script->first();
        } else {
            $script = null;
        }

        return array('script' => $script);
    }
}
