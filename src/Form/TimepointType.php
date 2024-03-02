<?php

namespace App\Form;

use App\Entity\BodyTemperature;
use App\Entity\Height;
use App\Entity\Weight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimepointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['data_class']) {
            case Height::class:
                $builder->add('value', IntegerType::class, ['label' => 'Altezza (cm)']);
                break;
            case Weight::class:
                $builder->add('value', NumberType::class, ['label' => 'Peso (kg)']);
                break;
            case BodyTemperature::class:
                $builder->add('value', NumberType::class, ['label' => 'Temperatura (°C)']);
                break;
            default:
                throw new \InvalidArgumentException('Timepoint non supportato');
        }

        $builder
            ->add(
                'child',
                ChoiceType::class,
                [
                    'choices' => $options['children'],
                    'label' => 'Bimbo/a',
                    'choice_value' => 'id',
                    'choice_label' => 'name'
                ]
            )
            ->add('date', DateType::class, ['label' => 'Data'])
            ->add('notes', TextareaType::class, ['label' => 'Note'])
            ->add('sumbit', SubmitType::class, ['label' => 'Salva']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->addAllowedTypes('children', 'array');
    }

}
