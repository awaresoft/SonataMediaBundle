<?php

namespace Awaresoft\Sonata\MediaBundle\Resizer;

use Gaufrette\File;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\ResizerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class ImageResizer implements ResizerInterface
{
    /**
     * ImagineInterface.
     */
    protected $adapter;

    /**
     * string.
     */
    protected $mode;

    /**
     * @var MetadataBuilderInterface
     */
    protected $metadata;

    /**
     * @param ContainerInterface $container
     * @param string $mode
     * @param MetadataBuilderInterface $metadata
     */
    public function __construct(ContainerInterface $container, $mode, MetadataBuilderInterface $metadata)
    {
        $this->adapter = $container->get($container->getParameter('image.engine.adapter'));
        $this->mode = $mode;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function resize(MediaInterface $media, File $in, File $out, $format, array $settings)
    {
        $image = $this->adapter->load($in->getContent());
        $size = $media->getBox();

        if (isset($settings['mode'])) {
            $this->mode == $settings['mode'];
        }

        if (!$settings['height'] && !$settings['width']) {
            throw new \RuntimeException(sprintf(
                'Width parameter or height is missing in context "%s" for provider "%s"',
                $media->getContext(),
                $media->getProviderName()
            ));
        }

        if ($settings['height'] != false && $settings['width'] != false) {
            if ($settings['height'] == $settings['width']) {
                $higher = $size->getWidth();
                $lower = $size->getHeight();
                $crop = $higher - $lower;

                if ($crop > 0) {
                    $point = $higher == $size->getHeight() ? new Point(0, 0) : new Point($crop / 2, 0);
                    $image->crop($point, new Box($lower, $lower));
                    $size = $image->getSize();
                } else {
                    $point = $lower == $size->getWidth() ? new Point(0, 0) : new Point(0, abs($crop) / 2);
                    $image->crop($point, new Box($higher, $higher));
                    $size = $image->getSize();
                }

                $settings['height'] = (int)($settings['width'] * $size->getHeight() / $size->getWidth());

                if ($settings['height'] < $size->getHeight() && $settings['width'] < $size->getWidth()) {
                    $content = $image->thumbnail(
                        new Box(
                            $settings['width'],
                            $settings['height']
                        ),
                        ImageInterface::THUMBNAIL_OUTBOUND
                    )->get($format, ['quality' => $settings['quality']]);
                } else {
                    $content = $image->get($format, ['quality' => $settings['quality']]);
                }

                $out->setContent($content, $this->metadata->get($media, $out->getName()));

                return;
            }
        }

        $content = $image->thumbnail($this->getBoxSimple($media, $settings), $this->mode)
            ->get($format, ['quality' => $settings['quality']]);

        $out->setContent($content, $this->metadata->get($media, $out->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getBox(MediaInterface $media, array $settings)
    {
        $size = $media->getBox();

        if (null != $settings['height']) {
            if ($size->getHeight() > $size->getWidth()) {
                $higher = $size->getHeight();
                $lower = $size->getWidth();
            } else {
                $higher = $size->getWidth();
                $lower = $size->getHeight();
            }

            if ($higher - $lower > 0) {
                return new Box($lower, $lower);
            }
        }

        $settings['height'] = (int)($settings['width'] * $size->getHeight() / $size->getWidth());

        if ($settings['height'] < $size->getHeight() && $settings['width'] < $size->getWidth()) {
            return new Box($settings['width'], $settings['height']);
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoxSimple(MediaInterface $media, array $settings)
    {
        $size = $media->getBox();

        if ($settings['width'] == null && $settings['height'] == null) {
            throw new \RuntimeException(
                sprintf(
                    'Width/Height parameter is missing in context "%s" for provider "%s". Please add at least one parameter.',
                    $media->getContext(),
                    $media->getProviderName()
                )
            );
        }

        if ($settings['height'] == null) {
            $settings['height'] = (int)($settings['width'] * $size->getHeight() / $size->getWidth());
        }

        if ($settings['width'] == null) {
            $settings['width'] = (int)($settings['height'] * $size->getWidth() / $size->getHeight());
        }

        return $this->computeBox($media, $settings);
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param MediaInterface $media
     * @param array $settings
     *
     * @return Box
     */
    private function computeBox(MediaInterface $media, array $settings)
    {
        if ($this->mode !== ImageInterface::THUMBNAIL_INSET && $this->mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new \InvalidArgumentException('Invalid mode specified');
        }

        $size = $media->getBox();

        $ratios = [
            $settings['width'] / $size->getWidth(),
            $settings['height'] / $size->getHeight(),
        ];

        if ($this->mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        return $size->scale($ratio);
    }
}
