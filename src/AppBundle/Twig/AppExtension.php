<?php

namespace AppBundle\Twig;

use Symfony\Component\Translation\Translator;
use AppBundle\Utility;

class AppExtension extends \Twig_Extension
{

    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('date_interval',
                array($this, 'dateInterval')),
        );
    }

    public function dateInterval($seconds)
    {
        return Utility::formatDateInterval($seconds, $this->translator);
    }

    public function getName()
    {
        return 'app_extension';
    }

}
