<?php

class Template
{
    private $html;
    private $vars = array();

    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            die("Can't create template: File $filename not found");
        }
        $file = fopen($filename, "r");
        $this->html = fread($file, filesize($filename));
        fclose($file);
    }

    public function set_var(string $name, string $content)
    {
        $this->vars[$name] = $content;
    }

    public function render(): string
    {
        $rendered = $this->html;
        foreach ($this->vars as $name => $content) {
            $rendered = str_replace("{{" . $name . "}}", $content, $rendered);
        }
        $rendered = preg_replace("/{{[a-zA-Z]*}}/", "", $rendered);
        return $rendered;
    }
}
