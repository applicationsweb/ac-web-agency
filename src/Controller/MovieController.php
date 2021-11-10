<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Movie;
use App\Form\CommentType;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage movie contents in the public part of the site.
 *
 * @Route("/movie")
 *
 */
class MovieController extends AbstractController
{
    /**
     * Liste des films
     * 
     * @Route("/", methods="GET", name="movie_index")
     *
     */
    public function index(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findBy([], ['created' => 'DESC']);

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * Détails du film
     * 
     * @Route("/{slug}", methods="GET|POST", name="movie_show")
     *
     */
    public function show(Request $request, Movie $movie): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $movie->addComment($comment);
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Le commentaire a bien été ajouté');

            return $this->redirectToRoute('movie_show', ['slug' => $movie->getSlug()]);
        }

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }
}