<?php
/*
 * This file is part of the <package> package.
 *
 * (c) Marc Aschmann <maschmann@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asm\TranslationLoaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TranslationController
 *
 * @package TranslationLoaderBundle\Controller
 * @author Marc Aschmann <maschmann@gmail.com>
 */
class TranslationController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render(
            'AsmTranslationLoaderBundle:Translation:index.html.twig',
            array(
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $translations = $this->get('asm_translation_loader.translation_manager')
            ->findAllTranslations();

        return $this->render(
            'AsmTranslationLoaderBundle:Translation:list.html.twig',
            array(
                'translations' => $translations,
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formAction(Request $request)
    {
        return $this->render(
            'AsmTranslationLoaderBundle:Translation:form.html.twig',
            array(
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        return new JsonResponse(
            array()
        );
    }

    /**
     * @param string $transKey
     * @param string $transLocale
     * @param string $messageDomain
     * @param Request $request
     * @return JsonResponse
     */
    public function readAction($transKey, $transLocale, $messageDomain, Request $request)
    {
        return new JsonResponse(
            array()
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        return new JsonResponse(
            array()
        );
    }

    /**
     * @param string $transKey
     * @param string $transLocale
     * @param string $messageDomain
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAction($transKey, $transLocale, $messageDomain, Request $request)
    {
        return new JsonResponse(
            array()
        );
    }
}
