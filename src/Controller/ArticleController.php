<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Article;

class ArticleController extends AbstractController
{
    public function index(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->findBy(['id' => $id]);
        return $this->render('article/index.html.twig', [
            'article' => $article,
        ]);
    }

    public function edit(Request $request, int $id=null): Response
    {
        $em = $this->getDoctrine()->getManager();

        if($id) {
            $mode = 'update';
            $article = $em->getRepository(Article::class)->findBy(['id' => $id]);
        }
        else {
            $mode = 'new';
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->saveArticle($article, $mode);

            return $this->redirectToRoute('article_edit', array('id' => $article->getId()));
        }

        $parameters = array(
            'form' => $form,
            'article' => $article,
            'mode' => $mode
        );

        return $this->render('article/edit.html.twig', $parameters);
    }

    public function remove(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->findBy(['id' => $id])[0];

        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }

    private function completeArticleBeforeSave(Article $article, string $mode) {
        if($article->getPublished()) {
            $article->setPublishedAt(new \DateTime());
        }
        $article->setAuthor($this->getUser());

        return $article;
    }

    private function saveArticle(Article $article, string $mode) {
        $article = $this->completeArticleBeforeSave($article, $mode);

        $em = $this->getDoctrine()->getManager();
        $em = persist($article);
        $em->flush();
    }
}
