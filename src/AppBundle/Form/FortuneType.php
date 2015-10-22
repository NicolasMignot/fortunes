<?php
/**
 * Created by PhpStorm.
 * User: Kelmas2a
 * Date: 22/10/15
 * Time: 10:05
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FortuneType extends AbstractType
{
    public function getName()
    {
        return "Fortune";
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title")
            ->add("author")
            ->add("content")
            ->add("submit", "submit");
    }
}
