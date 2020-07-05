<?php


namespace Espo\Core\Loaders;

class MailSender extends Base
{
    public function load()
    {
        return new \Espo\Core\Mail\Sender(
            $this->getContainer()->get('config'),
            $this->getContainer()->get('entityManager')
        );
    }
}
