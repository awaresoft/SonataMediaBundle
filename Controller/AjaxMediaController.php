<?php

namespace Awaresoft\Sonata\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Awaresoft\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AjaxMediaController
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class AjaxMediaController extends Controller
{
    /**
     * @param Request $request
     * @param Media $media
     * @param string $format
     *
     * @return JsonResponse
     */
    public function mediaUrlAction(Request $request, Media $media, $format = 'reference')
    {
        $translate = $this->get('translator');

        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([
                'type' => 'error',
                'message' => $translate->trans('bad_request'),
            ], Response::HTTP_BAD_REQUEST);
        }

        $mediaService = $this->get('sonata.media.pool');
        $provider = $mediaService->getProvider($media->getProviderName());
        $format = $provider->getFormatName($media, $format);
        $options = $provider->getHelperProperties($media, $format);

        return new JsonResponse([
            'type' => 'success',
            'url' => $options['src']
        ]);
    }
}
