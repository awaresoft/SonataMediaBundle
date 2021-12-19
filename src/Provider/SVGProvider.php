<?php

namespace Awaresoft\Sonata\MediaBundle\Provider;

use Sonata\MediaBundle\Provider\FileProvider;
use Gaufrette\Filesystem;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class SVGProvider
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class SVGProvider extends FileProvider
{
    protected $allowedMimeTypes;
    protected $allowedExtensions;
    protected $metadata;

    public function addTemplate($key, $value)
    {
        $this->templates[$key] = $value;
    }

    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = array(), array $allowedMimeTypes = array(), MetadataBuilderInterface $metadata = null)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail);

        $this->allowedExtensions = $allowedExtensions;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->metadata = $metadata;
    }

    public function buildCreateForm(FormMapper $formMapper)
    {
        $formMapper->add('binaryContent', 'file', array(
            'label' => 'Upload SVG file',
            'constraints' => array(
                new NotBlank(),
                new NotNull(),
            ),
        ));
    }
}
