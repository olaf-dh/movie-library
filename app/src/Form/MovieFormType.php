<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Repository\GenreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    private GenreRepository $genreRepository;

    /**
     * @param GenreRepository $genreRepository
     */
    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('plot', TextareaType::class, [
                'help' => 'Choose something catchy!',
                'required' => false,
            ])
            ->add('year')
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'label' => 'Genres',
                'multiple' => true,
                'expanded' => true,
                'choices' => $this->genreRepository
                    ->findAllOrderedByName(),
                'required' => true,
            ])
            ->add('length', null, [
                'required' => false,
                'data' => 0,
            ])
            ->add('file_name', null, [
                'required' => true,
            ])
            ->add('file_type', null, [
                'required' => true,
            ])
            ->add('file_size', null, [
                'required' => true,
            ])
            ->add('directory', null, [
                'required' => false,
            ])
            ->add('subtitle_file', null, [
                'required' => false,
            ])
            ->add('image_file', null, [
                'required' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
