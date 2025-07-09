<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Movie;
use App\Form\ImportFormType;
use App\Form\MovieFormType;
use App\Form\UpdateFormType;
use App\Repository\ActorRepository;
use App\Repository\DirectorRepository;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Service\ActorHelper;
use App\Service\DirectorHelper;
use App\Service\ExternalApiHelper;
use App\Service\MovieImportHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movie')]
class MovieController extends AbstractController
{
    private ExternalApiHelper $apiHelper;
    private string $omdbUrl;
    private string $omdbApiKey;

    public function __construct(ExternalApiHelper $apiHelper, $omdbUrl, $omdbApiKey)
    {
        $this->apiHelper = $apiHelper;
        $this->omdbUrl = $omdbUrl;
        $this->omdbApiKey = $omdbApiKey;
    }

    #[Route('/', name: 'app_movie', methods: ['GET', 'POST'])]
    public function index(
        EntityManagerInterface $entityManager,
        Request                $request,
        MovieImportHelper      $importHelper
    ): Response
    {
        $form = $this->createForm(ImportFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = $form->get('fileName')->getData();
            if ($file && $file->getClientOriginalExtension() !== 'json') {
                $this->addFlash('error', 'Das ist keine JSON-Datei!');
                return $this->redirectToRoute('app_movie');
            }
            $importHelper->importMovies($file);
        }

        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
            'importForm' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_movie_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(MovieFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Movie $movie */
            $movie = $form->getData();

            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash('success', 'Movie created! Move on to the next.');

            return $this->redirectToRoute('app_movie_list', []);
        }

        return $this->render('movie/new_entry.html.twig', [
            'movieForm' => $form->createView()
        ]);
    }

    #[Route('/list/{page<\d+>}', name: 'app_movie_list', methods: ['GET'])]
    public function list(MovieRepository $repository, int $page = 1): Response
    {
        $queryBuilder = $repository->createAllOrderedByTitleQueryBuilder();

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(8);
        $pagerfanta->setCurrentPage($page);

        return $this->render('movie/list.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/show/{id}', name: 'app_movie_show', methods: ['GET'])]
    public function show(MovieRepository $repository, $id): Response
    {
        $movie = $repository->find($id);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'path_to_video' => 'http://192.168.2.30/MyWeb/video/',
            'movie_options' => '?autoplay=1&mute=0'
        ]);
    }

    #[Route('/edit/{id}', name: 'app_movie_edit', methods: ['GET', 'POST'])]
    public function edit(
        MovieRepository        $repo,
        Request                $request,
        EntityManagerInterface $entityManager,
                               $id
    ): RedirectResponse|Response
    {
        $movie = $repo->find($id);

        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Movie $movie */
            $movie = $form->getData();

            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash('success', 'Movie successfully updated!');

            return $this->redirectToRoute('app_movie_list', []);
        }

        return $this->render('movie/edit_entry.html.twig', [
            'movieForm' => $form->createView(),
            'id' => $id
        ]);
    }

    /**
     * @Route("/movie/update/{id}", name="app_movie_update")
     */
    #[Route('/update/{id}', name: 'app_movie_update', methods: ['GET', 'POST'])]
    public function updateWithAdditionalInfo(
        int $id,
        EntityManagerInterface $entityManager,
        GenreRepository $genreRepository,
        ActorRepository $actorRepository,
        DirectorRepository $directorRepository,
        ActorHelper $actorHelper,
        DirectorHelper $directorHelper,
    ): RedirectResponse
    {
        $movieRepo = $entityManager->getRepository(Movie::class);

        $movie = $movieRepo->find($id);

        $requestData = [
            'movieTitle' => $movie->getTitle(),
            'movieYear' => $movie->getYear(),
        ];
        $movieResponse = $this->apiHelper->getAdditionalMovieInfo($requestData, $this->omdbUrl, $this->omdbApiKey);

        $genres = [];
        $actors = [];

        if (isset($movieResponse['Error'])) {
            $flashMessage = [
                'type' => 'error',
                'message' => 'Movie not found!'
            ];

            return $this->redirectToRoute('app_movie_list', [
                'flash' => $flashMessage
            ]);
        } else {
            if (isset($movieResponse['Genre'])) {
                $genres = explode(', ', $movieResponse['Genre']);
            }

            foreach ($genres as $genre) {
                $oGenre = $genreRepository->findOneBy(
                    [
                        'name' => strtolower($genre)
                    ]
                );
                if ($oGenre) {
                    $movie->addGenre($oGenre);
                }
            }
            if (isset($movieResponse['Runtime']) && $movie->getLength() == 0) {
                $movie->setLength((int)substr($movieResponse['Runtime'], 0, -4));
            }
            if (isset($movieResponse['Plot']) && $movie->getPlot() == null) {
                $movie->setPlot($movieResponse['Plot']);
            }
            if (isset($movieResponse['Actors'])) {
                $actors = explode(', ', $movieResponse['Actors']);
            }
            foreach ($actors as $actor) {
                $oActor = $actorRepository->findOneBy(['name' => $actor]);
                //dd($oActor);
                if (!$oActor) {
                    $actorHelper->importActor($actor);
                    $oActor = $actorRepository->findOneBy(['name' => $actor]);
                }
                $movie->addActor($oActor);
            }
            if (isset($movieResponse['imdbId'])) {
                $movie->setImdbId($movieResponse['imdbId']);
            }
            if (isset($movieResponse['Director'])) {
                $oDirector = $directorRepository->findOneBy(['name' => $movieResponse['Director']]);
                if (!$oDirector) {
                    $directorHelper->importDirector($movieResponse['Director']);
                    $oDirector = $directorRepository->findOneBy(['name' => $movieResponse['Director']]);
                }
                $movie->setDirector($oDirector);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Movie successfully updated!');

            return $this->redirectToRoute('app_movie_edit', [
                'id' => $id,
            ]);
        }
    }
}
