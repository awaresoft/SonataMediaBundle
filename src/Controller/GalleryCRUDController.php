<?php

namespace Awaresoft\Sonata\MediaBundle\Controller;

use Sonata\MediaBundle\Controller\GalleryAdminController as BaseGalleryAdminController;
use Awaresoft\Sonata\AdminBundle\Reference\Type\EntityObjectType;
use Awaresoft\Sonata\AdminBundle\Reference\Type\PageBlockType;
use Awaresoft\Sonata\AdminBundle\Traits\ControllerHelperTrait;

/**
 * Class GalleryCRUDController
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class GalleryCRUDController extends BaseGalleryAdminController
{
    use ControllerHelperTrait;

    /**
     * @inheritdoc
     */
    public function preDeleteAction($object)
    {
        $message = null;
        $bundles = $this->get("kernel")->getBundles();

        if (array_key_exists('AwaresoftSonataNewsBundle', $bundles)) {
            $message = $this->checkObjectHasRelations($object, $this->admin, [
                new PageBlockType($this->container, $object, 'sonata.media.block.gallery', 'galleryId'),
                new EntityObjectType(
                    $this->container,
                    $object,
                    '$class = \'Awaresoft\Sonata\NewsBundle\Entity\Post',
                    'gallery',
                    'admin_sonata_news_post_edit'
                ),
            ]);
        }

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function batchActionDeleteIsRelevant(array $idx)
    {
        $message = null;
        $bundles = $this->get("kernel")->getBundles();

        foreach ($idx as $id) {
            $object = $this->admin->getObject($id);

            if (array_key_exists('AwaresoftSonataNewsBundle', $bundles)) {
                $message .= $this->checkObjectHasRelations($object, $this->admin, [
                    new PageBlockType($this->container, $object, 'sonata.media.block.gallery', 'galleryId'),
                    new EntityObjectType(
                        $this->container,
                        $object,
                        'Awaresoft\Sonata\NewsBundle\Entity\Post',
                        'gallery',
                        'admin_sonata_news_post_edit'
                    ),
                ]);
            }
        }

        if (!$message) {
            return true;
        }

        return $message;
    }
}
