<?php

namespace Kris\LaravelFormBuilder;

class Rules
{

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
