<?php
// src/RCloud/Bundle/UserBundle/Entity/User.php

namespace RCloud\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
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
     * @ORM\OneToMany(targetEntity="RCloud\Bundle\RBundle\Entity\Folder", mappedBy="owner")
     */
    protected $folders;

    /**
     * @ORM\ManyToMany(targetEntity="RCloud\Bundle\UserBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="rcloud_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;


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

    /**
     * Add folder
     *
     * @param \RCloud\Bundle\RBundle\Entity\Folder $folder
     *
     * @return User
     */
    public function addFolder(\RCloud\Bundle\RBundle\Entity\Folder $folder)
    {
        $this->folders[] = $folder;

        return $this;
    }

    /**
     * Remove folder
     *
     * @param \RCloud\Bundle\RBundle\Entity\Folder $folder
     */
    public function removeFolder(\RCloud\Bundle\RBundle\Entity\Folder $folder)
    {
        $this->folders->removeElement($folder);
    }

    /**
     * Get folders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFolders()
    {
        return $this->folders;
    }
}
