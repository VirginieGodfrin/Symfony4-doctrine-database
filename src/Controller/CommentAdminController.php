<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;

class CommentAdminController extends Controller
{
    /**
     * @Route("/admin/comment", name="comment_admin")
     */
    public function index(CommentRepository $repository, Request $request)
    {
    	$comments = $repository->findBy([], ['createdAt' => 'DESC']);
    	// Basically, any time you want to use $_GET, $_POST, $_SERVER 
    	// or any of those global variables, use the Request instead.
    	// (we don't use form & form builder))
    	$q = $request->query->get('q');
    	if ($q) {
    		$comments = $repository->findAllWithSearch($q);
    	}
    	
        return $this->render('comment_admin/index.html.twig', [
            'comments' => $comments,
        ]);
    }
}
