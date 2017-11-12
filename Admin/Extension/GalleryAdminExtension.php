<?php

namespace Awaresoft\Sonata\MediaBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class GalleryAdminExtension
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class GalleryAdminExtension extends AbstractAdminExtension
{
    /**
     * Extension of parent method
     *
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->with($formMapper->getAdmin()->trans('Options'), array('class' => 'col-md-6 pull-left'))->end()
            ->with($formMapper->getAdmin()->trans('Gallery'), array('class' => 'col-md-6 pull-right'))->end()
        ;
    }
}
