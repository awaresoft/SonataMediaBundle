<?php

namespace Awaresoft\Sonata\MediaBundle\Controller;

use Sonata\MediaBundle\Controller\MediaAdminController as BaseMediaAdminController;
use Awaresoft\Sonata\AdminBundle\Reference\Type\EntityObjectType;
use Awaresoft\Sonata\AdminBundle\Reference\Type\PageBlockType;
use Awaresoft\Sonata\AdminBundle\Traits\ControllerHelperTrait;

/**
 * Class MediaCRUDController
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class MediaCRUDController extends BaseMediaAdminController
{
    use ControllerHelperTrait;

    /**
     * @inheritdoc
     */
    public function preDeleteAction($object)
    {
        $bundles = $this->get("kernel")->getBundles();

        $relationTypes = [
            new PageBlockType($this->container, $object, 'sonata.media.block.media', 'mediaId'),
            new PageBlockType($this->container, $object, 'sonata.media.block.feature_media', 'mediaId'),
        ];

        if (array_key_exists('AwaresoftSonataNewsBundle', $bundles)) {
            $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'banner', 'admin_sonata_news_post_edit');
            $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'image', 'admin_sonata_news_post_edit');
        }

        if (array_key_exists('AwaresoftBannerBundle', $bundles)) {
            $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\BannerBundle\Entity\Banner', 'media', 'admin_awaresoft_banner_banner_edit');
        }

        if (array_key_exists('AwaresoftFileBundle', $bundles)) {
            $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\FileBundle\Entity\File', 'media', 'admin_awaresoft_file_file_edit');
            $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\FileBundle\Entity\File', 'thumbnail', 'admin_awaresoft_file_file_edit');
        }

        $message = $this->checkObjectHasRelations($object, $this->admin, $relationTypes);

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
            $relationTypes = [
                new PageBlockType($this->container, $object, 'sonata.media.block.media', 'mediaId'),
                new PageBlockType($this->container, $object, 'sonata.media.block.feature_media', 'mediaId'),
                new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\MediaBundle\Entity\GalleryHasMedia', 'media', 'admin_sonata_media_galleryhasmedia_edit'),
            ];

            if (array_key_exists('AwaresoftSonataNewsBundle', $bundles)) {
                $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'banner', 'admin_sonata_news_post_edit');
                $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'image', 'admin_sonata_news_post_edit');
            }

            if (array_key_exists('AwaresoftBannerBundle', $bundles)) {
                $relationTypes[] = new EntityObjectType($this->container, $object, 'Application\BannerBundle\Entity\Banner', 'media', 'admin_awaresoft_banner_banner_edit');
            }

            if (array_key_exists('AwaresoftFileBundle', $bundles)) {
                $relationTypes[] = new EntityObjectType($this->container, $object, 'Application\FileBundle\Entity\File', 'media', 'admin_awaresoft_file_file_edit');
                $relationTypes[] = new EntityObjectType($this->container, $object, 'Application\FileBundle\Entity\File', 'thumbnail', 'admin_awaresoft_file_file_edit');
            }

            $message .= $this->checkObjectHasRelations($object, $this->admin, $relationTypes);
        }

        if (!$message) {
            return true;
        }

        return $message;
    }
}
