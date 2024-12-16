<?php
// src/Controller/ArticleController.php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Illustration;
use App\Entity\Video;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    #[Route('/article/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'unicité du nom de l'article
            $existingArticle = $entityManager->getRepository(Article::class)->findOneBy(['name' => $article->getName()]);
            if ($existingArticle) {
                $this->addFlash('error', 'Un article avec ce nom existe déjà.');
                return $this->redirectToRoute('article_new');
            }

            // Récupérer l'utilisateur courant et l'associer à l'article
            $user = $this->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour créer un article.');
            }
            $article->setUserId($user);

            // Traitement des illustrations (fichiers téléchargés)
            $illustrations = $form->get('illustrations')->getData();
            foreach ($illustrations as $file) {
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                    try {
                        $file->move($this->getParameter('illustrations_directory'), $newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'illustration.');
                        return $this->redirectToRoute('article_new');
                    }

                    $illustration = new Illustration();
                    $illustration->setUrl($newFilename);
                    $article->addIllustration($illustration);
                }
            }

            // Traitement des vidéos (embed codes)
            $videos = $form->get('videos')->getData();
            foreach ($videos as $embedCode) {
                if ($embedCode) {
                    $video = new Video();
                    $video->setEmbedCode($embedCode);
                    $article->addVideo($video);
                }
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_success');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'unicité du nom de l'article
            $existingArticle = $entityManager->getRepository(Article::class)->findOneBy(['name' => $article->getName()]);
            if ($existingArticle && $existingArticle->getId() !== $article->getId()) {
                $this->addFlash('error', 'Un article avec ce nom existe déjà.');
                return $this->redirectToRoute('article_edit', ['id' => $article->getId()]);
            }

            // Traitement des illustrations (fichiers téléchargés)
            $illustrations = $form->get('illustrations')->getData();
            foreach ($illustrations as $file) {
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                    try {
                        $file->move($this->getParameter('illustrations_directory'), $newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'illustration.');
                        return $this->redirectToRoute('article_edit', ['id' => $article->getId()]);
                    }

                    $illustration = new Illustration();
                    $illustration->setUrl($newFilename);
                    $article->addIllustration($illustration);
                }
            }

            // Traitement des vidéos (embed codes)
            $videos = $form->get('videos')->getData();
            foreach ($videos as $embedCode) {
                if ($embedCode) {
                    $video = new Video();
                    $video->setEmbedCode($embedCode);
                    $article->addVideo($video);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }


    #[Route('/article/{id}/delete', name: 'article_delete')]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé avec succès.');
        }

        return $this->redirectToRoute('app_home');
    }

    // #[Route('/article/success', name: 'article_success')]
    // public function success(): Response
    // {
    //     return $this->render('article/success.html.twig');
    // }
}
