<?php

namespace Awaresoft\Sonata\MediaBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class MediaAdminExtension
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class MediaAdminExtension extends AbstractAdminExtension
{
    /**
     * Extension of parent method
     *
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->remove('authorName');
        $formMapper->remove('copyright');
        $formMapper->remove('cdnIsFlushable');
    }
}
