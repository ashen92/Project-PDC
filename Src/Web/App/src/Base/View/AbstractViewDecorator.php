<?php
declare(strict_types=1);

namespace Base\View;

use Base\Interface\IView;

abstract class AbstractViewDecorator implements IView
{
    const DEFAULT_TEMPLATE = "/../src/App/View/Default.php";
    protected $template = self::DEFAULT_TEMPLATE;
    protected $view;

    public function __construct(IView $view) {
        $this->view = $view;
    }
    
    public function render() {
        return $this->view->render();
    }
    
    protected function renderTemplate(array $data = array()) {
        extract($data);
        ob_start();
        include $this->template;
        return ob_get_clean();
    }
}