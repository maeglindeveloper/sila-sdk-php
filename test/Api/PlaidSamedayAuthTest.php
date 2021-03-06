<?php

/**
 * Plaid Sameday Auth Test
 * PHP version 7.2
 */

namespace Silamoney\Client\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\ {
    Request,
    Response
};
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Silamoney\Client\Domain\Environments;

/**
 * Plaid Sameday Auth Test
 * Tests for the plaid sameday auth endpoint in the Sila Api class.
 *
 * @category Class
 * @package Silamoney\Client
 * @author José Morales <jmorales@digitalgeko.com>
 */
class PlaidSamedayAuthTest extends TestCase
{
    /**
     *
     * @var \Silamoney\Client\Api\ApiClient
     */
    protected static $api;

    /**
     *
     * @var \Silamoney\Client\Utils\TestConfiguration
     */
    protected static $config;

    /**
     *
     * @var \JMS\Serializer\SerializerBuilder
     */
    private static $serializer;

    public static function setUpBeforeClass(): void
    {
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
        self::$serializer = SerializerBuilder::create()->build();
        $json = file_get_contents(__DIR__ . '/Data/Configuration.json');
        self::$config = self::$serializer->deserialize($json, 'Silamoney\Client\Utils\TestConfiguration', 'json');
        self::$api = SilaApi::fromDefault(self::$config->appHandle, self::$config->privateKey);
    }

    /**
     * @test
     */
    public function testPlaidSamedayAuth200()
    {
        $body = file_get_contents(__DIR__ . '/Data/PlaidSamedayAuth200.json');
        $mock = new MockHandler([
            new Response(200, [], $body)
        ]);
        $handler = HandlerStack::create($mock);
        self::$api->getApiClient()->setApiHandler($handler);
        $response = self::$api->plaidSamedayAuth(self::$config->userHandle, "Custom Account Name");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Plaid public token succesfully created", $response->getData()->getMessage());
        $this->assertEquals("SUCCESS", $response->getData()->getStatus());
        $this->assertEquals("token", $response->getData()->getPublicToken());
    }

    /**
     * @test
     */
    public function testPlaidSamedayAuth400()
    {
        $this->expectException(ClientException::class);
        $body = file_get_contents(__DIR__ . '/Data/PlaidSamedayAuth400.json');
        $mock = new MockHandler([
            new ClientException("Bad Request", new Request('POST', Environments::SANDBOX), new Response(400, [], $body))
        ]);
        $handler = HandlerStack::create($mock);
        self::$api->getApiClient()->setApiHandler($handler);
        $response = self::$api->plaidSamedayAuth(self::$config->userHandle, "Incorrect Account Status");
    }

    /**
     * @test
     */
    public function testPlaidSamedayAuth404()
    {
        $this->expectException(ClientException::class);
        $body = file_get_contents(__DIR__ . '/Data/PlaidSamedayAuth404.json');
        $mock = new MockHandler([
            new ClientException("Not Found", new Request('POST', Environments::SANDBOX), new Response(404, [], $body))
        ]);
        $handler = HandlerStack::create($mock);
        self::$api->getApiClient()->setApiHandler($handler);
        $response = self::$api->plaidSamedayAuth(self::$config->userHandle, "Not A Valid Account");
    }
}
