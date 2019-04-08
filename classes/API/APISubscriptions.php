<?php
class APISubscriptions {

    public $error = false;

    private $content;
    private $API;

    public function __construct(APIOAuth $privateAPI, API $API) {
        $this->API = $API;
        $this->content = $privateAPI->get("/subscriptions", array("mine" => "true", "part" => "snippet", "maxResults" => "50"));
        if (count($this->content->items) == 0) {
            $this->error = true;
            return;
        }
    }

    public function get_subscriptions() {
        $result = array();
        foreach ($this->content->items as $subscription) {
            $result[] = new APIChannel($this->API, $subscription->snippet->resourceId->channelId);
        }
        return $result;
    }

}
