<?php
namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Service\MessageService;
use App\Repository\MessageRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractController
{   
    // Route pour créer un nouveau message
    #[IsGranted('ROLE_USER')] 
    #[Route('/article/{id}/{slug}/comment', name: 'addComment', methods: ['POST'])]
    public function addComment(int $id, string $slug, ArticleRepository $articleRepository, Request $request, MessageService $messageService): Response {
        $article = $articleRepository->findOneBy(['id' => $id, 'slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé.');
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setArticle($article);
            $message->setUser($this->getUser());

            // Enregistrement du message et message de succès
            $messageService->saveMessage($message);
            $this->addFlash('success', 'Votre commentaire a bien été ajouté.');

            return $this->redirectToRoute('article_details', ['id' => $id, 'slug' => $slug]);
        }
        
        // Message d'erreur
        $this->addFlash('danger', 'Une erreur est survenue lors de l\'ajout du commentaire.');
        return $this->redirectToRoute('article_details', ['id' => $id, 'slug' => $slug]);
    }

    // Route pour modifier un message
    #[IsGranted('ROLE_USER')] 
    #[Route('/message/{id}/edit', name: 'editMessage', methods: ['GET', 'POST'])]
    public function editMessage(int $id, Request $request, MessageRepository $messageRepository, MessageService $messageService): Response {
        $message = $messageRepository->find($id);

        if (!$message) {
            throw $this->createNotFoundException('Message non trouvé.');
        }

        $this->denyAccessUnlessGranted('message_edit', $message);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Enregistrement des mofications et message de succès
            $messageService->saveMessage($message);
            $this->addFlash('success', 'Votre commentaire a bien été modifié.');

            return $this->redirectToRoute('article_details', [
                'id' => $message->getArticle()->getId(),
                'slug' => $message->getArticle()->getSlug()
            ]);
        }

        // Message d'erreur
        $this->addFlash('danger', 'Une erreur est survenue lors de la modification du commentaire.');
        return $this->render('message/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
            'article' => $message->getArticle(),
        ]);
    }

    // Route pour supprimer un message
    #[IsGranted('ROLE_USER')] 
    #[Route('/message/{id}/delete', name: 'deleteMessage', methods: ['POST'])]
    public function deleteMessage(int $id, Request $request, MessageRepository $messageRepository, MessageService $messageService): Response {
        $message = $messageRepository->find($id);

        if (!$message) {
            throw $this->createNotFoundException('Message non trouvé.');
        }

        $this->denyAccessUnlessGranted('message_edit', $message);

        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            // Suppression du message et message de succès
            $messageService->deleteMessage($message);
            $this->addFlash('success', 'Votre commentaire a bien été supprimé.');
        } else {
            // Message d'erreur
            $this->addFlash('danger', 'Échec de la suppression du commentaire.');
        }

        return $this->redirectToRoute('article_details', [
            'id' => $message->getArticle()->getId(),
            'slug' => $message->getArticle()->getSlug()
        ]);
    }
}
