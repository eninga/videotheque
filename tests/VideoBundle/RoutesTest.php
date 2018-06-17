<?php

namespace VideoBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 * @author eningabiye
 */
class RoutesTest extends WebTestCase {

    /**
     * Test si les urls marchent bien
     * @dataProvider urls
     */
    public function testRoutes($url) {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urls() {
        return array(
            array('/'), //page d'accueil
            array('/add-category'), // formulaire d'ajout de catégorie
            array('/add-film'), // formulaire d'ajout de catégorie
            array('/categories'), // Listes de catégories
            array('/rechercher'), // formulaire de rcherche
        );
    }

}
