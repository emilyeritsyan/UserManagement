<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase {
    public $client = null;
//    public function testadd() {
//        $client = static::createClient();
//        $params = array(
//            'username' => 'Mali2'
//        );
////        $params = '{"username" : "Mali2"}';
//        $crawler = $client->request('POST', '/users', json_encode($params, 1));
//
//        $this->assertEquals(201, $client->getResponse()->getStatusCode());
////        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
//    }
//
//    public function testget() {
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/user');
//
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
////        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
//    }

    public function testAddAction() {
        $this->client = static::createClient();
        $this->client->request(
                'POST', '/users',  '{"username" : "Mali2"}'
        );
        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());
//        $this->assertJsonResponse($this->client->getResponse(), 200, false);
    }

    protected function assertJsonResponse($response, $statusCode = 200) {
        $this->assertEquals(
                $statusCode, $response->getStatusCode(), $response->getContent()
        );
        $this->assertTrue(
                $response->headers->contains('Content-Type', 'application/json'), $response->headers
        );
    }

}
