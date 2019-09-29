<?php
require_once "System.php";
require_once "User.php";

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

    public function set_var(string $name, string $content, bool $html = false)
    {
        if ($html) {
            $this->vars[$name] = $content;
        } else {
            $this->vars[$name] = htmlspecialchars($content);
        }
    }

    public function render(User $user, System $system): string
    {
        $rendered = $this->html;
        foreach ($this->vars as $name => $content) {
            $rendered = str_replace("{{" . $name . "}}", $content, $rendered);
        }
        if ($user->getDoNotDisturb($system)) {
            while (strpos($rendered, "{{donotdisturb}}") !== false and strpos($rendered, "{{/donotdisturb}}") !== false) {
                $start = strpos($rendered, "{{donotdisturb}}");
                $end = strpos(substr($rendered, $start), "{{/donotdisturb}}") + $start + 17;
                $rendered = substr($rendered, 0, $start) . substr($rendered, $end);
            }
        }
        $rendered = preg_replace("/{{[a-zA-Z\/]*}}/", "", $rendered);
        return $rendered;
    }
}
