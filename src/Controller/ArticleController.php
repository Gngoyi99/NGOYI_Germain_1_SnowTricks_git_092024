<?php
// src/Controller/ArticleController.php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ArticleController extends AbstractController
{
    private $articleService;
    private $security;

    public function __construct(ArticleService $articleService, Security $security)
    {
        $this->articleService = $articleService;
        $this->security = $security;
    }

    // Route pour créer un nouvel article
    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $this->security->getUser();
                if (!$user) {
                    throw $this->createAccessDeniedException('Vous devez être connecté pour créer un article.');
                }

                $illustrations = $form->get('illustrations')->getData();
                $videos = $form->get('videos')->getData();

                $this->articleService->createArticle($article, $illustrations, $videos, $user);

                return $this->redirectToRoute('app_home');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('article_new');
            }
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour éditer un article
    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $illustrations = $form->get('illustrations')->getData();
                $videos = $form->get('videos')->getData();

                $this->articleService->updateArticle($article, $illustrations, $videos);

                return $this->redirectToRoute('app_home');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('article_edit', ['id' => $article->getId()]);
            }
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    // Route pour supprimer un article
    #[Route('/article/{id}/delete', name: 'article_delete')]
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $this->articleService->deleteArticle($article);

            $this->addFlash('success', 'Article supprimé avec succès.');
        }

        return $this->redirectToRoute('app_home');
    }
}
