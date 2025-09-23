<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class ScrapController extends AbstractController

{
    #[Route('/api/scrap/elpais', name: 'scrap_elpais', methods: ['GET'])]
    public function scrapeElPais(): JsonResponse
    {
        try {

            $client = HttpClient::create();

            $response = $client->request('GET', 'https://elpais.com/');

            if ($response->getStatusCode() !== 200) {
                return new JsonResponse([
                    'error' => 'No se pudo acceder a El País',
                    'status_code' => $response->getStatusCode()
                ], 500);
            }

            $html = $response->getContent();
            $crawler = new Crawler($html);
            $noticias = [];

            $crawler->filter('article')->each(function (Crawler $node, $i) use (&$noticias) {
                if ($i >= 5) return;

                $titulo = $node->filter('h2 a, h3 a, .c_t a')->text('Sin título');
                $enlace = $node->filter('h2 a, h3 a, .c_t a')->attr('href');
                $resumen = $node->filter('.c_d, .c_e, p')->text('Sin resumen');

                if (!empty($titulo) && $titulo !== 'Sin título') {
                    $noticias[] = [
                        'titulo' => trim($titulo),
                        'enlace' => $this->completarUrl($enlace),
                        'resumen' => trim($resumen)
                    ];
                }
            });

            return new JsonResponse([
                'fuente' => 'El País',
                'fecha_scraping' => date('Y-m-d H:i:s'),
                'total_noticias' => count($noticias),
                'noticias' => $noticias
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Error al scrapear El País: ' . $e->getMessage()
            ], 500);
        }
    }

    private function completarUrl(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        if (strpos($url, 'http') === 0) {
            return $url;
        }

        if (strpos($url, '/') === 0) {
            return 'https://elpais.com' . $url;
        }

        return 'https://elpais.com/' . $url;
    }

}
