<?php
// src/Controller/ArticleController.php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ArticleRepository;
use App\Repository\MessageRepository;
use App\Service\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    #[IsGranted('ROLE_USER')] 
    #[Route('/article/new', name: 'article_new')]
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->articleService->createArticle(
                    $article,
                    $form->get('illustrations')->getData(),
                    $form->get('videos')->getData(),
                    $this->getUser()
                );

                // Message de succès
                $this->addFlash('success', 'L\'article a bien été créé !');

                return $this->redirectToRoute('article_details', [
                    'id' => $article->getId(),
                    'slug' => $article->getSlug(),
                ]);
            } catch (\Exception $e) {
                // Message d'erreur
                $this->addFlash('danger', 'Une erreur est survenue lors de la création de l\'article.');
            }
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour éditer un article
    #[IsGranted('ROLE_USER')]
    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Request $request, Article $article, ArticleService $articleService, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('article_edit', $article);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $deleteIllustrations = $request->request->all('delete_illustrations') ?? [];
        $deleteVideos = $request->request->all('delete_videos') ?? [];

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $articleService->deleteIllustrations($deleteIllustrations, $article);
                $articleService->deleteVideos($deleteVideos, $article);

                $articleService->updateArticle(
                    $article,
                    $form->get('illustrations')->getData(),
                    $form->get('videos')->getData()
                );

                $entityManager->flush();

                // Message de succès
                $this->addFlash('success', 'L\'article a bien été modifié !');

                return $this->redirectToRoute('article_details', [
                    'id' => $article->getId(),
                    'slug' => $article->getSlug(),
                ]);
            } catch (\Exception $e) {
                // Message d'erreur
                $this->addFlash('danger', 'Une erreur est survenue lors de la modification de l\'article.');
            }
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    // Route pour supprimer un article
    #[IsGranted('ROLE_USER')] 
    #[Route('/article/{id}/delete', name: 'article_delete')]
    public function delete(Request $request, Article $article): Response
    {
        $this->denyAccessUnlessGranted('article_delete', $article);

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            try {
                $this->articleService->deleteArticle($article);

                // Message de succès
                $this->addFlash('success', 'L\'article a bien été supprimé.');

            } catch (\Exception $e) {
                // Message d'erreur
                $this->addFlash('danger', 'Une erreur est survenue lors de la suppression de l\'article.');
            }
        }

        return $this->redirectToRoute('app_home');
    }

    // Route pour afficher les détails d'un article
    #[Route('/article/{id}/{slug}', name: 'article_details', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]+'])]
    public function details(
        int $id,
        string $slug,
        ArticleRepository $articleRepository,
        MessageRepository $messageRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $article = $articleRepository->find($id);

        if (!$article || $article->getSlug() !== $slug) {
            throw $this->createNotFoundException('Article non trouvé.');
        }

        $page = max(1, $request->query->getInt('page', 1)); 
        $limit = 10;
        $paginationData = $messageRepository->getMessagesPaginated($article, $page, $limit);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $message->setArticle($article);
                $message->setUser($this->getUser());
                $message->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($message);
                $entityManager->flush();

                // Message de succès
                $this->addFlash('success', 'Votre commentaire a bien été ajouté !');

                return $this->redirectToRoute('article_details', [
                    'id' => $id,
                    'slug' => $slug,
                    'page' => 1, 
                ]);
            } catch (\Exception $e) {
                // Message d'erreur
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'ajout du commentaire.');
            }
        }

        return $this->render('article/article_details.html.twig', [
            'article' => $article,
            'messages' => $paginationData['messages'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $page,
            'form' => $form->createView(),
        ]);
    }
}
