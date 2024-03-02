<?php

namespace App\Form;

use App\Entity\Child;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nome', 'required' => true])
            ->add(
                'birthDate',
                BirthdayType::class,
                [
                    'label' => 'Data di nascita',
                    'required' => true,
                    'input' => 'datetime_immutable'
                ])
            ->add('submit', SubmitType::class, ['label' => 'Aggiungi']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Child::class]);
    }
}
