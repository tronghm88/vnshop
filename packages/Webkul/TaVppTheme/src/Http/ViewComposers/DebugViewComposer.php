<?php

namespace Webkul\TaVppTheme\Http\ViewComposers;

use Illuminate\View\View;

class DebugViewComposer
{
    public function compose(View $view)
    {
        $viewName = $view->getName();
        if (strpos($viewName, 'product') !== false || strpos($viewName, 'shop') !== false || strpos($viewName, 'ta-vpp') !== false) {
            file_put_contents(base_path('debug.log'), json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'H5','location'=>'DebugViewComposer.php','message'=>'View rendered','data'=>['viewName'=>$viewName,'file'=>$view->getPath()],'timestamp'=>microtime(true)*1000])."\n", FILE_APPEND);
        }
    }
}
