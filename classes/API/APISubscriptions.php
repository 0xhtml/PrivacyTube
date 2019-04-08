<?php
class APISubscriptions {

    public $error = false;

    private $content;

    public function __construct(APIOAuth $API) {
        $this->content = $API->get("/subscriptions", array("mySubscribers" => "true", "part" => "snippet"));
        if (count($this->content->items) == 0) {
            $this->error = true;
            return;
        }
    }

    public function get_data() {
        var_dump($this->content);
        die();
    }

}
