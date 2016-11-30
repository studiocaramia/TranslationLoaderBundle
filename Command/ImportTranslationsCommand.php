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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Class DumpTranslationFiles
 *
 * @package Asm\TranslationLoaderBundle\Command
 * @author marc aschmann <maschmann@gmail.com>
 * @uses Symfony\Component\Console\Input\InputArgument
 * @uses Symfony\Component\Console\Input\InputInterface
 * @uses Symfony\Component\Console\Input\InputOption
 * @uses Symfony\Component\Console\Output\OutputInterface
 * @uses Symfony\Component\Translation\Catalogue\DiffOperation
 * @uses Symfony\Component\Translation\Catalogue\MergeOperation
 * @uses Symfony\Component\Translation\MessageCatalogue
 * @uses Symfony\Component\Finder\Finder
 * @uses Asm\TranslationLoaderBundle\Entity\Translation
 */
class ImportTranslationsCommand extends BaseTranslationCommand
{
    /**
     * message catalogue container
     *
     * @var MessageCatalogueInterface[]
     */
    private $catalogues = array();

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('asm:translations:import')
            ->setDescription('import translations selected in ressources config section from files to db')
            ->addOption(
                'clear',
                'c',
                null,
                'clear database before import'
            );
    }


    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            '<info>--------------------------------------------------------------------------------</info>'
        );
        $output->writeln('<info>Translation file importer</info>');
        $output->writeln(
            '<info>--------------------------------------------------------------------------------</info>'
        );
        $output->writeln('<info>importing all available translation files ...</info>');

        if ($input->getOption('clear')) {
            $output->writeln('<comment>deleting all translations from database...</comment>');
            $output->writeln(
                '<info>--------------------------------------------------------------------------------</info>'
            );
            $output->writeln('');

            $translationManager = $this->getTranslationManager();
            $translations       = $translationManager->findAllTranslations();
            foreach ($translations as $translation) {
                $translationManager->removeTranslation($translation);
            }
        }

        $this->generateCatalogues($output);
        $this->importCatalogues($output);

        $output->writeln(
            '<info>--------------------------------------------------------------------------------</info>'
        );
        $output->writeln('<comment>finished!</comment>');
    }


    /**
     * iterate all bundles and generate a message catalogue for each locale
     *
     * @param OutputInterface $output
     * @throws \ErrorException
     */
    private function generateCatalogues($output)
    {
        $translationWriter = $this->getTranslationWriter();
        $supportedFormats  = $translationWriter->getFormats();

        $handledRessources = $this->getContainer()->getParameter('asm_translation_loader.resources');

        // iterate all bundles and get their translations
        foreach (array_keys($this->getContainer()->getParameter('kernel.bundles')) as $bundle) {
            $currentBundle   = $this->getKernel()->getBundle($bundle);
            $translationPath = $currentBundle->getPath() . '/Resources/translations';

            // load any existing translation files
            if (is_dir($translationPath)) {
                $output->writeln('<info>searching ' . $bundle . ' translations</info>');
                $output->writeln(
                    '<info>--------------------------------------------------------------------------------</info>'
                );

                $finder = new Finder();
                $files  = $finder
                    ->files()
                    ->in($translationPath);

                foreach ($files as $file) {
                    /** @var SplFileInfo $file */
                    $extension = explode('.', $file->getFilename());
                    // domain.locale.extension
                    if (3 == count($extension)) {
                        $fileExtension = array_pop($extension);
                        if (in_array($fileExtension, $supportedFormats)) {
                            $locale = array_pop($extension);
                            $domain = array_pop($extension);

                            if (!isset($handledRessources[$locale]) || !in_array($domain, $handledRessources[$locale])) {
                                $output->writeln(
                                    '<comment>skipping ' . $file->getFilename(
                                    ) . ' with locale ' . $locale . ' and domain ' . $domain . '</comment>'
                                );
                                continue;
                            }

                            if (empty($this->catalogues[$locale])) {
                                $this->catalogues[$locale] = new MessageCatalogue($locale);
                            }

                            $fileLoader = $this->getFileLoaderResolver()->resolveLoader($fileExtension);

                            if (null === $fileLoader) {
                                throw new \ErrorException('could not find loader for ' . $fileExtension . ' files!');
                            }

                            $output->writeln(
                                '<comment>loading ' . $file->getFilename(
                                ) . ' with locale ' . $locale . ' and domain ' . $domain . '</comment>'
                            );
                            $currentCatalogue = $fileLoader->load($file->getPathname(), $locale, $domain);
                            $this->catalogues[$locale]->addCatalogue($currentCatalogue);
                        }
                    }
                }
                $output->writeln('');
            }
        }
    }


    /**
     * look through the catalogs and store them to database
     *
     * @param OutputInterface $output
     */
    private function importCatalogues($output)
    {
        $translationManager = $this->getTranslationManager();

        $output->writeln('<info>inserting all translations</info>');
        $output->writeln(
            '<info>--------------------------------------------------------------------------------</info>'
        );

        foreach ($this->catalogues as $locale => $catalogue) {

            $output->write('<comment>' . $locale . ': </comment>');
            foreach ($catalogue->getDomains() as $domain) {
                foreach ($catalogue->all($domain) as $key => $message) {
                    if ('' !== $key) {
                        $translation = $translationManager->findTranslationBy(
                            array(
                                'transKey'      => $key,
                                'transLocale'   => $locale,
                                'messageDomain' => $domain,
                            )
                        );

                        if (!$translation) {
                            // create a new translation if no entry does exist yet
                            $translation = $translationManager->createTranslation();
                            $translation->setTransKey($key);
                            $translation->setTransLocale($locale);
                            $translation->setMessageDomain($domain);
                            $translation->setTranslation($message);
                            $translationManager->updateTranslation($translation);
                        } elseif ($translation->getTranslation() != $message) {
                            // update only if we've got a changed message
                            $translation->setTranslation($message);
                            $translationManager->updateTranslation($translation);
                        }
                    }
                }
                $output->write('<info> ... ' . $domain . '.' . $locale . '</info>');
                // force garbage collection
                gc_collect_cycles();
            }
            $output->writeln('');
        }
    }

    /**
     * @return \Asm\TranslationLoaderBundle\Translation\FileLoaderResolver
     */
    private function getFileLoaderResolver()
    {
        return $this->getContainer()->get('asm_translation_loader.file_loader_resolver');
    }
}
