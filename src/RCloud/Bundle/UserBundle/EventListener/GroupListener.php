<?php

// src/RCloud/Bundle/UserBundle/EventListener/GroupListener.php

namespace RCloud\Bundle\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GroupListener implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::GROUP_CREATE_SUCCESS => 'onGroupCreateSuccess'
        );
    }

    public function onGroupCreateSuccess(FormEvent $event)
    {

    }
}
