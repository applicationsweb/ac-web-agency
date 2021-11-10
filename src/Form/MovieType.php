<?php

namespace App\Form;

use App\Entity\Kind;
use App\Entity\Movie;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\KindsInputType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MovieType extends AbstractType
{
    private $slugger;

    // Form types are services, so you can inject other services in them if needed
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => ['autofocus' => true],
                'label' => 'Nom du film',
            ])
            ->add('image', null, [
                'label' => 'Url de l\'image',
                'help' => 'https://via.placeholder.com/1024x500',
            ])
            ->add('kinds', EntityType::class, [
                'class' => Kind::class,
                'choice_label' => 'name',
                'label' => 'Genre(s)',
                'multiple' => true,
                'expanded' => true
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Movie */
                $movie = $event->getData();
                if (null !== $movieName = $movie->getName()) {
                    $movie->setSlug($this->slugger->slug($movieName)->lower());
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
