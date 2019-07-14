<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ChatConversation;
use App\Entity\ChatMessage;
use App\Repository\ChatMessageRepository;
use App\Form\ChatType;
use App\Form\ChatMessageType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChatController extends AbstractController
{
    /**
     * @Route("/messages", name="messages")
     */
    public function chat(Request $request, ObjectManager $manager)
    {
        $conv = new ChatConversation();
        $form = $this->createForm(ChatType::class, $conv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $conv->addParticipant($this->getUser());

            $participants = $form->get('conv')->getData();
            foreach ($participants as $p)
                $conv->addParticipant($p);

            $manager->persist($conv);
            $manager->flush();
            return $this->redirectToRoute('conversation_view', [
                'id' => $conv->getId()
            ]);
        }

        return $this->render('chat/index.html.twig', [
            'formChat' => $form->createView()
        ]);
    }

    /**
     * @Route("/conversations/{id}", name="conversation_view")
     */
    public function conversation(ChatConversation $chatConversation,
                                 ChatMessageRepository $chatMessageRepository)
    {
        $formMsg = $this->createForm(ChatMessageType::class);
        return $this->render('chat/conversation.html.twig', [
            'formMessages' => $formMsg->createView(),
            'conversation' => $chatConversation,
            'messages' => array_reverse($chatMessageRepository->getLastMessages($chatConversation))
        ]);
    }

    /**
     * @Route("/conversations/{id}/submit", name="conversation_submit")
     */
    public function submit(ChatConversation $chatConversation,
                           Request $request, ObjectManager $manager)
    {
        $message = new ChatMessage();
        $formMsg = $this->createForm(ChatMessageType::class, $message);
        $formMsg->handleRequest($request);

        if ($formMsg->isSubmitted() && $formMsg->isValid())
        {
            $message->setConversation($chatConversation);
            $message->setSender($this->getUser());
            $message->setContent($formMsg->get('content')->getData());
            $message->setSubmitTime(new \DateTime());
            $manager->persist($message);
            $manager->flush();
            return new JsonResponse([
                'success' => true,
                "htmlTemplate" => $this->renderView('chat/_message.html.twig', [
                    'm' => $message
                ])
            ]);
        }

        return new JsonResponse(['success' => false]);
    }

    /**
     * @Route("/conversations/{id}/refresh", name="conversation_refresh")
     */
    public function refresh(ChatConversation $chatConversation,
                            ChatMessageRepository $chatMessageRepository)
    {
        $user = $this->getUser();
        if ($user === null)
            throw $this->createNotFoundException();

        $messages = $chatMessageRepository->getLastMessagesFromOther($chatConversation, $user);

        if (count($messages) <= 0)
            return new JsonResponse(['success' => false]);

        $repoResponse = array_reverse($messages);
        $templates = [];

        foreach ($repoResponse as $value)
            $templates[] = $this->renderView('chat/_message.html.twig', [
                'm' => $value
            ]);

        return new JsonResponse([
            'success' => true,
            'htmlTemplate' => $templates
        ]);
    }
}