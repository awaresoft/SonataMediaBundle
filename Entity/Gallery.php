<?php

namespace Awaresoft\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;

/**
 * Gallery entity class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class Gallery extends BaseGallery
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