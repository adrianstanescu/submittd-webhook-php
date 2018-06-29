<?php
namespace Submittd\Webhook;

class Field {
    /**
     * @var string
     */
    public $name;
    /**
     * @var string | null
     */
    public $label = null;
    /**
     * @var mixed
     */
    public $value;

    public function __construct($name, $value, $label = null) {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
    }

    public static function fromRaw($data) {
        $field = new Field(
            isset($data->name) ? $data->name : null,
            isset($data->value) ? $data->value : null,
            isset($data->label) ? $data->label : null
        );
        return $field;
    }
}