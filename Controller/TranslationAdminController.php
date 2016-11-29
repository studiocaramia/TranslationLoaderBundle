<?php

namespace Asm\TranslationLoaderBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\Datagrid;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;

use Asm\TranslationLoaderBundle\Entity\Translation;
use Asm\TranslationLoaderBundle\DTO\TranslationAdmin;
use Asm\TranslationLoaderBundle\Model\TranslationManagerInterface;


use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class TranslationAdminController extends CRUDController
{
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $tranlations = $this->getDoctrine()->getRepository(Translation::class)->getTranslationListIndexed('fr');

        $fields = [
            'transKey',
            'transLocale',
        ];

        $domains = ['General', 'Front', 'Mobile'];

        foreach($domains as $domain) {
            $fields[] = $domain;
        }

        $form = $this->createForm('form');


        return $this->render($this->admin->getTemplate('list'), array(
            'admin'     => $this->admin,
            'action'     => 'list',
            'list'       => $tranlations,
            'fields'        => $fields,
            'domains'        => $domains,
            'form'       => $form->createView(),
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ));
    }

    public function formAction(Request $request)
    {
        return new Response("dd");
        $translation = new Translation();
        $translation
            ->setTransKey($request->get('transKey'))
            ->setTransLocale($request->get('transLocale'))
            ->setMessageDomain($request->get('messageDomain'))
            ->setTranslation($request->get('translation'))
        ;

        $form = $this->createForm('asm_translation', $translation, array());

        return new Response(
            $this->get('templating')->render(
                'AsmTranslationLoaderBundle:Translation:form.html.twig',
                array(
                    'form' => $form->createView(),
                )
            )
        );
    }

    public function createTranslationAction(Request $request)
    {
        return $this->handleForm('create', $request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateTranslationAction(Request $request)
    {
        return $this->handleForm('update', $request);
    }

    /**
     * Weird name to prevent heritance error
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteTranslationAction(Request $request)
    {
        $manager = $this->get('asm_translation_loader.translation_manager');
        $translation = $manager->findTranslationBy(
            array(
                'transKey' => $request->get('key'),
                'transLocale' => $request->get('locale'),
                'messageDomain' => $request->get('domain'),
            )
        );

        if (!empty($translation)) {
            $manager->removeTranslation($translation);
            $this->addFlash('sonata_flash_success', 'Traduction supprimée');
        } else {
            $this->addFlash('sonata_flash_success', 'Traduction introuvable');
        }

        return $this->redirect($this->admin->generateUrl('list'));
    }

    /**
     * @param string $type
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function handleForm($type, $request)
    {
        $error = null;
        $form = $this->createForm('asm_translation', new Translation(), array(
            'csrf_protection' => false
        ));
        $form->submit($request->get('asm_translation'));

        if ($form->isValid()) {
            $manager = $this->get('asm_translation_loader.translation_manager');

            if ('update' == $type) {
                /** @var \Asm\TranslationLoaderBundle\Entity\Translation $update */
                $update = $form->getData();
                // get translation from database again to keep date_created
                $translation = $manager->findTranslationBy(
                    array(
                        'transKey' => $update->getTransKey(),
                        'transLocale' => $update->getTransLocale(),
                        'messageDomain' => $update->getMessageDomain(),
                    )
                );

                $translation
                    ->setTransKey($update->getTransKey())
                    ->setTransLocale($update->getTransLocale())
                    ->setMessageDomain($update->getMessageDomain())
                    ->setTranslation((String) $update->getTranslation());

                $manager->updateTranslation($translation);

                $this->addFlash('sonata_flash_success', 'Traduction éditée');
            } else {
                $translation = $form->getData();
                $translation->setDateCreated(new \DateTime());
                try {
                    $manager->updateTranslation($translation);
                    $error = '';

                    $this->addFlash('sonata_flash_success', 'Traduction crée');
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    $this->addFlash('sonata_flash_error', 'Erreur ' . $e->getMessage());
                }
            }

            
        } else {
            $this->addFlash('sonata_flash_error', 'Erreur ' . $form->getErrorsAsString());
        }
            return $this->redirect($this->admin->generateUrl('list'));

        return $response;
    }
}
