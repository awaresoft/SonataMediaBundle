<?php

namespace Awaresoft\Sonata\MediaBundle\Command;

use Doctrine\ORM\EntityRepository;
use Sonata\MediaBundle\Command\BaseCommand;
use Sonata\MediaBundle\Model\Media;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate thumbnails
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class SyncThumbsCommand extends BaseCommand
{
    /**
     * @var bool
     */
    protected $quiet = false;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('awaresoft:media:sync-thumbnails')
            ->setDescription('Sync uploaded image thumbs with new media formats')
            ->setDefinition([
                    new InputArgument('date', InputArgument::REQUIRED, 'Date from which media will be read'),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var EntityRepository $repo
         * @var Media[] $medias
         */

        $this->quiet = false;
        $this->output = $output;

        $repo = $this->getContainer()->get('doctrine')->getRepository('AwaresoftSonataMediaBundle:Media');

        $medias = $repo->createQueryBuilder('m')
            ->where('m.createdAt >= :createdAt')
            ->setParameter('createdAt', $input->getArgument('date'))
            ->getQuery()
            ->getResult();

        $this->log('Count: ' . count($medias));

        foreach ($medias as $media) {
            $provider = $this->getMediaPool()->getProvider($media->getProviderName());

            if (!$this->processMedia($media, $provider)) {
                continue;
            }
        }

        $this->getMediaManager()->getEntityManager()->clear();
    }

    /**
     * @param MediaInterface $media
     * @param MediaProviderInterface $provider
     *
     * @return bool
     */
    protected function processMedia($media, $provider)
    {
        $this->log('Generating thumbs for ' . $media->getName() . ' - ' . $media->getId());

        try {
            $provider->generateThumbnails($media);
        } catch (\Exception $e) {
            $this->log(sprintf('<error>Unable to generate new thumbnails, media: %s - %s </error>',
                $media->getId(), $e->getMessage()));

            return false;
        }

        return true;
    }

    /**
     * Write a message to the output.
     *
     * @param string $message
     */
    protected function log($message)
    {
        if (false === $this->quiet) {
            $this->output->writeln($message);
        }
    }
}
