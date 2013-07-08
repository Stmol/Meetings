<?php
/**
 * Created by Stmol.
 * Date: 30.06.13
 */

namespace Stmol\HuddleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class NewMeetingType
 * @package Stmol\HuddleBundle\Form
 * @author Yury [Stmol] Smidovich
 */
class NewMeetingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meeting', 'meeting')
            ->add('member', 'member')
            ->add('Submit', 'submit')
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'new_meeting';
    }
}