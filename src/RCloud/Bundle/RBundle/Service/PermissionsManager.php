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

    public function inheritPermissions($object, $parent) {
        $acl = $this->aclProvider->findAcl(ObjectIdentity::fromDomainObject($parent));
        
        foreach($acl->getObjectAces() as $ace) {
            $this->changePermissions($object, $ace->getSecurityIdentity(), $ace->getMask());
        }

    }

    public function setOwnerPermissions($object, $user) {
        // création de l'ACL
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        // retrouve l'identifiant de sécurité de l'utilisateur actuellement connecté
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // donne accès au propriétaire
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $this->aclProvider->updateAcl($acl);
    }
}