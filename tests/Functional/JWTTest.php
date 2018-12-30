<?php

namespace App\Tests\Functional;

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
    private const username = 'anderson.mota12@gmail.com';
    private const password = '1234';

    /**
     * @param $username
     * @param $password
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    private function requestLogin($username, $password)
    {
        $credentials = [
            'username' => $username,
            'password' => $password,
        ];

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [],
            json_encode($credentials)
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
    protected function createAuthenticatedClient($username = self::username, $password = self::password)
    {
        $client = $this->requestLogin($username, $password);
        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();

        if (isset($data['token'])) {
            $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        }

        return $client;
    }

    public function testGetToken()
    {
        $username = self::username;
        $password = self::password;

        $client = $this->requestLogin($username, $password);
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('token', $data);
        $this->assertSame(3, count(explode('.', $data['token'])));
    }

    public function testAuthorized()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', self::ROUTE);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testNotAuthorized()
    {
        $client = $this->createAuthenticatedClient('another_user', 'any_pass');
        $client->request('GET', self::ROUTE);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', self::ROUTE);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}
