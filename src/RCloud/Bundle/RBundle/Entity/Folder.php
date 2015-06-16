<?php

namespace RCloud\Bundle\RBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Folder
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="RCloud\Bundle\RBundle\Repository\FolderRepository")
 */
class Folder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    private $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime")
     */
    private $dateModification;

    /**
     * @ORM\ManyToOne(targetEntity="RCloud\Bundle\UserBundle\Entity\User", inversedBy="folders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="RCloud\Bundle\RBundle\Entity\Folder")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="RCloud\Bundle\RBundle\Entity\Script", mappedBy="folder")
     */
    private $scripts;



    public function __construct()
    {
        $this->setDateAdd(new \DateTime);
        $this->setDateModification(new \DateTime);
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
     * Set name
     *
     * @param string $name
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     * @return Folder
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return Folder
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set owner
     *
     * @param \RCloud\Bundle\UserBundle\Entity\User $owner
     * @return Folder
     */
    public function setOwner(\RCloud\Bundle\UserBundle\Entity\User $owner)
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
     * Set parent
     *
     * @param \RCloud\Bundle\RBundle\Entity\Folder $parent
     *
     * @return Folder
     */
    public function setParent(\RCloud\Bundle\RBundle\Entity\Folder $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \RCloud\Bundle\RBundle\Entity\Folder
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add script
     *
     * @param \RCloud\Bundle\RBundle\Entity\Script $script
     *
     * @return Folder
     */
    public function addScript(\RCloud\Bundle\RBundle\Entity\Script $script)
    {
        $this->scripts[] = $script;
        $script->setFolder($this);

        return $this;
    }

    /**
     * Remove script
     *
     * @param \RCloud\Bundle\RBundle\Entity\Script $script
     */
    public function removeScript(\RCloud\Bundle\RBundle\Entity\Script $script)
    {
        $this->scripts->removeElement($script);
        $script->setFolder(null);
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
