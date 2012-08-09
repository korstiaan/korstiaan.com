<?php

namespace Kors\Com\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface
    ;

use Knp\Command\Command;

/**
 * app/console command symlinks bootstrap's assets to web dir
 */
class SymlinkCommand extends Command
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('symlink:create')
            ->setDescription('Symlinks bootstrap assets to web dir')
            ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();

        $linkBase = "{$app['dir.web']}/assets/packages/bootstrap";

        if (!is_dir($linkBase)) {
            mkdir($linkBase, 0777, true);
        }

        $targetBase = "{$app['dir.vendor']}/twitter/bootstrap";

        foreach (array(
            'js',
            'img',
            'less',
        ) as $dir) {
            $target = "{$targetBase}/$dir";
            if (!is_dir($target)) {
                throw new \LogicException("Twitter bootstrap's {$dir} can't be found at {$target}");
            }
            $link = "{$linkBase}/$dir";
            if (is_dir($link)) {
                $output->writeln("<comment>symlink to bootstrap's {$dir} already exists, skipping</comment>");
                continue;
            }
            if (false === @symlink($target, $link)) {
                throw new \Exception("Unable to create symlink to {$target}");
            }
            $output->writeln("<info>created symlink to bootstrap's {$dir}</info>");
        }
    }
}
