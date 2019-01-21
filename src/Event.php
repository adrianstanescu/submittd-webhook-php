<?php
namespace Submittd\Webhook;

class Event {
    const TYPE_SUCCESS = 'submissionSuccess';

    /**
     * @var string
     */
    public $type = Event::TYPE_SUCCESS;
    /**
     * @var int | null
     * Unix timestamp in seconds
     */
    public $timestamp = null;
    /**
     * @var string | null
     */
    public $remoteIP = null;
    /**
     * @var string | null
     */
    public $origin = null;
    /**
     * @var string | null
     */
    public $referer = null;
    /**
     * @var array
     */
    public $utm = [
        'source' => '',
        'medium' => '',
        'campaign' => '',
        'term' => '',
        'content' => ''
    ];
    /**
     * @var Field[]
     */
    public $fields = [];
    /**
     * @var mixed
     */
    public $context = null;

    /**
     * @return Event
     */
    static function fromRequest() {
        $event = new Event();
        $event->timestamp = time();
        $event->remoteIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $event->origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
        $event->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        return $event;
    }

    /**
     * @param object $data
     * @return Event
     */
    static function fromRaw($data) {
        $event = new Event();
        if (isset($data->type)) {
            $event->type = $data->type;
        }
        if (isset($data->timestamp)) {
            $event->timestamp = $data->timestamp;
        }
        if (isset($data->remoteIP)) {
            $event->remoteIP = $data->remoteIP;
        }
        if (isset($data->origin)) {
            $event->origin = $data->origin;
        }
        if (isset($data->referer)) {
            $event->referer = $data->referer;
        }
        if (isset($data->context)) {
            $event->context = $data->context;
        }
        if (isset($data->fields)) {
            if (!is_array($data->fields)) {
                throw new \RuntimeException('Invalid content, property "fields" should be an array');
            }
            foreach ($data->fields as $field) {
                $event->addField(Field::fromRaw($field));
            }
        }
        return $event;
    }

    public function clearFields() {
        $this->fields = [];
    }

    /**
     * @param string | Field $nameOrField
     * @param mixed | null $value
     * @param string | null $label
     */
    public function addField($nameOrField, $value = null, $label = null) {
        if (!($nameOrField instanceof Field)) {
            $nameOrField = new Field($nameOrField, $value, $label);
        }
        $this->fields[] = $nameOrField;
    }

    /**
     * @param Field[] | array $fields
     */
    public function addFields($fields) {
        foreach ($fields as $k => $field) {
            if (is_numeric($k) and $field instanceof Field) {
                $this->addField($field);
            } else {
                $this->addField($k, $field);
            }
        }
    }

    /**
     * @param Field[] | array $fields
     */
    public function setFields($fields) {
        $this->fields = [];
        $this->addFields($fields);
    }

    /**
     * @param array $utmData {
     *  @var string $source
     *  @var string $medium
     *  @var string $campaign
     *  @var string $term
     *  @var string $content
     * }
     */
    public function setUTM($utmData) {
        foreach ($utmData as $key => $value) {
            if (!in_array($key, ['source', 'medium', 'campaign', 'term', 'content'])) {
                throw new \RuntimeException('Invalid utm data, keys shoud be: "source", "medium", "campaign", "term" and "content"');
            }
        }
        $this->utm = $utmData;
    }

    /**
     * @param string $fieldName
     * @param null | mixed $default
     * @return mixed | null
     */
    public function getFieldValue($fieldName, $default = null) {
        foreach ($this->fields as $field) {
            if ($field->name === $fieldName) {
                return $field->value === null ? $default : $field->value;
            }
        }
        return $default;
    }

    /**
     * @return array
     */
    public function getFieldsAssoc() {
        $arr = [];
        foreach ($this->fields as $field) {
            $arr[$field->name] = $field->value;
        }
        return $arr;
    }

    /**
     * @return mixed
     */
    public function getContext() {
        return $this->context;
    }
}