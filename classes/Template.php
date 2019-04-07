<?php
class Template {

    private $html;
    private $vars = array();

    public function __construct(string $filename) {
        if (!file_exists($filename)) {
            die("Error creating template: File does not exist!");
        }
        $file = fopen($filename, "r");
        if ($file === false) {
            die("Error creating template: Unable to open file!");
        }
        $this->html = fread($file, filesize($filename));
        fclose($file);
    }

    public function set_var(string $name, string $content) {
        if (!preg_match("/^[a-zA-Z]*$/", $name)) {
            return false;
        }
        $this->vars[$name] = $content;
        return true;
    }

    public function render() {
        $rendered = $this->html;
        foreach ($this->vars as $name => $content) {
            $rendered = str_replace("{{" . $name .  "}}", $content, $rendered);
        }
        $rendered = preg_replace("/{{[a-zA-Z]*}}/", "", $rendered);
        return $rendered;
    }

}
