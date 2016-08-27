<?php

namespace Awaresoft\Sonata\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;

/**
 * Media entity entity
 *
 * @ORM\Entity
 * @ORM\Table(name="media__media")
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class Media extends BaseMedia
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
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