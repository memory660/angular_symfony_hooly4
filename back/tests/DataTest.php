<?php

namespace App\Tests;

use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DataTest extends WebTestCase
{

    private EntityManagerInterface $entityManager;
    private LocationRepository $locationRepository;
    private static ?KernelBrowser $client = null;

    protected function setUp(): void
    {

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        if (null === self::$client) {
            self::$client = static::createClient();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function test401(): void
    {
        $client = self::$client;
        $crawler = $client->request('GET', '/api/locations');
        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(401);
    }

    /*
    public function reservationsNotAuthorized()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/reservations',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            '{"date":"","no":1,"societyId":1, "foodtrackId":1}'
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
        
    public function testRegister(): void
    {
        $client = static::createClient();
        $client->request('POST', '/auth/register', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'john@api.fr',
            "password" => "Za22!tRt"
        ]));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'email' => 'john@api.fr'
            ]),
            $client->getResponse()->getContent()
        );   
    }

    public function testThatCannotCreateTokenWithBadEmail()
    {         
        $client = static::createClient();
        $client->request('POST', '/auth/register', [], [], 
        [
			'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
        ], json_encode([
            "email" => "user1@test", 
            "password" => "aaaaaaaa"
            ])
        );
        
        $this->assertEquals(422, $client->getResponse()->getStatusCode());                      
    }    


    public function testThatCannotCreateTokenWithBadLenghtPassword()
    {         
        $client = static::createClient();
        $client->request('POST', '/auth/register', [], [], 
        [
			'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
        ], json_encode([
            "email" => "user1@test.fr", 
            "password" => "aaaaa"
            ])
        );
        
        $this->assertEquals(422, $client->getResponse()->getStatusCode());                      
    }    
    */ 
}
