<?php


namespace Espo\Core\Loaders;

class WebSocketSubmission extends Base
{
    public function load()
    {
        return new \Espo\Core\WebSocket\Submission(
            $this->getContainer()->get('config')
        );
    }
}
