<?php
declare(strict_types=1);

namespace Base\View;

class FooterViewDecorator extends AbstractViewDecorator
{
    const DEFAULT_TEMPLATE = "/../src/App/View/Shared/Footer.php";
    
    public function render() {
        return $this->view->render() . $this->renderTemplate();
    }
}