<?php

namespace App\Tests;

use http\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Faker\Factory;

class CartApiTest extends WebTestCase
{
    public function testListProducts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products/');

        $content = (string) $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString('title', $content);
    }

    public function testShowProduct(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products/1');

        $content = (string) $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString('title', $content);
    }

    public function testAddProduct(): void
    {
        $faker = Factory::create();

        $title = $faker->word;
        $price = (string) $faker->randomFloat(2,  0, 100);

        $client = static::createClient();
        $client->request('POST', '/products/', [], [], [], json_encode([
            'title' => $title,
            'price' => $price
        ]));

        $content = (string) $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString($title, $content);
        $this->assertStringContainsString($price, $content);
    }


    public function testUpdateProduct(): void
    {
        $faker = Factory::create();

        $title = $faker->word;
        $price = (string) $faker->randomFloat(2,  0, 100);

        $client = static::createClient();
        $client->request('PATCH', '/products/1', [], [], [], json_encode([
            'title' => $title,
            'price' => $price
        ]));

        $content = (string) $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString($title, $content);
        $this->assertStringContainsString($price, $content);
    }

    public function testDeleteProduct(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/products/1');

        $this->assertResponseIsSuccessful();
    }

    public function testCreateCart(): void
    {
        $client = static::createClient();
        $client->request('POST', '/cart/', [], [], [], '{}');

        $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
    }

    public function testAddAndRemoveProduct(): void
    {
        // create cart

        $client = static::createClient();
        $client->request('POST', '/cart/', [], [], [], '{}');

        $content = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        // add product to the cart

        $cartId = json_decode($content, true)['id'];

        $client->request('POST', "/cart/$cartId/add_product/1", [], [], [], '{}');

        $this->assertResponseIsSuccessful();

        $client->request('POST', "/cart/$cartId/remove_product/1", [], [], [], '{}');

        $this->assertResponseIsSuccessful();
    }

    public function testCantAddMoreThan3Products(): void
    {
        // create cart

        $client = static::createClient();
        $client->request('POST', '/cart/', [], [], [], '{}');

        $content = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        // add product to the cart

        $cartId = json_decode($content, true)['id'];

        $client->request('POST', "/cart/$cartId/add_product/1", [], [], [], '{}');
        $client->request('POST', "/cart/$cartId/add_product/2", [], [], [], '{}');
        $client->request('POST', "/cart/$cartId/add_product/3", [], [], [], '{}');
        $client->request('POST', "/cart/$cartId/add_product/4", [], [], [], '{}');

        $this->assertResponseStatusCodeSame(500);
    }
}
