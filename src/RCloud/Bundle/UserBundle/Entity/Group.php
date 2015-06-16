<?php
// src/RCloud/Bundle/UserBundle/Entity/Group.php

namespace RCloud\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rcloud_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RCloud\Bundle\UserBundle\Entity\User")
     */
    protected $owner;

    /**
     * Set owner
     *
     * @param \RCloud\Bundle\UserBundle\Entity\User $owner
     *
     * @return Group
     */
    public function setOwner(\RCloud\Bundle\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \RCloud\Bundle\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
