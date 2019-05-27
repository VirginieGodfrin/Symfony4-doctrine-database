<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
use App\Service\SlackClient;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentRepository;

class ArticleController extends AbstractController
{
    /**
     * Currently unused: just showing a controller with a constructor!
     */
    private $isDebug;

    public function __construct(bool $isDebug)
    {
        $this->isDebug = $isDebug;
    }

    // Remember service's config: classes in src/ available to be used as services
    // Use ArticleRepository like one of them

    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(ArticleRepository $repository)
    {
        // $repository = $em->getRepository(Article::class);
        // use a custom query method
        $articles = $repository->findAllPublishedOrderedByNewest();
        dump($articles);
        return $this->render('article/homepage.html.twig',[
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/news/{slug}", name="article_show")
     */
    public function show(Article $article, SlackClient $slack)
    {
        if ($article->getSlug() === 'khaaaaaan') {
            $slack->sendMessage('Kahn', 'Ah, Kirk, my old friend...');
        }

        // $comments = $commentRepository->findBy(['article' => $article]);
        // When we call getComments(), Doctrine makes a query in the background to go get the comment data.
        // This is called "lazy loading": related data is not queried for until, and unless, we use it. 
        // To make this magic possible, Doctrine uses this PersistentCollection object. 
        // This is not something you need to think or worry about: this object looks and acts like an array.

        // $comments = $article->getComments();
        // dump($comments);

        // foreach ($comments as $comment) {
        //     dump($comment);
        // }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/news/{slug}/heart", name="article_toggle_heart", methods={"POST"})
     */
    public function toggleArticleHeart(Article $article, LoggerInterface $logger, EntityManagerInterface $em)
    {   
        // don't do it in real life it'an exemple 
        // situation de compétition -> race condition
        // move this kind of code out of controller in a service or
        // if logic is simple put it in entity
        // $article->setHeartCount($article->getHeartCount() + 1);
        $article->incrementHeartCount();
        // No need persist() for updates.
        // When you query Doctrine for an object, 
        // it already knows that you want that object to be saved to the database when you call flush(). 
        // Doctrine is also smart enough to know that it should update the object, instead of inserting a new one.
        $em->flush();

        $logger->info('Article is being hearted!');

        return new JsonResponse(['hearts' => $article->getHeartCount()]);
    }
}
