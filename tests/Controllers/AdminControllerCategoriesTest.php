<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Category;

class AdminControllerCategoriesTest extends WebTestCase
{
    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testTextOnPage(): void
    { 
        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertSame(500, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Categories list');
        $this->assertContains('Electronics', $this->client->getResponse());
    }
}
