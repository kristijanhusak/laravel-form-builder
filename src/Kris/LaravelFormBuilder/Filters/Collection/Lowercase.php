<?php

namespace Kris\LaravelFormBuilder\Filters\Collection;

use Kris\LaravelFormBuilder\Filters\FilterInterface;

/**
 * Class Lowercase
 *
 * @package Kris\LaravelFormBuilder\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class Lowercase implements FilterInterface
{
    /**
     * Encoding for string input.
     *
     * @var string $encoding
     */
    protected $encoding = null;

    /**
     * StringToLower constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!array_key_exists('encoding', $options) && function_exists('mb_internal_encoding')) {
            $options['encoding'] = mb_internal_encoding();
        }

        if (array_key_exists('encoding', $options)) {
            $this->setEncoding($options['encoding']);
        }
    }

    /**
     * Returns current encoding.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param null $encoding
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\Lowercase
     *
     * @throws \Exception
     */
    public function setEncoding($encoding = null)
    {
        if ($encoding !== null) {
            if (!function_exists('mb_strtolower')) {
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
     * Returns the string lowercased $value.
     *
     * @param  mixed $value
     * @param  array $options
     *
     * @return mixed
     */
    public function filter($value, $options = [])
    {
        $value = (string) $value;
        if ($this->getEncoding() !== null) {
            return mb_strtolower($value, $this->getEncoding());
        }

        return strtolower($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Lowercase';
    }
}