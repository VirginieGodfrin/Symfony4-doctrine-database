<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;


class ArticleAdminController extends AbstractController
{
	/**
     * @Route("/admin/article/new")
     */
    // get the entity manager
	public function new(EntityManagerInterface $em)
	{
		die('to do');
		return new Response(sprintf(
            'Hiya! New Article id: #%d slug: %s',
            $article->getId(),
            $article->getSlug()
        ));
	}
}