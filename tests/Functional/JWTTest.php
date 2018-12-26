<?php

namespace App\Functional\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JWTTest
 * @package App\Tests
 * @extends WebTestCase
 */
class JWTTest extends WebTestCase
{
    private const ROUTE = '/api/link';

    /**
     * @param $username
     * @param $password
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    private function requestLogin($username, $password)
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [
                'username' => $username,
                'password' => $password,
            ]
        );

        return $client;
    }

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient($username = 'mota', $password = 'test')
    {
        $client = $this->requestLogin($username, $password);

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testGetToken()
    {
        $username = 'mota';
        $password = 'test';

        $client = $this->requestLogin($username, $password);
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJsonResponse(Response::HTTP_OK, $client->getResponse());
        $this->assertArrayHasKey('token', $data);
        $this->assertSame(3, count(explode('.', $data)));
    }

    public function testAuthorized()
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', self::ROUTE);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testNotAuthorized()
    {
        $client = $this->createAuthenticatedClient('another_user', 'any_pass');
        $crawler = $client->request('GET', self::ROUTE);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testWithoutToken()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::ROUTE);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}
