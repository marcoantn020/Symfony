<?php

namespace App\Form;

use App\DTO\SeriesCreateFormInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeriesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'seriesName' ,options: ['label' => 'Nome da serie'])
            ->add(child: 'seasonsQuantity',type: NumberType::class, options: ['label' => 'Quantidade de temporadas'])
            ->add(child: 'episodePerSeason',type: NumberType::class, options: ['label' => 'Episodios por temporada'])
            ->add('save', SubmitType::class, ['label' => 'Salvar'])
            ->setMethod($options['is_edit'] ? 'PUT' : 'POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeriesCreateFormInput::class,
            'is_edit' => false
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
