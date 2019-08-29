<?php

namespace Features\Service;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Fidry\AliceDataFixtures\LoaderInterface;

class Loader
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var LoaderInterface */
    private $loader;

    /** @var Storage */
    private $storage;

    /** @var bool */
    private $transaction = false;

    public function __construct(EntityManagerInterface $manager, LoaderInterface $loader, Storage $storage)
    {
        $this->manager = $manager;
        $this->loader = $loader;
        $this->storage = $storage;
        $this->transaction = false;

        $this->createSchema($manager);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    public function startTransaction()
    {
        $this->getManager()->beginTransaction();
        $this->transaction = true;
    }

    public function start()
    {
        $this->transaction = false;
    }

    public function finish()
    {
        if ($this->transaction && $this->getManager()->getConnection()->isTransactionActive()) {
            $this->getManager()->rollback();
        } else {
            $this->purge();
        }
        $this->transaction = false;
    }

    public function clear()
    {
        $this->getManager()->clear();
    }

    public function purge()
    {
        $purger = new ORMPurger($this->getManager());
        $purger->purge();
    }

    public function loadFixtures(array $files, bool $once = true)
    {
        if ($once) {
            $hash = md5(serialize($files));
            if ($this->storage->get('lastFixturesHash') !== $hash) {
                $this->purge();
                $this->storage->set('lastFixturesHash', $hash);
                $this->loadFiles($files);
            }
        } else {
            $this->purge();
            $this->loadFiles($files);
        }
    }

    private function loadFiles(array $files)
    {
        $this->loader->load($files);
        $this->getManager()->flush();
        $this->getManager()->clear();
    }

    private function createSchema(EntityManagerInterface $manager)
    {
        $schemaTool = new SchemaTool($manager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($manager->getMetadataFactory()->getAllMetadata());
    }
}
