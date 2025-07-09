<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UpdateFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attribute', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Wähle ein Attribut',
                'choices' => [
                    'Filmgenre' => 'Genre',
                    'Länge des Films' => 'Runtime',
                    'kurze Zusammenfassung' => 'Plot',
                    'Schauspieler' => 'Actors',
                    'ID von IMDB' => 'imdbID',
                    'Regie des Films' => 'Director'
                ],
            ])
            ->add('limit', NumberType::class, [
                'required' => false,
            ])
            ->add('offset', NumberType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Jetzt aktualisieren',
            ])
        ;
    }
}
