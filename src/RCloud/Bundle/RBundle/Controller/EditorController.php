<?php

namespace RCloud\Bundle\RBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EditorController extends Controller
{
    /**
     * @Route("/editor", name="show_editor")
     * @Template()
     */
    public function showAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $scripts = $user->getScripts();

        $packages = array(
            'arules', 'biclust', 'FactoMineR',
            'flexmix', 'klaR', 'kohonen', 'nnet',
            'randomForest', 'Rmixmod', 'rpart',
            'RSQLite', 'tree'
        );

        return array(
            'scripts' => $scripts,
            'packages' => $packages
        );
    }
}
