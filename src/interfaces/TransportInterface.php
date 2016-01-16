<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Valentin Konusov <rlng-krsk@yandex.ru>
 */

namespace yarcode\email\interfaces;

interface TransportInterface
{
    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $text
     * @param array $files
     * @param null $bcc
     * @return mixed
     */
    public function send($from, $to, $subject, $text, $files = [], $bcc = null);
}