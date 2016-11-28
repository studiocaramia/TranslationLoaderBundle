<?php

/*
 * This file is part of the AsmTranslationLoaderBundle package.
 *
 * (c) Marc Aschmann <maschmann@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asm\TranslationLoaderBundle\Model;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base {@link TranslationManagerInterface} implementation (can be extended by
 * concrete implementations).
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class TranslationManager implements TranslationManagerInterface
{
    /**
     * Class implementing the {@link TranslationInterface} managed by this manager
     *
     * @var string
     */
    protected $class;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $domainHeritances = array();

    /**
     * @param string $class Class name of managed {@link TranslationInterface} objects
     * @param EventDispatcherInterface $eventDispatcher Event dispatcher used to propagate new, modified
     *                                                  and removed translations
     * @param Array $domainHeritances Witch domains must be overitten
     */
    public function __construct($class, EventDispatcherInterface $eventDispatcher, $domainHeritances)
    {
        $this->class            = $class;
        $this->eventDispatcher  = $eventDispatcher;
        $this->domainHeritances = $domainHeritances;
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslation()
    {
        /** @var TranslationInterface $translation */
        $translation = new $this->class();
        $translation->setDateCreated(new \DateTime());

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllTranslations()
    {
        return $this->findTranslationsBy(array());
    }

    /**
     * {@inheritdoc}
     */
    public function findTranslationsByLocaleAndDomain($locale, $domain = 'messages')
    {
        // Domain without heritance
        if (false == ($parentDomain = $this->getParentDomain($domain))) {
            return $this->findTranslationsBy(array(
                'transLocale'   => $locale,
                'messageDomain' => $domain,
            ));
        }

        // Domain with heritance
        $parentTranslations = $this->findTranslationsBy(array(
            'transLocale'   => $locale,
            'messageDomain' => $parentDomain,
        ));

        $childTranslations = $this->findTranslationsBy(array(
            'transLocale'   => $locale,
            'messageDomain' => $domain,
        ));

        return $this->mergeTranslationsDomains($parentTranslations, $childTranslations);
    }

    /**
     * [getParentDomain description]
     * @param  [type] $domain [description]
     * @return [type]         [description]
     */
    protected function getParentDomain($domain)
    {
        foreach($this->domainHeritances as $parent => $childs) {
            if (in_array($domain, $childs)) {
                return $parent;
            }
        }

        return false;
    }

    protected function mergeTranslationsDomains($parentTranslations, $childTranslations)
    {
        $result = [];

        // Index by transKey
        foreach($parentTranslations as $pTrans) {
            $result[$pTrans->getTransKey()] = $pTrans;
        }

        // Erase existings with child values
        foreach($childTranslations as $cTrans) {
            $result[$cTrans->getTransKey()] = $cTrans;
        }

        // Free memory
        unset($parentTranslations);
        unset($childTranslations);

        // Drop indexes
        return array_values($result);
    }
}
