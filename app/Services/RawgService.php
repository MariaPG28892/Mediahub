<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RawgService
{
    private string $baseUrl;
    private string $key;

    public function __construct()
    {
        $this->baseUrl = config('services.rawg.base_url');
        $this->key = config('services.rawg.key');
    }

    //Sacar juegos con buena portada
    public function getJuegosBonitos($page = 1)
    {
        return Cache::remember("rawg_bonitos_{$page}", 3600, function () use ($page) {

            return Http::timeout(20)
                ->get($this->baseUrl . '/games', [
                    'key' => $this->key,
                    'page' => $page,
                    'ordering' => '-added',
                    'page_size' => 20,
                ])
                ->json();
        });
    }

    //buscar 
    public function buscarJuegos($query, $page = 1)
    {
        return Cache::remember("rawg_search_{$query}_{$page}", 3600, function () use ($query, $page) {

            return Http::timeout(30)
                ->get($this->baseUrl . '/games', [
                    'key' => $this->key,
                    'search' => $query,
                    'search_precise' => 1,
                    'page' => $page,
                    'page_size' => 20,
                ])
                ->json();
        });
    }

    //Buscar por genero
    public function getJuegosPorGenero($generoId, $page = 1)
    {
        return Cache::remember("rawg_genre_{$generoId}_{$page}", 3600, function () use ($generoId, $page) {

            return Http::timeout(20)
                ->get($this->baseUrl . '/games', [
                    'key' => $this->key,
                    'genres' => $generoId,
                    'page' => $page,
                    'page_size' => 20,
                    'ordering' => '-rating',
                ])
                ->json();
        });
    }

    //Sacar los generos
    public function getGeneros()
    {
        return Cache::remember("rawg_genres", 86400, function () {

            return Http::timeout(20)
                ->get($this->baseUrl . '/genres', [
                    'key' => $this->key,
                ])
                ->json();
        });
    }

    //Sacar el juego y sus detalles.
    public function getJuego($id)
    {
        return Cache::remember("rawg_game_{$id}", 86400, function () use ($id) {

            return Http::timeout(20)
                ->get($this->baseUrl . "/games/{$id}", [
                    'key' => $this->key,
                ])
                ->json();
        });
    }

    //Sacar las plataformas
    public function getPlataformas()
    {
        return Cache::remember("rawg_platforms", 86400, function () {

            return Http::timeout(20)
                ->get($this->baseUrl . '/platforms', [
                    'key' => $this->key,
                ])
                ->json();
        });
    }
}