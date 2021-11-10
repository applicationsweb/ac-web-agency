<?php

namespace App\Controller\Admin;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Security\MovieVoter;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/movie")
 */
class MovieController extends AbstractController
{
    /**
     * Liste des films
     * 
     * @Route("/", methods="GET", name="admin_index")
     * @Route("/", methods="GET", name="admin_movie_index")
     */
    public function index(MovieRepository $movies): Response
    {
        $authorMovies = $movies->findBy([], ['created' => 'DESC']);

        return $this->render('admin/movie/index.html.twig', ['movies' => $authorMovies]);
    }


    /**
     * Créer un nouveau film
     *
     * @Route("/new", methods="GET|POST", name="admin_movie_new")
     *
     */
    public function new(Request $request): Response
    {
        $movie = new Movie();
        $movie->setAuthor($this->getUser());

        $form = $this->createForm(MovieType::class, $movie)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($movie);
            $em->flush();

            $this->addFlash('success', 'Le film a bien été ajouté');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_movie_new');
            }

            return $this->redirectToRoute('admin_movie_index');
        }

        return $this->render('admin/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Détails d'un film
     *
     * @Route("/{id<\d+>}", methods="GET", name="admin_movie_show")
     */
    public function show(Movie $movie): Response
    {
        return $this->render('admin/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * Modification d'un film
     *
     * @Route("/{id<\d+>}/edit", methods="GET|POST", name="admin_movie_edit")
     */
    public function edit(Request $request, Movie $movie): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le film a bien été modifié');

            return $this->redirectToRoute('admin_movie_edit', ['id' => $movie->getId()]);
        }

        return $this->render('admin/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un film
     *
     * @Route("/{id}/delete", methods="POST", name="admin_movie_delete")
     */
    public function delete(Request $request, Movie $movie): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_movie_index');
        }

        $movie->getKinds()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();

        $this->addFlash('success', 'Le film a bien été supprimé');

        return $this->redirectToRoute('admin_movie_index');
    }
}
