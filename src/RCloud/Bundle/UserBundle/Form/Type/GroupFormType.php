<?php

// src/RCloud/Bundle/UserBundle/Form/Type/GroupFormType.php

namespace RCloud\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('description')
        ->add('owner', 'entity', array(
            'class' => 'RCloudUserBundle:User',
            'property' => 'username',
            'disabled' => true
        ))
        ;
    }

    public function getParent()
    {
        return 'fos_user_group';
    }

    public function getName()
    {
        return 'rcloud_user_group';
    }
}
