<?php

namespace App\Form;

use App\Entity\WordDensity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WordDensityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('notes', TextType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('limit', IntegerType::class, [
                'label' => false,
                'required' => true,
                'data' => 20,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => WordDensity::class,
            ]);
    }
}
