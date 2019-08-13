<?php

namespace RedStor\Controllers;

use RedStor\SDK\RedStorClient;
use Slim\Views\Twig;
use ⌬\Configuration\Configuration;
use ⌬\Controllers\Abstracts\HtmlController;
use ⌬\Log\Logger;

abstract class GatewayController extends HtmlController
{
    /** @var RedStorClient */
    protected $redStorClient;

    /** @var Configuration */
    private $configuration;
    /** @var Logger */
    private $logger;

    public function __construct(
        Twig $twig,
        Configuration $configuration,
        Logger $logger
    ) {
        parent::__construct($twig);

        $this->configuration = $configuration;
        $this->logger = $logger;
        //$this->redis->client('SETNAME', $this->getCalledClassStub());
        $this->redStorClient = new RedStorClient([
            'host' => 'redstor',
        ]);
    }
}
