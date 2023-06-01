<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Article;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Article::class)->findAll;
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
