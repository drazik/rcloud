<?php

// src/RCloud/Bundle/UserBundle/EventListener/GroupListener.php

namespace RCloud\Bundle\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GroupEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContext;

class GroupListener implements EventSubscriberInterface
{
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::GROUP_CREATE_INITIALIZE => 'onGroupCreateInitialize'
        );
    }

    public function onGroupCreateInitialize(GroupEvent $event)
    {
        $group = $event->getGroup();
        $user = $this->securityContext->getToken()->getUser();

        $group->setOwner($user);
        $user->addGroup($group);
    }
}
