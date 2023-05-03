<?php

namespace App\Controller;


use App\Entity\Favorite;
use App\Entity\User;
use App\Entity\Movie;
use App\Entity\TvShow;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MovieRepository;
use App\Repository\FavoriteRepository;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;


class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function register(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
    
        $username = $data['username'];
        $password = $data['password'];
    
      
        if (empty($username) || empty($password)) {
            return new JsonResponse(['error' => 'Username and password are required'], 400);
        }
    
        
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Username already exists'], 400);
        }
    
        
        $user = new User();
        $user->setUsername($username);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT)); // hash password
    
        // save user to database
        $entityManager->persist($user);
        $entityManager->flush();
    
        return new JsonResponse(['success' => 'User registered successfully'], 201);
    }


    public function addFavoriteMovie(Request $request, int $userId, int $movie_id, EntityManagerInterface $entityManager, MovieRepository $movieRepository): JsonResponse

{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['id'])) {
        return new JsonResponse(['error' => 'Movie ID is required'], 400);
    }

    $movieId = $data['id'];

    $movie = $movieRepository->find($movieId);

    if (!$movie) {
        return new JsonResponse(['error' => 'Movie not found'], 404);
    }

    $userRepository = $entityManager->getRepository(User::class);

    $user = $userRepository->find($userId);

    if (!$user) {
        return new JsonResponse(['error' => 'User not found'], 404);
    }

    $user->addFavorite($movie);

    $entityManager->flush();

    return new JsonResponse(['success' => sprintf('Movie "%s" added to favorites', $movie->getTitle())], 201);
}

public function addFavoriteMovieFromApi(int $userId, int $movieId, EntityManagerInterface $entityManager, MovieRepository $movieRepository): JsonResponse
{
    // Fetch movie details from the API

    $client = HttpClient::create();
    $apiKey = '4bb07248e8d38144e13c5476cc2a7576';
    $response = $client->request('GET', "https://api.themoviedb.org/3/movie/", [
        'query' => [
            'api_key' => $apiKey,
        ],
    ]);
    $data = json_decode($response->getContent(), true);

    $movieId = intval($data['id']);

    $existingMovie = $movieRepository->findOneBy(['apiId' => $movieId]);
    if ($existingMovie) {
        $movie = $existingMovie;
    } else {
        
        $movie = new Movie();
        $movie->setTitle($data['title']);
        $movie->setApiId($data['id']);
        $movie->setOverview($data['overview']);
        $movie->setReleaseDate(new \DateTime($data['release_date']));
        $movie->setPosterPath($data['poster_path']);
        $entityManager->persist($movie);
        $entityManager->flush();
    }

    // Add the movie to the user's favorites

    $user = $this->getUser();
    if ($user->getId() !== $userId) {
        return new JsonResponse(['error' => 'Invalid user ID'], 400);
    }
    $favorite = $this->addToFavorites($user, $movie);
    return new JsonResponse(['success' => sprintf('Movie "%s" added to favorites', $movie->getTitle()), 'favorite_id' => $favorite->getId()], 201);
}


// list of favourite 
public function getFavorites(int $id, FavoriteRepository $favoriteRepository): JsonResponse
{
    $userRepository = $this->entityManager->getRepository(User::class);
     $user = $userRepository->find($id);
    if (!$user) {
        return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    $favoriteMovies = $favoriteRepository->findByUser($user);

    $movies = [];

    foreach ($favoriteMovies as $favoriteMovie) {
        $movies[] = [
            'id' => $favoriteMovie->getMovie()->getId(),
            'title' => $favoriteMovie->getMovie()->getTitle(),
            'overview' => $favoriteMovie->getMovie()->getOverview(),
            'poster_path' => $favoriteMovie->getMovie()->getPosterPath(),
            'release_date' => $favoriteMovie->getMovie()->getReleaseDate()->format('Y-m-d'),
        ];
    }

    return new JsonResponse($movies, Response::HTTP_OK);
}

    public function deleteFavorite(int $id, int $movieId, FavoriteRepository $favoriteRepository): Response
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $favoriteRepository = $this->entityManager->getRepository(Favorite::class);
        $favorite = $favoriteRepository->findOneBy(['user' => $user, 'movie' => $movieId]);

        if (!$favorite) {
            return new JsonResponse(['error' => 'Favorite not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($favorite);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

}
    
