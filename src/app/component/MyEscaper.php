<?php

namespace App\Component;

use Phalcon\Escaper;

class MyEscaper
{
    /**
     * Sanitizes the input
     *
     * @param [type] $string
     * @return santized string
     */
    public function sanitizeInput($input)
    {
        $escaper = new Escaper();
        $output= $escaper->escapeHtml($input);
        return $output;
    }
}
