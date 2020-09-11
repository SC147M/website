<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\CommentType;
use App\Repository\NewsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class NewsController extends AbstractController
{
    /**
     * @Route("/aktuelles/{page}", name="news")
     * @param int                $page
     * @param NewsRepository     $newsRepository
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($page = 1, NewsRepository $newsRepository, PaginatorInterface $paginator)
    {
        $queryBuilder = $newsRepository->findLatest();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $page, 10);

        return $this->render('news/index.html.twig', ['news' => $pagination]);
    }

    /**
     * @Route("/news/{id}/{slug}", name="news_detail")
     * @param News           $news
     * @param NewsRepository $newsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function detail(News $news, NewsRepository $newsRepository)
    {
        $related = $newsRepository->findByTags($news);

        $news->setViews($news->getViews() + 1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($news);
        $entityManager->flush();

        $form = $this->createForm(CommentType::class, null, [
            'action' => $this->generateUrl('comment_create', ['id' => $news->getId()]),
        ]);

        return $this->render('news/detail.html.twig', [
            'news'    => $news,
            'related' => $related,
            'form'    => $form->createView(),
        ]);
    }
}
