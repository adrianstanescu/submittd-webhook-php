<?php
namespace Submittd\Webhook;

class Payload {
    /**
     * @var string | null
     */
    public $accountID = null;
    /**
     * @var string | null
     */
    public $applicationID = null;
    /**
     * @var string | null
     */
    public $formID = null;
    /**
     * @var Event[]
     */
    public $events = [];

    public function getEncoded() {
        return json_encode($this);
    }
    public function setEncoded($encodedContent) {
        $data = json_decode($encodedContent);
        if ($data === null) {
            throw new \RuntimeException('Cannot decode content');
        }
        if (!is_object($data)) {
            throw new \RuntimeException('Invalid content');
        }
        if (isset($data->accountID)) {
            $this->accountID = $data->accountID;
        }
        if (isset($data->applicationID)) {
            $this->applicationID = $data->applicationID;
        }
        if (isset($data->formID)) {
            $this->formID = $data->formID;
        }
        if (isset($data->events)) {
            if (!is_array($data->events)) {
                throw new \RuntimeException('Invalid content, property "events" should be an array');
            }
            foreach ($data->events as $event) {
                $this->events[] = Event::fromRaw($event);
            }
        }
    }
}