<?php

namespace Awaresoft\Sonata\MediaBundle\DataFixtures\ORM;

use Awaresoft\DoctrineBundle\DataFixtures\AbstractFixture;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;

/**
 * Class LoadMediaData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadMediaDevData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public static function getGroups(): array
    {
        return ['dev'];
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 9;
    }

    public function load(ObjectManager $manager)
    {
        $gallery = $this->getGalleryManager()->create();

        $manager = $this->getMediaManager();
        $faker = $this->getFakerGenerator();

        $canada = Finder::create()->name('IMG_3587*.jpg')->in(__DIR__ . '/../data/files/gilles-canada');
        $paris = Finder::create()->name('IMG_3008*.jpg')->in(__DIR__ . '/../data/files/hugo-paris');
        $switzerland = Finder::create()->name('switzerland_2012-05-19_006.jpg')->in(__DIR__ . '/../data/files/sylvain-switzerland');

        $i = 0;
        foreach ($canada as $file) {
            $media = $manager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setName('Canada');
            $media->setDescription('Canada');
            $media->setAuthorName('Gilles Rosenbaum');
            $media->setCopyright('CC BY-NC-SA 4.0');

            $this->addReference('sonata-media-' . ($i++), $media);

            $manager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMedia($gallery, $media);
        }

        foreach ($paris as $file) {
            $media = $manager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setName('Paris');
            $media->setDescription('Paris');
            $media->setAuthorName('Hugo Briand');
            $media->setCopyright("CC BY-NC-SA 4.0");

            $this->addReference('sonata-media-' . ($i++), $media);

            $manager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMedia($gallery, $media);
        }

        foreach ($switzerland as $file) {
            $media = $manager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            $media->setName('Switzerland');
            $media->setDescription('Switzerland');
            $media->setAuthorName('Sylvain Deloux');
            $media->setCopyright('CC BY-NC-SA 4.0');

            $this->addReference('sonata-media-' . ($i++), $media);

            $manager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMedia($gallery, $media);
        }

        $gallery->setEnabled(true);
        $gallery->setName($faker->sentence(4));
        $gallery->setDefaultFormat('small');
        $gallery->setContext('default');

        $this->getGalleryManager()->update($gallery);

        $this->addReference('media-gallery', $gallery);
    }

    /**
     * @param \Sonata\MediaBundle\Model\GalleryInterface $gallery
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @return void
     */
    public function addMedia(GalleryInterface $gallery, MediaInterface $media)
    {
        $galleryHasMedia = new \Awaresoft\Sonata\MediaBundle\Entity\GalleryHasMedia();
        $galleryHasMedia->setMedia($media);
        $galleryHasMedia->setPosition(count($gallery->getGalleryHasMedias()) + 1);
        $galleryHasMedia->setEnabled(true);

        $gallery->addGalleryHasMedias($galleryHasMedia);
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getGalleryManager()
    {
        return $this->container->get('sonata.media.manager.gallery');
    }
}
