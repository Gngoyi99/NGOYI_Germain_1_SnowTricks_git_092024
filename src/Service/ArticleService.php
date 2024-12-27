<?php
// src/Service/ArticleService.php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Illustration;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleService
{
    private $entityManager;
    private $slugger;
    private $uploadDirectory;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger, string $uploadDirectory)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->uploadDirectory = $uploadDirectory;
    }

    // Créer un nouvel article
    public function createArticle(Article $article, $illustrations, $videos, $user)
    {
        // Vérification de l'unicité du nom de l'article
        $existingArticle = $this->entityManager->getRepository(Article::class)->findOneBy(['name' => $article->getName()]);
        if ($existingArticle) {
            throw new \Exception('Un article avec ce nom existe déjà.');
        }

        // Assigner l'utilisateur à l'article
        $article->setUserId($user);

        // Traitement des illustrations
        foreach ($illustrations as $file) {
            $this->handleFileUpload($file, $article);
        }

        // Traitement des vidéos
        foreach ($videos as $embedCode) {
            $this->handleVideo($embedCode, $article);
        }

        // Persister l'article
        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }

    // Mettre à jour un article existant
    public function updateArticle(Article $article, $illustrations, $videos)
    {
        // Traitement des illustrations
        foreach ($illustrations as $file) {
            $this->handleFileUpload($file, $article);
        }

        // Traitement des vidéos
        foreach ($videos as $embedCode) {
            $this->handleVideo($embedCode, $article);
        }

        // Enregistrer les changements
        $this->entityManager->flush();
    }

    // Supprimer un article
    public function deleteArticle(Article $article)
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }

    // Gérer l'upload des fichiers
    private function handleFileUpload($file, Article $article)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->uploadDirectory, $newFilename);
        } catch (FileException $e) {
            throw new \Exception('Une erreur est survenue lors du téléchargement de l\'illustration.');
        }

        $illustration = new Illustration();
        $illustration->setUrl($newFilename);
        $article->addIllustration($illustration);
    }

    // Gérer l'ajout des vidéos
    private function handleVideo($embedCode, Article $article)
    {
        $video = new Video();
        $video->setEmbedCode($embedCode);
        $article->addVideo($video);
    }
}
