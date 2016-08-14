<?php

namespace Awaresoft\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;

/**
 * Gallery entity class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class Media extends BaseMedia
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