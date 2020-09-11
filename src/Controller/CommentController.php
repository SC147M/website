<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

    /**
     * @Route("/news/{id}/createcomment", name="comment_create")
     *
     * @param News    $news
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(News $news, Request $request)
    {
        $user = $this->getUser();
        if (!$user->getId()) {
            throw new AccessDeniedHttpException();
        }

        $comment = new Comment();
        $comment->setNews($news);
        $comment->setUser($this->getUser());

        $form = $this->createForm(CommentType::class, null, [
            'action' => $this->generateUrl('comment_create', [
                'id' => $news->getId(),
            ]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data->getMessage();
            $comment->setMessage($message);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('news_detail', [
            'id'   => $news->getId(),
            'slug' => $news->getSlug(),
        ]);
    }
}
