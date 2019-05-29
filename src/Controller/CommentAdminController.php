<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\CommentRepository;

class CommentAdminController extends Controller
{
    /**
     * @Route("/admin/comment", name="comment_admin")
     */
    public function index(CommentRepository $repository)
    {
    	$comments = $repository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('comment_admin/index.html.twig', [
            'comments' => $comments,
        ]);
    }
}
