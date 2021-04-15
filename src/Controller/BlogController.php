<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Form\CommentType;
use App\Entity\Article;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Comment;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        $article = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $article
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    { 
        return $this->render('blog/home.html.twig'); 

    }
      /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager) : Response
    {
        if(!$article){ 
            $article = new Article(); 
        }

        $form = $this->createFormBuilder($article)
                    ->add('title') 
                    ->add('content') 
                    ->add('image') 
                    ->add('category', EntityType::class, [
                        'class' => Category::class,
                        'choice_label' => 'title'
                    ])
                    ->getForm(); 

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){ 
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article); 
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id'=> $article->getId()] );
        }

        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() != null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager) : Response
    {   

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig',[
            'article' => $article, 
            'commentForm' => $form->createView()
        ]);
    }
  
}
