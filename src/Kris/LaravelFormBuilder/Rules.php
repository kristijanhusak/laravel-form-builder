<?php

namespace Kris\LaravelFormBuilder;

class Rules
{

    /**
     * @var string|null
     */
    protected $fieldName;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $messages;

    /**
     * @param array $rules
     * @param array $attributes
     * @param array $messages
     */
    public function __construct(array $rules, array $attributes = [], array $messages = []) {
        $this->rules = $rules;
        $this->attributes = $attributes;
        $this->messages = $messages;
    }

    /**
     * @param string $name
     */
    public function setFieldName($name) {
        $this->fieldName = $name;

        return $this;
    }

    /**
     * @param string $fieldName
     */
    public function getFieldRules($fieldName = null) {
      $fieldName = $this->ensureFieldName($fieldName);

      $rules = $this->rules;
      return isset($rules[$fieldName]) ? $rules[$fieldName] : [];
    }

    /**
     * @param mixes $rule
     * @param string $fieldName
     */
    public function addFieldRule($rule, $fieldName = null) {
      $rules = $this->getFieldRules($fieldName);
      $rules[] = $rule;
      $this->setFieldRules($rules, $fieldName);
    }

    /**
     * @param array $rules
     * @param string $fieldName
     */
    public function setFieldRules(array $rules, $fieldName = null) {
      $fieldName = $this->ensureFieldName($fieldName);
      $this->rules[$fieldName] = $rules;
    }

    /**
     * @param string $fieldName
     */
    protected function ensureFieldName($fieldName) {
      if (!$fieldName) {
        if (!$this->fieldName) {
          throw new InvalidArgumentException("Field functions on non-field Rules need explicit field name");
        }

        $fieldName = $this->fieldName;
      }

      return $fieldName;
    }

    /**
     * @param array $rules
     * @param array $attributes
     * @param array $messages
     */
    public function append($rules) {
      if (is_array($rules)) {
        $rules = static::fromArray($rules);
      }

      $this->rules = array_replace_recursive($this->rules, $rules->getRules());
      $this->attributes = array_replace_recursive($this->attributes, $rules->getAttributes());
      $this->messages = array_replace_recursive($this->messages, $rules->getMessages());

      return $this;
    }

    /**
     * @return array
     */
    public function getRules() {
      return $this->rules;
    }

    /**
     * @return array
     */
    public function getAttributes() {
      return $this->attributes;
    }

    /**
     * @return array
     */
    public function getMessages() {
      return $this->messages;
    }

    /**
     * @param array[] $rules
     * @return static
     */
    static public function fromArray($rules) {
        if (!$rules) {
            return new static([]);
        }

        $rules += [
            'rules' => [],
            'attributes' => [],
            'error_messages' => [],
        ];

        return new static($rules['rules'], $rules['attributes'], $rules['error_messages']);
    }

}
