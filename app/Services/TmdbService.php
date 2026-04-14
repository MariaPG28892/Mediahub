<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    private string $baseUrl;
    private string $token;

    //Realizo un constructor que me guarde la URL y el token del archivo .env para poder usarlo más facilmente. 
    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->token = config('services.tmdb.token');
    }

    //Función para traer las películas más populares
    public function getPeliculasPopulares($page = 1)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/movie/popular', [
                'page' => $page
            ])->json();
    }

    //Función para buscar las películas desde la API
    public function buscarPeliculas($query, $page = 1)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/search/movie', [
                'query' => $query,
                'page' => $page,
            ])->json();
    }

    //Filtrar según el género
    public function getPeliculasPorGenero($generoId, $page = 1)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/discover/movie', [
                'with_genres' => $generoId,
                'page' => $page,
                'sort_by' => 'popularity.desc'
            ])
            ->json();
    }

     // Series populares
    public function getSeriesPopulares($page = 1)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/tv/popular', [
                'page' => $page
            ])->json();
    }

    // Buscar series
    public function buscarSeries($query, $page = 1)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/search/tv', [
                'query' => $query,
                'page' => $page,
            ])->json();
    }

    // Series por género
    public function getSeriesPorGenero($generoId, $page = 1)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . '/discover/tv', [
                'with_genres' => $generoId,
                'page' => $page,
                'sort_by' => 'popularity.desc'
            ])
            ->json();
    }
}