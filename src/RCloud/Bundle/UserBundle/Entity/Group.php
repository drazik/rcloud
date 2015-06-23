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
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="RCloud\Bundle\UserBundle\Entity\User", mappedBy="groups")
     */
    protected $users;

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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add user
     *
     * @param \RCloud\Bundle\UserBundle\Entity\User $user
     *
     * @return Group
     */
    public function addUser(\RCloud\Bundle\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;
        $user->addGroup($this);

        return $this;
    }

    /**
     * Remove user
     *
     * @param \RCloud\Bundle\UserBundle\Entity\User $user
     */
    public function removeUser(\RCloud\Bundle\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
        $user->removeGroup($this);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
