<?php


namespace Espo\Core\Loaders;

class EntityManager extends Base
{
    public function load()
    {
        $config = $this->getContainer()->get('config');

        $params = array(
            'host' => $config->get('database.host'),
            'port' => $config->get('database.port'),
            'dbname' => $config->get('database.dbname'),
            'user' => $config->get('database.user'),
            'charset' => $config->get('database.charset', 'utf8'),
            'password' => $config->get('database.password'),
            'metadata' => $this->getContainer()->get('ormMetadata')->getData(),
            'repositoryFactoryClassName' => '\\Espo\\Core\\ORM\\RepositoryFactory',
            'driver' => $config->get('database.driver'),
            'platform' => $config->get('database.platform'),
            'sslCA' => $config->get('database.sslCA'),
            'sslCert' => $config->get('database.sslCert'),
            'sslKey' => $config->get('database.sslKey'),
            'sslCAPath' => $config->get('database.sslCAPath'),
            'sslCipher' => $config->get('database.sslCipher')
        );

        $entityManager = new \Espo\Core\ORM\EntityManager($params);
        $entityManager->setEspoMetadata($this->getContainer()->get('metadata'));
        $entityManager->setHookManager($this->getContainer()->get('hookManager'));
        $entityManager->setContainer($this->getContainer());

        return $entityManager;
    }
}

