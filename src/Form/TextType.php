<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05-11-18
 * Time: 14:15
 */

namespace App\Form;


use App\Entity\Text;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key')
            ->add('french');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Text::class,
        ]);
    }
}
