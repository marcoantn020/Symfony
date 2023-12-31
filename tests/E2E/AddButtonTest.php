<?php

namespace App\Tests\E2E;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddButtonTest extends WebTestCase
{
    public function testAddButtonDoesNotExistWhenUserIsNotLoggedIn(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/series');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.btn.btn-dark.mb-2');
    }

    public function testAddButtonWhenUserIsLogged(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'tests@mail.com']);

        $client->loginUser($user);
        $crawler = $client->request('GET', '/series');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.btn.btn-dark.mb-2');
    }
}
