<?php

namespace VideoBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VideoBundle\Entity\Category;

class FilmType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('titre', TextType::class, array(
                    'label' => "Titre du film *",
                    'required' => true
                ))
                ->add('description', TextareaType::class, array(
                    'label' => "Description du film *",
                    'required' => true
                ))
                ->add('photo', FileType::class, array(
                    'label' => "Photo d'affichage",
                    'required' => false
                ))
                ->add('categorie', EntityType::class, array(
                    'class' => Category::class,
                    'label' => "Categorie du film *",
                    'choice_label' => 'nom',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VideoBundle\Entity\Film'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'videobundle_film';
    }

}
