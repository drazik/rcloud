<?php

// src/RCloud/Bundle/RBundle/Service/PermissionsManager.php

namespace RCloud\Bundle\RBundle\Service;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class PermissionsManager {
	private $aclProvider;

	public function __construct($aclProvider) {
		$this->aclProvider = $aclProvider;
	}	

	public function changePermissions($object, $securityId, $mask) {

	    $acl = $this->aclProvider->findAcl(ObjectIdentity::fromDomainObject($object));

        $hasPermissions = false;
        foreach($acl->getObjectAces() as $index=>$ace) {
            if ($ace->getSecurityIdentity()->equals($securityId)) {
                $acl->updateObjectAce($index, $mask);
                $hasPermissions = true;
                break;
            }
        }

        if (!$hasPermissions) {
            $acl->insertObjectAce($securityId, $mask);
        }

        $this->aclProvider->updateAcl($acl);
	}
}