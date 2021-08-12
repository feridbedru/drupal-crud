<?php

namespace App\Form;

use App\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'String' => '1',
                    'Integer' => '2',
                    'URL' => '3',
                    'Text' => '4',
                    'Longtext' => '5',
                    'Phone' => '6',
                    'Email' => '7',
                    'Decimal' => '8',
                    'File' => '9',
                    'Date' => '10',
                    'Boolean' => '11',
                    'Relation' => '12',
                ],
                'placeholder' => "Choose Type",
                'required' => true,
            ])
            ->add('description')
            ->add('length')
            ->add('default_value')
            ->add('validation')
            ->add('is_nullable')
            ->add('is_on_form')
            ->add('is_on_index')
            ->add('is_on_show')
            ->add('relation_entity')
            ->add('relation_field')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
        ]);
    }
}
