<?php
namespace Kors\Com\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface
    ;

use Knp\Command\Command;

/**
 * app/console command which dumps assets defined with assetic
 */
class AsseticCommand extends Command
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('assetic:dump')
            ->setDescription('Dumps all assets to the filesystem')
            ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app 	= $this->getSilexApplication();

        $helper = $app['assetic.dumper'];

        if (isset($app['twig'])) {
            $output->writeln("<info>dumping assets defined in twig templates</info>");
            $helper->addTwigAssets();
        }

        $output->writeln("<info>dumping assets</info>");
        $helper->dumpAssets();
    }
}
