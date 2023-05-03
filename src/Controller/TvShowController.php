<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;

class TvShowController extends AbstractController
{
    public function index(Request $request): JsonResponse
    {
        $apiKey = '4bb07248e8d38144e13c5476cc2a7576';
        $client = HttpClient::create();
        $page = $request->query->getInt('page', 1); 
        $response = $client->request('GET', 'https://api.themoviedb.org/3/tv/popular', [
            'query' => [
                'api_key' => $apiKey,
                'page' => $page,
            ],
        ]);
        $tvShows = json_decode($response->getContent());

        return new JsonResponse($tvShows);
    }
    public function show(int $id, SerializerInterface $serializer)
 {
    $apiKey = '4bb07248e8d38144e13c5476cc2a7576';
    $client = HttpClient::create();
    
    $response = $client->request('GET', "https://api.themoviedb.org/3/tv/{$id}", [
        'query' => [
            'api_key' => $apiKey,
        ],
    ]);
    $data = json_decode($response->getContent());
    
    // Return the details as a JSON response
    return new JsonResponse($serializer->normalize($data, null, ['groups' => ['default']]));
}

public function getTvShowTrailer(int $id)
  {
    $apiKey = '4bb07248e8d38144e13c5476cc2a7576';
    $client = HttpClient::create();
    $response = $client->request('GET', "https://api.themoviedb.org/3/tv/{$id}/videos", [
        'query' => [
            'api_key' => $apiKey,
        ],
    ]);
    $videos = json_decode($response->getContent());

    foreach ($videos->results as $video) {
        if ($video->type === 'Trailer') {
            return new JsonResponse($video);
        }
    }

    return new JsonResponse(null);
 }

public function watchTrailer(int $id)
{
    $apiKey = '4bb07248e8d38144e13c5476cc2a7576';
    $client = HttpClient::create();
    $response = $client->request('GET', "https://api.themoviedb.org/3/tv/{$id}/videos", [
        'query' => [
            'api_key' => $apiKey,
        ],
    ]);
    $videos = json_decode($response->getContent());

    foreach ($videos->results as $video) {
        if ($video->type === 'Trailer') {
            $trailerUrl = 'https://www.youtube.com/watch?v=' . $video->key;
            header("Location: $trailerUrl");
            exit;
        }
    }

    
    header("Location: /tv/$id");
    exit;
}
}
