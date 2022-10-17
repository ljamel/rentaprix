<?php

namespace App\Test\Controller;

use App\Entity\Calcul;
use App\Repository\CalculRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalculControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CalculRepository $repository;
    private string $path = '/calcul/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Calcul::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Calcul index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'calcul[title]' => 'Testing',
            'calcul[date]' => 'Testing',
            'calcul[devis]' => 'Testing',
            'calcul[durationMonth]' => 'Testing',
        ]);

        self::assertResponseRedirects('/calcul/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Calcul();
        $fixture->setTitle('My Title');
        $fixture->setDate('My Title');
        $fixture->setDevis('My Title');
        $fixture->setDurationMonth('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Calcul');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Calcul();
        $fixture->setTitle('My Title');
        $fixture->setDate('My Title');
        $fixture->setDevis('My Title');
        $fixture->setDurationMonth('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'calcul[title]' => 'Something New',
            'calcul[date]' => 'Something New',
            'calcul[devis]' => 'Something New',
            'calcul[durationMonth]' => 'Something New',
        ]);

        self::assertResponseRedirects('/calcul/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDate());
        self::assertSame('Something New', $fixture[0]->getDevis());
        self::assertSame('Something New', $fixture[0]->getDurationMonth());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Calcul();
        $fixture->setTitle('My Title');
        $fixture->setDate('My Title');
        $fixture->setDevis('My Title');
        $fixture->setDurationMonth('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/calcul/');
    }
}
