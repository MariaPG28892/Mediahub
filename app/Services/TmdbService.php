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
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/discover/movie', [
                'sort_by' => 'popularity.desc',
                'include_adult' => false,
                'vote_count.gte' => 100,
                'page' => $page
            ])->json();

        // Palabras clave a bloquear
        $palabrasBloqueadas = ['sex', 'lust', 'erotic', 'affair', 'voyeur', 'betrayal'];

        // Filtrado adicional
        $response['results'] = array_filter($response['results'], function ($peli) use ($palabrasBloqueadas) {
            $texto = strtolower($peli['overview'] ?? '');

            foreach ($palabrasBloqueadas as $palabra) {
                if (str_contains($texto, $palabra)) {
                    return false;
                }
            }

            return !$peli['adult'];
        });

        return $response;
    }

    //Función para buscar las películas desde la API
    public function buscarPeliculas($query, $page = 1)
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/search/movie', [
                'query' => $query,
                'page' => $page,
                'include_adult' => false
            ])->json();

        $palabrasBloqueadas = ['sex', 'lust', 'erotic', 'affair', 'voyeur', 'betrayal'];

        $response['results'] = array_filter($response['results'], function ($peli) use ($palabrasBloqueadas) {
            $texto = strtolower($peli['overview'] ?? '');

            foreach ($palabrasBloqueadas as $palabra) {
                if (str_contains($texto, $palabra)) {
                    return false;
                }
            }

            return !$peli['adult'];
        });

        return $response;
    }

    //Filtrar según el género
    public function getPeliculasPorGenero($generoId, $page = 1)
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/discover/movie', [
                'with_genres' => $generoId,
                'page' => $page,
                'sort_by' => 'popularity.desc',
                'include_adult' => false
            ])
            ->json();

        $palabrasBloqueadas = ['sex', 'lust', 'erotic', 'affair', 'voyeur', 'betrayal'];

        $response['results'] = array_filter($response['results'], function ($peli) use ($palabrasBloqueadas) {
            $texto = strtolower($peli['overview'] ?? '');

            foreach ($palabrasBloqueadas as $palabra) {
                if (str_contains($texto, $palabra)) {
                    return false;
                }
            }

            return !$peli['adult'];
        });

        return $response;
    }

     // Series populares
    public function getSeriesPopulares($page = 1)
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/tv/popular', [
                'page' => $page
            ])->json();

        return $response;
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