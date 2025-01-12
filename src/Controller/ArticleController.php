<?php
// src/Controller/ArticleController.php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
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
    private $articleRepository;


    public function __construct(ArticleService $articleService, Security $security, ArticleRepository $articleRepository)
    {
        $this->articleService = $articleService;
        $this->security = $security;
        $this->articleRepository = $articleRepository;

    }

    // Route pour créer un nouvel article
    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request): Response
    {
        // Vérifie si l'utilisateur est authentifié
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer un article.');
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->createArticle(
                $article,
                $form->get('illustrations')->getData(),
                $form->get('videos')->getData(),
                $this->getUser()
            );

            return $this->redirectToRoute('article_details', [
                'id' => $article->getId(),
                'slug' => $article->getSlug(),
            ]);
            
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    // Route pour éditer un article
    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Request $request, Article $article): Response
    {
        // Vérifie si l'utilisateur a le droit de modifier l'article
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $article->getUserId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les droits pour modifier cet article.');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->updateArticle($article, $form->get('illustrations')->getData(), $form->get('videos')->getData());

            return $this->redirectToRoute('article_details', ['id' => $article->getId()]);
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
        // Vérifie si l'utilisateur est autorisé à supprimer l'article
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $article->getUserId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les droits pour supprimer cet article.');
        }

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $this->articleService->deleteArticle($article);
            $this->addFlash('success', 'Article supprimé avec succès.');
        }

        return $this->redirectToRoute('app_home');
    }


    #[Route('/article/{id}/{slug}', name: 'article_details', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]+'])]
    public function details(int $id, string $slug): Response
    {
        // Récupération de l'article via l'id
        $article = $this->articleRepository->find($id);

        if (!$article || $article->getSlug() !== $slug) {
            throw $this->createNotFoundException('Article non trouvé.');
        }

        return $this->render('article/article_details.html.twig', [
            'article' => $article,
        ]);
    }


}
