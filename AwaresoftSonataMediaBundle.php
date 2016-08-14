<?php

namespace Awaresoft\Sonata\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * AwaresoftSonataMediaBundle class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class AwaresoftSonataMediaBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataMediaBundle';
    }
}