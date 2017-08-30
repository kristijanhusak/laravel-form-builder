<?php

namespace Kris\LaravelFormBuilder\Filters\Collection;

use Kris\LaravelFormBuilder\Filters\FilterInterface;

/**
 * Class StripTags
 *
 * @package Kris\LaravelFormBuilder\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class StripTags implements FilterInterface
{
    /**
     * Array of allowed tags and allowed attributes for each allowed tag.
     *
     * Tags are stored in the array keys, and the array values are themselves
     * arrays of the attributes allowed for the corresponding tag.
     *
     * @var array $allowedTags
     */
    protected $allowedTags = [];

    /**
     *
     * Array of allowed attributes for all allowed tags.
     *
     * Attributes stored here are allowed for all of the allowed tags.
     *
     * @var array $allowedAttributes
     */
    protected $allowedAttributes = [];

    /**
     * StripTags constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (array_key_exists('allowedTags', $options)) {
            $this->setAllowedTags($options['allowedTags']);
        }

        if (array_key_exists('allowedAttribs', $options)) {
            $this->setAllowedAttributes($options['allowedAttribs']);
        }
    }

    /**
     * Sets the allowedTags property.
     *
     * @param array|string $allowedTags
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\StripTags
     */
    public function setAllowedTags($allowedTags)
    {
        if (!is_array($allowedTags)) {
            $allowedTags = array($allowedTags);
        }

        foreach ($allowedTags as $index => $element) {

            // If the tag was provided without attributes
            if (is_int($index) && is_string($element)) {
                // Canonicalize the tag name
                $tagName = strtolower($element);
                // Store the tag as allowed with no attributes
                $this->allowedTags[$tagName] = [];
            }

            // Otherwise, if a tag was provided with attributes
            else if (is_string($index) && (is_array($element) || is_string($element))) {

                // Canonicalize the tag name
                $tagName = strtolower($index);
                // Canonicalize the attributes
                if (is_string($element)) {
                    $element = [$element];
                }

                // Store the tag as allowed with the provided attributes
                $this->allowedTags[$tagName] = [];
                foreach ($element as $attribute) {
                    if (is_string($attribute)) {
                        // Canonicalize the attribute name
                        $attributeName = strtolower($attribute);
                        $this->allowedTags[$tagName][$attributeName] = null;
                    }
                }

            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedTags()
    {
        return $this->allowedTags;
    }

    /**
     * Sets the allowedAttributes property.
     *
     * @param array|string $allowedAttribs
     *
     * @return \Kris\LaravelFormBuilder\Filters\Collection\StripTags
     */
    public function setAllowedAttributes($allowedAttribs)
    {
        if (!is_array($allowedAttribs)) {
            $allowedAttribs = [$allowedAttribs];
        }

        // Store each attribute as allowed.
        foreach ($allowedAttribs as $attribute) {
            if (is_string($attribute)) {
                // Canonicalize the attribute name.
                $attributeName = strtolower($attribute);
                $this->allowedAttributes[$attributeName] = null;
            }
        }

        return $this;
    }

    /**
     * @param  mixed $value
     * @param  array $options
     *
     * @return string
     */
    public function filter($value, $options = [])
    {
        $value = (string) $value;

        // Strip HTML comments first
        while (strpos($value, '<!--') !== false) {
            $pos   = strrpos($value, '<!--');
            $start = substr($value, 0, $pos);
            $value = substr($value, $pos);

            // If there is no comment closing tag, strip whole text.
            if (!preg_match('/--\s*>/s', $value)) {
                $value = '';
            } else {
                $value = preg_replace('/<(?:!(?:--[\s\S]*?--\s*)?(>))/s', '',  $value);
            }

            $value = $start . $value;
        }

        // Initialize accumulator for filtered data.
        $dataFiltered = '';
        // Parse the input data iteratively as regular pre-tag text followed by a
        // tag; either may be empty strings.
        preg_match_all('/([^<]*)(<?[^>]*>?)/', $value, $matches);

        // Iterate over each set of matches
        foreach ($matches[1] as $index => $preTag) {
            // If the pre-tag text is non-empty, strip any ">" characters from it.
            if (strlen($preTag)) {
                $preTag = str_replace('>', '', $preTag);
            }
            // If a tag exists in this match, then filter the tag.
            $tag = $matches[2][$index];
            if (strlen($tag)) {
                $tagFiltered = $this->filterCertainTag($tag);
            } else {
                $tagFiltered = '';
            }
            // Add the filtered pre-tag text and filtered tag to the data buffer.
            $dataFiltered .= $preTag . $tagFiltered;
        }

        // Return the filtered data.
        return $dataFiltered;
    }

    /**
     * Filters a single tag against the current property data.
     *
     * @param  string $tag
     *
     * @return string
     */
    public function filterCertainTag($tag)
    {
        // Parse the tag into:
        // 1. a starting delimiter (mandatory)
        // 2. a tag name (if available)
        // 3. a string of attributes (if available)
        // 4. an ending delimiter (if available)
        $isMatch = preg_match('~(</?)(\w*)((/(?!>)|[^/>])*)(/?>)~', $tag, $matches);

        // If the tag does not match, then strip the tag entirely
        if (!$isMatch) {
            return '';
        }

        // Save the matches to more meaningfully named variables
        $tagStart      = $matches[1];
        $tagName       = strtolower($matches[2]);
        $tagAttributes = $matches[3];
        $tagEnd        = $matches[5];

        // If the tag is not an allowed tag, then remove the tag entirely
        if (!isset($this->allowedTags[$tagName])) {
            return '';
        }

        // Trim the attribute string of whitespace at the ends
        $tagAttributes = trim($tagAttributes);

        // If there are non-whitespace characters in the attribute string
        if (strlen($tagAttributes)) {
            // Parse iteratively for well-formed attributes
            preg_match_all('/([\w-]+)\s*=\s*(?:(")(.*?)"|(\')(.*?)\')/s', $tagAttributes, $matches);

            // Initialize valid attribute accumulator
            $tagAttributes = '';

            // Iterate over each matched attribute
            foreach ($matches[1] as $index => $attributeName) {
                $attributeName      = strtolower($attributeName);
                $attributeDelimiter = empty($matches[2][$index]) ? $matches[4][$index] : $matches[2][$index];
                $attributeValue     = empty($matches[3][$index]) ? $matches[5][$index] : $matches[3][$index];

                // If the attribute is not allowed, then remove it entirely
                if (!array_key_exists($attributeName, $this->allowedTags[$tagName])
                    && !array_key_exists($attributeName, $this->allowedAttributes)) {
                    continue;
                }
                // Add the attribute to the accumulator
                $tagAttributes .= " $attributeName=" . $attributeDelimiter
                    . $attributeValue . $attributeDelimiter;
            }
        }

        // Reconstruct tags ending with "/>" as backwards-compatible XHTML tag
        if (strpos($tagEnd, '/') !== false) {
            $tagEnd = " $tagEnd";
        }

        // Return the filtered tag
        return $tagStart . $tagName . $tagAttributes . $tagEnd;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return 'StripTags';
    }
}