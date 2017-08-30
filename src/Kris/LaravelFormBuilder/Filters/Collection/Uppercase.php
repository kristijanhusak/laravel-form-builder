<?php

namespace Kris\LaravelFormBuilder\Filters\Collection;

use Kris\LaravelFormBuilder\Filters\FilterInterface;

/**
 * Class Uppercase
 *
 * @package Kris\LaravelFormBuilder\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class Uppercase implements FilterInterface
{
    /**
     * @var string $encoding
     */
    protected $encoding = null;

    /**
     * StringToUpper constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!array_key_exists('encoding', $options) && function_exists('mb_internal_encoding')) {
            $options['encoding'] = mb_internal_encoding();
        }

        if (array_key_exists('encoding', $options)) {
            $this->setEncoding($options['encoding']);
        }
    }

    /**
     * @param null $encoding
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\Uppercase
     *
     * @throws \Exception
     */
    public function setEncoding($encoding)
    {
        if ($encoding !== null) {
            if (!function_exists('mb_strtoupper')) {
                $ex = new \Exception('mbstring extension is required for value mutating.');
                throw $ex;
            }

            $encoding = (string) $encoding;
            if (!in_array(strtolower($encoding), array_map('strtolower', mb_list_encodings()))) {
                $ex = new \Exception('The given encoding '.$encoding.' is not supported by mbstring ext.');
                throw $ex;
            }
        }

        $this->encoding = $encoding;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param  mixed  $value
     * @param  array  $options
     *
     * @return string
     */
    public function filter($value, $options = [])
    {
        $value = (string) $value;
        if ($this->getEncoding()) {
            return mb_strtoupper($value, $this->getEncoding());
        }

        return strtoupper($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Uppercase';
    }
}