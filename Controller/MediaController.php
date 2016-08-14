<?php

namespace Awaresoft\Sonata\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\MediaBundle\Controller\MediaController as BaseMediacontroller;
use Awaresoft\Sonata\MediaBundle\Entity\Media;

/**
 * Class MediaController
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class MediaController extends BaseMediacontroller
{
    /**
     * @param Request $request
     * @param Media $media
     * @param string $format
     *
     * @return Response
     */
    public function showAction(Request $request, Media $media, $format = 'reference')
    {
        if (!$this->get('sonata.media.pool')->getDownloadStrategy($media)->isGranted($media, $request)) {
            throw new AccessDeniedException();
        }

        $headers = ['Content-Disposition' => ResponseHeaderBag::DISPOSITION_INLINE . '; filename="' . $media->getMetadataValue('filename') . '"'];
        $response = $this->getProvider($media)->getDownloadResponse($media, $format, $this->get('sonata.media.pool')->getDownloadMode($media), $headers);

        if ($response instanceof BinaryFileResponse) {
            $response->prepare($request);
        }

        return $response;
    }
}
