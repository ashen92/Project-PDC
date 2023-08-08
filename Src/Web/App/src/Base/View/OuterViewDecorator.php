<?php
declare(strict_types=1);

namespace Base\View;

class OuterViewDecorator extends AbstractViewDecorator
{
    const DEFAULT_TEMPLATE = "/../src/App/View/Shared/Layout.php";
    
    public function render() {
        $data["innerview"] = $this->view->render();
        return $this->renderTemplate($data);
    }
}