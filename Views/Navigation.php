<?php
/**
 * The left navigation html output will be build.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package Agent_AddPages
 */

namespace AgentAddPages\Views;

class Navigation
{

    public function __construct()
    {
    }

    /**
     * Returns rendered html.
     * @return string
     */
    public function render()
    {
        $view = new \lw_view(dirname(__FILE__) . '/Templates/Navigation.phtml');
        return $view->render();
    }

}