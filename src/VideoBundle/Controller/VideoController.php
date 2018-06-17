<?php

namespace VideoBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use VideoBundle\Entity\Category;
use VideoBundle\Entity\Compteur;
use VideoBundle\Entity\Film;
use VideoBundle\Form\CategoryType;
use VideoBundle\Form\FilmType;

/**
 * Cette classe VideoController contient les controlleurs pour
 *  la gestion de la vidéothèque
 *
 * @author eningabiye
 */
class VideoController extends Controller {

    /**
     * Affichage de la page d'accueil
     * @return Response
     */
    public function indexAction() {
        return $this->render('@Video/video/index.html.twig');
    }

    /**
     * Création d'une nouvelle catégorie, 
     * l'utilisateur est redirigé à page de creation de film si la catégorie est enregistrée
     * @param Request $request
     * @return Response la reponse à afficher au navigateur
     */
    public function postCategoryAction(Request $request) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('video_add_film');
        }
        return $this->render("@Video/video/form_categorie.html.twig", ['form' => $form->createView()]);
    }

    /**
     * Afficgafe de la liste des catégories 
     * @return Response la reponse à afficher au navigateur
     */
    public function getCategoriesAction() {
        $categories = $this->getDoctrine()->getRepository('VideoBundle:Category')->findAll();
        return $this->render("@Video/video/categories.html.twig", ["categories" => $categories]);
    }

    /**
     * Recupération et affichage des details du film dont l'Id est $id
     * @param type $id  L'Id du film
     * @return Response la reponse à afficher au navigateur
     */
    public function getFilmAction($id) {
        $film = $this->getDoctrine()->getRepository('VideoBundle:Film')->find($id);
        $this->checkFilm($film);
        return $this->render("@Video/video/details_film.html.twig", ["film" => $film]);
    }

    /**
     * Creation d'un nouveau film ou modification du film dont l'id est $id
     * C'est un nouveau film si $id est null, sinon une modification
     */
    public function postFilmAction(Request $request, $id = null) {
        $film = null;
        if ($id != null) {
            $film = $this->getDoctrine()->getRepository('VideoBundle:Film')->find($id);
            $this->checkFilm($film);
            $path = $film->getPhoto(); // On garde en mémoire l'ancienne valeur de la photo
        } else {
            $film = new Film(); // Crée une nouveller instance si le film n'existe pas encore
        }
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $film->getPhoto(); // Ici le $photo peut être l'ancienne ou la nouvelle uplaodée
            if ($photo != null && $request->files != null) {// Vérifie s'il y a une photo uploadée
                if ($id != null && $path != null) {
                    $this->deletePhoto($path); // On supprime l'ancienne photo s'il y a une nouvelle
                }
                $fileName = "photo_" . md5(uniqid()) . '.' . $photo->guessExtension();
                $photo->move($this->getParameter('photo_affichage'), $fileName);
                $film->setPhoto($fileName);
            } elseif ($path != null) {
                $film->setPhoto($path); // En cas de modification sans uploader une photo, on garde l'ancienne si elle existe
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($film);
            $this->incrementCompteur($entityManager, $id);
            $entityManager->flush();
            $this->notify($film, $id, 'add'); //envoie de l'email à l'admin
            return $this->redirectToRoute('video_get_films_categories', ['categorie' => $film->getCategorie()->getId()]);
        }
        return $this->render("@Video/video/form_films.html.twig", ['form' => $form->createView()]);
    }

    /**
     * 
     * @param type $id l'id du film à supprimer
     * @return RedirectResponse
     * @throws Exception lors de la supprission de la photo d'affichage
     */
    public function deleteFilmAction($id) {
        $film = $this->getDoctrine()->getRepository('VideoBundle:Film')->find($id);
        $this->checkFilm($film);
        if ($film && $film->getPhoto() != null) {
            $this->deletePhoto($film->getPhoto());
        }
        $categorie = $film->getCategorie()->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($film);
        $compteur = $this->compteur();
        $compteur->setTotal($compteur->getTotal() - 1);
        $entityManager->persist($compteur);
        $entityManager->flush();
        $this->notify($film, null, 'delete'); //envoie de l'email à l'admin
        return $this->redirectToRoute('video_get_films_categories', ['categorie' => $categorie]);
    }

    /**
     * Affichage du formulaire pour la recherche d'un film
     * @return Response la reponse à afficher au navigateur
     */
    public function searchFormAction() {
        $categories = $this->getDoctrine()->getRepository('VideoBundle:Category')->findAll();
        return $this->render("@Video/video/search_film.html.twig", ["categories" => $categories]);
    }

    /**
     * Execution de la recherche
     * @param Request $request
     * @return Response  la reponse à afficher au navigateur
     */
    public function postSearchFilmsAction(Request $request) {
        $categorie_id = $request->request->get("categorie");
        $titre = trim($request->request->get("titre"));
        $date = trim($request->request->get("date"));
        $category = $this->getDoctrine()->getRepository('VideoBundle:Category')->find($categorie_id);
        $films = null;
        if ($category) {
            $films = $this->getDoctrine()->getRepository('VideoBundle:Film')->findFilms($titre, $category, $date);
        }
        return $this->render("@Video/video/liste_films.html.twig", ["films" => $films]);
    }

    /**
     * Affichage des films par categorie et avec pagination
     * @param Request $request
     * @param Category $categorie la catégorie des films
     * @return Response la reponse à afficher au navigateur
     */
    public function getFilmsByCategorieAction(Request $request, $categorie) {
        $page = $request->query->getInt('page', 1); //retourne 1 s'il n'a pas de valeur pour page
        $limit = $this->getParameter('limit_films');
        $query = $this->getDoctrine()->getRepository('VideoBundle:Film')->findFilmsByCategory($categorie);
        $paginator = $this->get('knp_paginator');
        $films = $paginator->paginate($query, $page, $limit);
        return $this->render("@Video/video/liste_filsms_pagination.html.twig", ["films" => $films]);
    }

    /**
     * Affichage du compteur des films,
     * ce controlleur est chargés dans dans toutes les pages
     * @return Response la reponse à afficher au navigateur
     */
    public function compteurAction() {
        $compteur = $this->compteur() != null ? $this->compteur()->getTotal() : 0;
        return $this->render("@Video/video/compteur.html.twig", ["compteur" => $compteur]);
    }

    /**
     * 
     * @return Compteur $compteur object unique dans la table compteur
     */
    public function compteur() {
        return $compteur = $this->getDoctrine()->getRepository('VideoBundle:Compteur')->countFilms();
    }

    /**
     * 
     * @return integer le nombre de films si le compteur existe, sinon zéro
     */
    public function counter() {
        return $this->compteur() != null ? $this->compteur()->getTotal() : 0;
    }

    /**
     * Cette methode fournie la vue à envoyé dans le mail de notification à l'admin
     * @param Film $film Le film traité
     * @param string $action "delete" si suppression ou "add" si ajout de film
     * @return Response la reponse à afficher au navigateur
     */
    public function emailView($film, $action) {
        return $this->renderView('@Video/video/email.html.twig', array('action' => $action, 'film' => $film));
    }

    /**
     * On incremente le compteur si un nouveau film est ajouté
     * @param $entityManager
     * @param integer $id
     */
    public function incrementCompteur($entityManager, $id) {
        $compteur = null;
        if ($id == null) {
            if ($this->compteur() == null) {
                $compteur = new Compteur();
                $compteur->setTotal(1);
            } else {
                $compteur = $this->compteur();
                $compteur->setTotal($compteur->getTotal() + 1);
            }
            $entityManager->persist($compteur);
        }
    }

    /**
     * Envoi de l'email à l'admin
     * @param Film $film
     * @param integer $id Id du film, 
     * @param string $action delete ou add
     */
    public function notify($film, $id, $action) {
        if ($id == null) {
            $notifier = $this->container->get("video.notify");
            $notifier->notify($this->emailView($film, $action));
        }
    }

    /**
     * Suppression de la photo sur le disque de la machine
     * @param string $path le nom de laphoto dans la base de donnée
     * @throws Exception
     */
    public function deletePhoto($path) {
        $fs = new Filesystem();
        try {
            $fs->remove($this->getParameter('photo_affichage') . "/" . $path);
        } catch (IOExceptionInterface $exception) {
            throw new Exception("Erreur lors de la suppression " . $exception->getPath());
        }
    }

    /**
     * Verifie si le film existeavant un traitement
     * @param Film $film
     * @throws Exception si le film n'est pas trouvé
     */
    public function checkFilm($film) {
        if (!$film) {
            throw $this->createNotFoundException("Le film n'a pas été trouvé!");
        }
    }

}
