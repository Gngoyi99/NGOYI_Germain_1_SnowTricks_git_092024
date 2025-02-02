<?php
// src/Form/ArticleType.php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            // Validation des articles
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'article',
                'attr' => ['placeholder' => 'Ex: Snowboard Tricks'],
                'empty_data' => '', // Empêche null d’être envoyé

            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez votre article en quelques mots...'],
                'empty_data' => '', // Empêche null d’être envoyé

            ])

            ->add('category', TextType::class, [
                'label' => 'Catégorie',
                'attr' => ['placeholder' => 'Ex: Freestyle, Slalom, etc.'],
                'empty_data' => '', // Empêche null d’être envoyé

            ])

            // Validation pour les images
            ->add('illustrations', CollectionType::class, [
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => 'Télécharger une image',
                    'constraints' => [
                        new File([
                            'maxSize' => '2M',
                            'mimeTypes' => ['image/jpeg', 'image/png'],
                            'mimeTypesMessage' => 'Seuls les fichiers JPG et PNG sont autorisés.',
                        ])
                    ],
                ],
                'allow_add' => true,
                'by_reference' => false,
                'required' => false,
                'mapped' => false,
                'prototype' => true,
                'attr' => [
                    'class' => 'illustration-collection',
                ],
            ])

            // Validation pour les vidéos
            ->add('videos', CollectionType::class, [
                'entry_type' => UrlType::class,
                'entry_options' => [
                    'label' => 'URL de la vidéo',
                    'constraints' => [
                        new NotBlank(['message' => 'L\'URL de la vidéo est obligatoire.']),
                        new Regex([
                            'pattern' => '/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be|vimeo\.com)\/.+$/',
                            'message' => 'Veuillez entrer une URL YouTube ou Vimeo valide.'
                        ])
                    ],
                ],
                'allow_add' => true,
                'by_reference' => false,
                'required' => false,
                'mapped' => false,
                'prototype' => true,
                'attr' => [
                    'class' => 'video-collection',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
