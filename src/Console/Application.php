<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\Console;

use Symfony\Component\Console\Application as SymfonyApplication;

final class Application extends SymfonyApplication
{
    /** @private */
    const LOGO = <<<'ASCII'

    ____  __  ______     _____                           
   / __ \/ / / / __ \   / ___/_________  ____  ___  _____
  / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
 / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /    
/_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/     
                                     /_/


ASCII;

    /**
     * @inheritDoc
     */
    public function getLongVersion()
    {
        return sprintf(
            '<info>%s</info> version <comment>%s</comment>',
            $this->getName(),
            $this->getVersion()
        );
    }

    /**
     * @inheritdoc
     */
    public function getHelp()
    {
        return self::LOGO . parent::getHelp();
    }
}