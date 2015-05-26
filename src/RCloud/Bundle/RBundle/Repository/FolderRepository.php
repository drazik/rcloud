<?php
namespace RCloud\Bundle\RBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FolderRepository extends EntityRepository {

	public function getFolders($owner, $idParent = null) {
        $query = $this->createQueryBuilder('a');

        $query = $query->where('a.owner = :owner') 
                       ->setParameter('owner', $owner);
                       

        if ($idParent === null ) {
			$query = $query->andWhere('a.parentFolderId is NULL');
                  		  
        }
        else {
        	$query = $query->andWhere('a.parentFolderId = :idParent')
                  		  ->setParameter('idParent', $idParent);
        }

         return $query->getQuery()->getResult();

    }
}