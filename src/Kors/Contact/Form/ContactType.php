<?php

namespace Kors\Contact\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * Builds the contact form
 *
 */
class ContactType extends AbstractType
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label'    => 'Name',
            'required' => false,
        ));
        $builder->add('email', 'text', array(
            'label'    => 'Email',
            'required' => false,
        ));
        $builder->add('subject', 'text', array(
            'label'    => 'Subject',
            'required' => false,
        ));
        $builder->add('content', 'textarea', array(
            'label'    => 'Content',
            'required' => false,
        ));
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::getDefaultOptions()
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'csrf_protection' => true,
        );
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'contact_type';
    }
}
