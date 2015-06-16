<?php

namespace RCloud\Bundle\RBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FolderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text')
            ->add('parent', 'entity', array(
                  'class'    => 'RCloudRBundle:Folder',
                  'property' => 'name',
                  'multiple' => false,
                  'required' => false,
                  'placeholder' => 'Aucun',
                  'empty_data' => null))
            ->add('save', 'submit')
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rcloud\Bundle\RBundle\Entity\Folder'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rcloud_rbundle_folder';
    }
}
