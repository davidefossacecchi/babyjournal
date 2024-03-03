<?php

namespace App\Form;

use App\Entity\Timepoints\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('caption', TextareaType::class,  ['label' => 'Didascalia'])
            ->add('image', FileType::class, [
                'label' => 'Immagine',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/*'],
                        'mimeTypesMessage' => 'Carica un\'immagine'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Condividi']);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_type' => Post::class]);
    }


}
