<?php

namespace Asm\TranslationLoaderBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;

use Asm\TranslationLoaderBundle\Form\Type\TranslationType;
use Asm\TranslationLoaderBundle\Model\TranslationManagerInterface;

class TranslationAdmin extends Admin
{
    /**
     * [$translationManager description]
     * @var TranslationManagerInterface
     */
    protected $translationManager;


    protected $datagridValues = array(

        // reverse order (default = 'ASC')
        '_sort_order' => 'DESC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'createdAt',

    );

    public function setTranslationManager(TranslationManagerInterface $translationManager)
    {
        $this->translationManager = $translationManager;
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        $query->where(
            $query->getQueryBuilder()->getRootAlias() . ".transKey = :impossiblevalue"
        )->setParameter('impossiblevalue', -1);

        return $query;
    }

    public function getNormalizedIdentifier($entity)
    {
        return $entity->getTransKey();
    }

    protected function getEntityRepository()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')->getRepository($this->getClass());
    }

    protected function getAllMessageDomains()
    {
        return $this->getConfigurationPool()->getContainer()->getParameter('asm_translation_loader.domains');
    }

    protected function getAllTransLocales()
    {
        return $this->getConfigurationPool()->getContainer()->getParameter('asm_translation_loader.locales');
    }

    public function findTranslationForKeyDomainLocale($key, $domain, $locale)
    {
        $existing = $this->translationManager->findTranslationBy([
            'messageDomain' => $domain,
            'transLocale' => $locale,
            'transKey' => $key
        ]);

        return $existing;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);
        $collection->add('form', 'form');
        $collection->add('createTranslation', 'createTranslation');
        $collection->add('updateTranslation', 'updateTranslation');
        $collection->add('deleteTranslation', 'deleteTranslation');
    }

    public function getInlineForm($translation)
    {

        $options = [
            'action' => $this->generateObjectUrl('edit', $translation)
        ];
        $form = $this->getConfigurationPool()->getContainer()->get('form.factory')->create(TranslationType::class, $translation, $options);

        return $form->createView();
    }
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('transKey')
            ->add('translation')
            ->add('transLocale', null, [], ChoiceType::class, [
                'choices' => array_flip($this->getAllTransLocales()),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                }
            ])
            ->add('messageDomain', null, [], ChoiceType::class, [
                'choices' => $this->getAllMessageDomains(),
            ])
        ;
    }


    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('transKey')
            ->add('transLocale')
            ->add('messageDomain')
            ->add('translation')
        ;

        // foreach($this->getAllMessageDomains() as $messageDomain) {

        //     $listMapper->add("messagedomain_" . strtolower($messageDomain), 'text', [
        //         'label' => $messageDomain,
        //         'template' => 'AsmTranslationLoaderBundle:Admin:list_messagedomain.html.twig',
        //         'filters' => [
        //             'messageDomain' => $messageDomain,
        //         ]
        //     ]);
        // }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('translation')
            ->add('transKey')
            ->add('transLocale')
            ->add('messageDomain')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('translation')
            ->add('dateCreated')
            ->add('dateUpdated')
            ->add('transKey')
            ->add('transLocale')
            ->add('messageDomain')
        ;
    }
}
