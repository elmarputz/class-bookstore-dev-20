<?php


namespace Bookshop;


class Controller extends BaseObject
{
    const ACTION = 'action';
    const PAGE = 'page';
    const CC_NAME = 'nameOnCard';
    const CC_NUMBER = 'cardNumber';
    const ACTION_ADD = 'addToCart';
    const ACTION_REMOVE = 'removeFromCart';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_ORDER = 'placeOrder';
    const USER_NAME = 'userName';
    const USER_PASSWORD = 'password';


    private static $instance = false;

    public static function getInstance() : Controller {
        if (!self::$instance) {
            self::$instance = new Controller();
        }
        return self::$instance;
    }

    private function __construct() {}


    public function invokePostAction () : bool {

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new \Exception('Controller can only handle Post Requests');
            return null;
        }
        elseif (!isset($_REQUEST[self::ACTION])) {
            throw new \Exception(self::ACTION . ' not specified.');
            return null;
        }

        $action = $_REQUEST[self::ACTION];


        switch ($action) {
            case self::ACTION_ADD :
                ShoppingCart::add((int) $_REQUEST['bookId']);
                Util::redirect();
                break;
            case self::ACTION_REMOVE :
                ShoppingCart::remove((int) $_REQUEST['bookId']);
                Util::redirect();
                break;
            case self::ACTION_LOGIN :
                if (!AuthenticationManager::authenticate($_REQUEST[self::USER_NAME], $_REQUEST[self::USER_PASSWORD])) {
                    $this->forwardRequest(array('Invalid user name or password.'));
                }
                Util::redirect();
                break;

            case self::ACTION_LOGOUT :
                //sign out current user
                AuthenticationManager::signOut();
                Util::redirect();
                break;

            default :
                throw new \Exception('Unknown controller action: ' . $action);
                return null;
                break;

        }

    }


    /**
     *
     * @param array $errors : optional assign it to
     * @param string $target : url for redirect of the request
     */
    protected function forwardRequest(array $errors = null, $target = null) {
        //check for given target and try to fall back to previous page if needed
        if ($target == null) {
            if (!isset($_REQUEST[self::PAGE])) {
                throw new Exception('Missing target for forward.');
            }
            $target = $_REQUEST[self::PAGE];
        }
        //forward request to target
        // optional - add errors to redirect and process them in view
        if (count($errors) > 0)
            $target .= '&errors=' . urlencode(serialize($errors));
        header('location: ' . $target);
        exit();
    }


}