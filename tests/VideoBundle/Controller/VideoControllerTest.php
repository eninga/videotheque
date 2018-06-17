<?php

namespace VideoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VideoControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertContains('Bienvenue sur la vidéothèque', $client->getResponse()->getContent());
    }
}
