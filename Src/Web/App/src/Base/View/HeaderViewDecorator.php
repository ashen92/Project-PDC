<?php
declare(strict_types=1);

namespace Base\View;

class HeaderViewDecorator extends AbstractViewDecorator
{
    const DEFAULT_TEMPLATE = "/../src/App/View/Header.php";
    
    public function render() {
        return $this->renderTemplate() . $this->view->render();
    }
}