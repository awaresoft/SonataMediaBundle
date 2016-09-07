<?php

namespace Awaresoft\Sonata\MediaBundle\Controller;

use Sonata\MediaBundle\Controller\MediaAdminController as BaseMediaAdminController;
use Awaresoft\Sonata\AdminBundle\Reference\Type\EntityObjectType;
use Awaresoft\Sonata\AdminBundle\Reference\Type\PageBlockType;
use Awaresoft\Sonata\AdminBundle\Traits\ControllerHelperTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        $relationTypes = [
            new PageBlockType($this->container, $object, 'sonata.media.block.media', 'mediaId'),
            new PageBlockType($this->container, $object, 'sonata.media.block.feature_media', 'mediaId'),
            new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'banner', 'admin_sonata_news_post_edit'),
            new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'image', 'admin_sonata_news_post_edit'),
        ];

        if (class_exists($class = 'Awaresoft\BannerBundle\Entity\Banner')) {
            $relationTypes[] = new EntityObjectType($this->container, $object, $class, 'media', 'admin_awaresoft_banner_banner_edit');
        }

        if (class_exists($class = 'Awaresoft\FileBundle\Entity\File')) {
            $relationTypes[] = new EntityObjectType($this->container, $object, $class, 'media', 'admin_awaresoft_file_file_edit');
            $relationTypes[] = new EntityObjectType($this->container, $object, $class, 'thumbnail', 'admin_awaresoft_file_file_edit');
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

        foreach ($idx as $id) {
            $object = $this->admin->getObject($id);
            $relationTypes = [
                new PageBlockType($this->container, $object, 'sonata.media.block.media', 'mediaId'),
                new PageBlockType($this->container, $object, 'sonata.media.block.feature_media', 'mediaId'),
                new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\MediaBundle\Entity\GalleryHasMedia', 'media', 'admin_sonata_media_galleryhasmedia_edit'),
            ];

            if (class_exists($class = 'Awaresoft\Sonata\NewsBundle\Entity\Post')) {
                $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'banner', 'admin_sonata_news_post_edit');
                $relationTypes[] = new EntityObjectType($this->container, $object, 'Awaresoft\Sonata\NewsBundle\Entity\Post', 'image', 'admin_sonata_news_post_edit');
            }
            if (class_exists($class = 'Application\BannerBundle\Entity\Banner')) {
                $relationTypes[] = new EntityObjectType($this->container, $object, $class, 'media', 'admin_awaresoft_banner_banner_edit');
            }

            if (class_exists($class = 'Application\FileBundle\Entity\File')) {
                $relationTypes[] = new EntityObjectType($this->container, $object, $class, 'media', 'admin_awaresoft_file_file_edit');
                $relationTypes[] = new EntityObjectType($this->container, $object, $class, 'thumbnail', 'admin_awaresoft_file_file_edit');
            }

            $message .= $this->checkObjectHasRelations($object, $this->admin, $relationTypes);
        }

        if (!$message) {
            return true;
        }

        return $message;
    }
}
