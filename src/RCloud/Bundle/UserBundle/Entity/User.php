<?php
// src/RCloud/Bundle/UserBundle/Entity/User.php

namespace RCloud\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rcloud_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="RCloud\Bundle\RBundle\Entity\Script", mappedBy="owner")
     */
    protected $scripts;
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->scripts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add scripts
     *
     * @param \RCloud\Bundle\RBundle\Entity\Script $scripts
     * @return User
     */
    public function addScript(\RCloud\Bundle\RBundle\Entity\Script $scripts)
    {
        $this->scripts[] = $scripts;
        $scripts->setOwner($this);
    
        return $this;
    }

    /**
     * Remove scripts
     *
     * @param \RCloud\Bundle\RBundle\Entity\Script $scripts
     */
    public function removeScript(\RCloud\Bundle\RBundle\Entity\Script $scripts)
    {
        $this->scripts->removeElement($scripts);
    }

    /**
     * Get scripts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getScripts()
    {
        return $this->scripts;
    }
}
