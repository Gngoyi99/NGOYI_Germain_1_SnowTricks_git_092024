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

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('category', TextType::class)

            // Illustrations (images)
            ->add('illustrations', CollectionType::class, [
                'entry_type' => FileType::class,
                'entry_options' => ['label' => 'Télécharger une image'],
                'allow_add' => true,
                'by_reference' => false,
                'required' => false,
                'mapped' => false, // Pas de lien direct avec Article
                'prototype' => true,
                'attr' => [
                    'class' => 'illustration-collection',
                ],
            ])

            // Vidéos (embed codes)
            ->add('videos', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['label' => 'Embed Code'],
                'allow_add' => true,
                'by_reference' => false,
                'required' => false,
                'mapped' => false, // Pas de lien direct avec Article
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
