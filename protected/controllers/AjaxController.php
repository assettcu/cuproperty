<?php
/**
 * Ajax Controller
 *
 * The main Controller class for the entire application. This handles all the action requests
 * and redirects or routes them through the proper channels. Setting up classes and processing
 * forms are done in the appropriate action functions.
 * 
 * This class conforms to the Model-View-Controller paradigm.
 * 
 */
class AjaxController extends Controller
{
    public function actionUpdateWatchList()
    {
        $this->noGuest();
        $user = new UserObj(Yii::app()->user->name);
        if(!$user->loaded) {
            return false;
        }
        $user->watchlist = $_REQUEST["tags"];
        return $user->save();
    }
    
    public function actionCron()
    {
        $cron = new Cron;
        $cron->parse_watchlist();
    }
    
    /**
     * Important function for enforcing SSL on a page.
     * Mainly used for the Login page.
     */
    private function makeSSL()
    {
        if($_SERVER['SERVER_PORT'] != 443) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            exit();
        }
    }

    /**
     * Forces page to not be SSL.
     */
    private function makeNonSSL()
    {
        if($_SERVER['SERVER_PORT'] == 443) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            exit();
        }
    }
    
    /**
     * Checks to see if a user is logged into the application.
     * If not then it will redirect to the login page with a warning.
     */
    private function noGuest()
    {
        if(Yii::app()->user->isGuest) {
            Yii::app()->user->setFlash("warning","You must be signed in to access this page.");
            $this->redirect(Yii::app()->createUrl('login')."?redirect=".urlencode("https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
            exit;
        }
    }
}
    