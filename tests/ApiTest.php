<?php

use App\Controllers\Api;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public Api $api;
    public string $baseUrl;

    public function setUp(): void
    {
        $this->api = new Api([]);
        $this->baseUrl = 'http://localhost:8080/api';
    }

    public function testeLApiDesProduitsSansFiltres()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/articles-without-filters.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/products');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesProduitsAvecUnTriParDate()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/articles-sorted-by-date.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/products?sort=date');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesProduitsAvecUnTriParVues()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/articles-sorted-by-views.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/products?sort=views');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesProduitsAvecUneRechercheParNom()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/articles-with-search-filter-jeu.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/products?search=Jeu');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesProduitsAvecUneGeolocationALyon()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/articles-with-geolocation-filter.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/products?latitude=45.78329355002979&longitude=4.88261570302125&radius=30');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesVillesAvecUneRechercheParNom()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/cities-with-search-filter-ly.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/cities?query=ly');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDeNombreDeDonationsParUtilisateur()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/donate-per-user.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/donatePerUser');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesArticlesLesPlusVus()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/most-viewed.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/mostViewed');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesArticlesLesPlusContactes()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/most-contacted.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/mostContacted');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }

    public function testeLApiDesStatistiques()
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/Expected/statistics.json'));
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->get($this->baseUrl . '/statistics');
            $this->assertEquals($expected, json_decode($response->getBody()));
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }
}