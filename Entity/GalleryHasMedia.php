<?php

namespace Awaresoft\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGalleryHasMedia as BaseGalleryHasMedia;

/**
 * Gallery entity class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class GalleryHasMedia extends BaseGalleryHasMedia
{

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}