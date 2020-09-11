<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * @Route("/media/{id}", name="media")
     * @param Media $media
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function detail(Media $media)
    {
        $filenname = $this->renderView('media/url.txt.twig', ['media' => $media]);
        return $this->json($filenname);
    }
}
