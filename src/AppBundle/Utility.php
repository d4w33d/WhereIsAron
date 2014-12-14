<?php

namespace AppBundle;

use Symfony\Component\Translation\Translator;

class Utility
{

    public static function formatDateInterval($seconds, $translator)
    {
        $s = $seconds % 60;
        $i = floor(($seconds % 3600) / 60);
        $h = floor(($seconds % (3600 * 24)) / 3600);
        $d = floor($seconds / (3600 * 24));

        $format = trim(''
            . ($d > 0 ? ($d . ' ' . ($d > 1 ? $translator->trans('jours')
                : $translator->trans('jour'))) : '') . ' '
            . ($h > 0 ? ($h . ' ' . ($h > 1 ? $translator->trans('heures')
                : $translator->trans('heure'))) : '') . ' '
            . ($i > 0 ? ($i . ' ' . ($i > 1 ? $translator->trans('minutes')
                : $translator->trans('minute'))) : '') . ' '
            . ($s > 0 ? ($s . ' ' . ($s > 1 ? $translator->trans('secondes')
                : $translator->trans('seconde'))) : ''));

        return $format ?: ('0 ' . $translator->trans('secondes'));
    }

    public static function isEmailAddress($str) {
        return preg_match('/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i', $str);
    }

    public static function isPhoneNumber($str) {
        return preg_match('/^\+(?:[0-9] ?){6,14}[0-9]$/', $str);
    }

}
