<?php
declare(strict_types=1);

namespace Base\View;

use Base\Interface\IView;
use InvalidArgumentException;

class View implements IView
{
    const DEFAULT_TEMPLATE = "/../src/App/View/Default.php";
    
    protected $template = self::DEFAULT_TEMPLATE;
    protected $fields = array();
    
    public function __construct($template = null, array $fields = array()) {
        if ($template !== null) {
            $this->setTemplate($template);
        }
        if (!empty($fields)) {
            foreach ($fields as $name => $value) {
                $this->$name = $value;
            }
        } 
    }
    
    public function setTemplate($template) {
        $template = $template . ".php";
        if (!is_file($template) || !is_readable($template)) {
            throw new InvalidArgumentException(
                "The template '$template' is invalid.");   
        }
        $this->template = $template;
        return $this;
    }
      
    public function getTemplate() {
        return $this->template;
    }
    
    public function __set($name, $value) {
        $this->fields[$name] = $value;
        return $this;
    }
    
    public function __get($name) {
        if (!isset($this->fields[$name])) {
            throw new InvalidArgumentException(
                "Unable to get the field '$name'.");
        }
        $field = $this->fields[$name];
        return $field instanceof Closure ? $name($this) : $field;
    }
    
    public function __isset($name) {
        return isset($this->fields[$name]);
    }
    
    public function __unset($name) {
        if (!isset($this->fields[$name])) {
            throw new InvalidArgumentException(
                "Unable to unset the field '$name'.");
        }
        unset($this->fields[$name]);
        return $this;
    }
    
    public function render() {
        extract($this->fields);
        ob_start();
        include $this->template;
        return ob_get_clean();
    }
}