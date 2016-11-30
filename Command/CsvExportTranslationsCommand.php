<?php

/*
 * This file is part of the AsmTranslationLoaderBundle package.
 *
 * (c) Marc Aschmann <maschmann@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asm\TranslationLoaderBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Asm\TranslationLoaderBundle\Entity\Translation;

/**
 * Class CsvExportTranslationsCommand
 *
 * @package Asm\TranslationLoaderBundle\Command
 */
class CsvExportTranslationsCommand extends BaseTranslationCommand
{

    private $exportDir;
    private $exportDomains;
    private $force;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('asm:translations:csv_export')
            ->setDescription('export db translations into csv files')
            ->addOption(
                'dir',
                'd',
                InputOption::VALUE_OPTIONAL,
                'directory where files will be dumped',
                sys_get_temp_dir()
            )
            ->addOption(
                'domains',
                null,
                InputOption::VALUE_OPTIONAL,
                'to restict domains, separated by a comma',
                null
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'erase and create files without confirmation'
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir        = $input->getOption('dir');
        $domains    = $input->getOption('domains');
        $force      = $input->getOption('force');


        $this->exportDir        = $this->checkDir($input, $output, $dir);
        $this->exportDomains    = $this->checkDomains($input, $output, $domains);
        $this->force            = $force;
        $locales                = $this->getContainer()->getParameter('asm_translation_loader.locales');

        foreach ($locales as $locale) {
            $this->generateCsvForLocale($input, $output, $locale);
        }

    }

    protected function checkDir(InputInterface $input, OutputInterface $output, $dir)
    {
        $fs = new Filesystem();
        try {
            if (!$fs->exists($dir)) {
                if (!$this->force) {
                    $helper = $this->getHelper('question');
                    $question = new ConfirmationQuestion("Dir '$dir' does no exists. Create it ?", false);
                    if (!$helper->ask($input, $output, $question)) {
                        exit;
                    }
                }
                $output->writeln("<comment>Creating dir '$dir'</comment>");
                $fs->mkdir($dir);
            }
        } catch (IOExceptionInterface $e) {
            $output->writeln("<error>An error occurred while creating your directory at '".$e->getPath()."'</error>");
            exit;
        }

        return $dir;
    }

    protected function checkDomains(InputInterface $input, OutputInterface $output, $domains)
    {
        $existingDomains = $selectedDomains = $this->getContainer()->getParameter('asm_translation_loader.domains');

        if (!empty($domains)) {
            $domains = explode(',', $domains);
            $selectedDomains = array_intersect($existingDomains, $domains);
        }

        if (!count($selectedDomains)) {
            $output->writeln("<error>No domain found</error>");
            exit;
        }

        return $selectedDomains;
    }

    protected function generateCsvForLocale(InputInterface $input, OutputInterface $output, $locale)
    {
        $output->writeln("<info>Handling locale '$locale'</info>");
        $file = sprintf(
            '%s/translation.%s.csv',
            $this->exportDir,
            $locale
        );

        $fs = new Filesystem();
        if ($fs->exists($file)) {
            if (!$this->force) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion("File '$file' already exists. Erase it ?", true);
                if (!$helper->ask($input, $output, $question)) {
                    $output->writeln("<comment>Skiping locale '$locale'</comment>");
                    return;
                }
            }
            $fs->remove($file);
        }
        $fs->touch($file);

        $handle = fopen($file, 'r+');

        $em     = $this->getContainer()->get('doctrine')->getManager();
        $repo   = $em->getRepository(Translation::class);

        $qb     = $repo->createQueryBuilder('keys');
        $qb
            ->select('keys.transKey AS key')
            ->andWhere($qb->expr()->in('keys.messageDomain', $this->exportDomains))
            ->groupBy('keys.transKey')
        ;

        foreach($this->exportDomains as $domain) {
            $alias = "_$domain";
            $dql =  $repo->createQueryBuilder($alias)
                        ->select("$alias.translation")
                        ->andWhere($qb->expr()->eq("$alias.messageDomain", "'$domain'"))
                        ->andWhere($qb->expr()->eq("$alias.transKey", 'keys.transKey'))
                        ->andWhere($qb->expr()->eq("$alias.transLocale", "'$locale'"))
                        ->setFirstResult(0)
                        ->setMaxResults(1)
                    ->getQuery()
                    ->getDQL()
            ;
            $qb->addSelect("($dql) AS $domain");
        }

        fputcsv($handle, array_merge(['Key'], $this->exportDomains));

        $iterableResult = $qb->getQuery()->iterate();
        while (false !== ($row = $iterableResult->next())) {
            fputcsv($handle, current($row));
        }

        fclose($handle);
    }
}
