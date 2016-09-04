<?php

namespace Awaresoft\Sonata\MediaBundle\Filesystem;

use Psr\Log\LoggerInterface;
use Sonata\MediaBundle\Filesystem\Replicate as BaseReplicate;
use Gaufrette\Adapter as AdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Replicate
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class Replicate extends BaseReplicate
{
    /**
     * Replicate constructor.
     *
     * @param AdapterInterface $master
     * @param AdapterInterface $slave
     * @param ContainerInterface $container
     * @param LoggerInterface|null $logger
     */
    public function __construct($master, $slave, ContainerInterface $container, LoggerInterface $logger = null)
    {
        parent::__construct($container->get($master), $container->get($slave), $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $ok = true;
        try {
            $this->slave->delete($key);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('Unable to delete %s, error: %s', $key, $e->getMessage()));
            }

            $ok = false;
        }

        if ($this->master === $this->slave || $this->strpos($key, 'thumb') !== false) {
            return $ok;
        }

        try {
            $this->master->delete($key);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('Unable to delete %s, error: %s', $key, $e->getMessage()));
            }

            $ok = false;
        }

        return $ok;
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $content, array $metadata = null)
    {
        $ok = true;
        $return = false;

        try {
            $return = $this->master->write($key, $content, $metadata);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('Unable to write %s, error: %s', $key, $e->getMessage()));
            }

            $ok = false;
        }

        if ($this->master === $this->slave || strpos($key, 'thumb') !== false) {
            return $ok;
        }

        try {
            $return = $this->slave->write($key, $content, $metadata);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('Unable to write %s, error: %s', $key, $e->getMessage()));
            }

            $ok = false;
        }

        return $ok && $return;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($key, $new)
    {
        $ok = true;

        try {
            $this->master->rename($key, $new);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('Unable to rename %s, error: %s', $key, $e->getMessage()));
            }

            $ok = false;
        }

        if ($this->master === $this->slave || strpos($key, 'thumb') !== false) {
            return $ok;
        }

        try {
            $this->slave->rename($key, $new);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('Unable to rename %s, error: %s', $key, $e->getMessage()));
            }

            $ok = false;
        }

        return $ok;
    }
}
