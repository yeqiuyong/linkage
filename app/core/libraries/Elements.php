<?php
namespace Multiple\Core\Libraries;

use Phalcon\Mvc\User\Component;
use Phalcon\Tag;
use Phalcon\Mvc\Url;

/**
 * Elements
 *
 * Helps to build UI elements for the application
 */
class Elements extends Component
{

    private $_headerMenu = array(
        'navbar-left' => array(
            'index' => array(
                'caption' => 'Home',
                'action' => 'index'
            ),
            'invoices' => array(
                'caption' => 'Invoices',
                'action' => 'index'
            ),
            'about' => array(
                'caption' => 'About',
                'action' => 'index'
            ),
            'contact' => array(
                'caption' => 'Contact',
                'action' => 'index'
            ),
        ),
        'navbar-right' => array(
            'session' => array(
                'caption' => 'Log In/Sign Up',
                'action' => 'index'
            ),
        )
    );

    private $_tabs = array(
        'Invoices' => array(
            'controller' => 'invoices',
            'action' => 'index',
            'any' => false
        ),
        'Companies' => array(
            'controller' => 'companies',
            'action' => 'index',
            'any' => true
        ),
        'Products' => array(
            'controller' => 'products',
            'action' => 'index',
            'any' => true
        ),
        'Product Types' => array(
            'controller' => 'producttypes',
            'action' => 'index',
            'any' => true
        ),
        'Your Profile' => array(
            'controller' => 'invoices',
            'action' => 'profile',
            'any' => false
        )
    );

    private $admin_menu = [
        'Dashboard' => [
            'link' => 'admin/index/index',
            'class' => 'glyphicon glyphicon-home',
            'profile' => 2,
            'has_child' => false
        ],
        '管理员管理' => [
            'link' => 'admin/index/index',
            'class' => 'glyphicon glyphicon-plus',
            'profile' => 1,
            'has_child' => true,
            'child_menu' =>[
                '角色管理' => [
                    'link' => 'admin/index/index',
                    'class' => 'nav nav-pills nav-stacked',
                ],
                '管理员管理' => [
                    'link' => 'admin/adminuser/index',
                    'class' => 'nav nav-pills nav-stacked',
                ]
            ]
        ]
    ];

    /**
     * Builds header menu with left and right items
     *
     * @return string
     */
    public function getMenu()
    {

        $auth = $this->session->get('auth');
        if ($auth) {
            $this->_headerMenu['navbar-right']['session'] = array(
                'caption' => 'Log Out',
                'action' => 'end'
            );
        } else {
            unset($this->_headerMenu['navbar-left']['invoices']);
        }

        $controllerName = $this->view->getControllerName();
        foreach ($this->_headerMenu as $position => $menu) {
            echo '<div class="nav-collapse">';
            echo '<ul class="nav navbar-nav ', $position, '">';
            foreach ($menu as $controller => $option) {
                if ($controllerName == $controller) {
                    echo '<li class="active">';
                } else {
                    echo '<li>';
                }
                echo $this->tag->linkTo($controller . '/' . $option['action'], $option['caption']);
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

    }

    public function getAdminMenu()
    {
        $auth = $this->session->get('auth');
        if ($auth == null) {
            return;
        }

        $url = new Url();
        $base_url = $url->getBaseUri();

        echo '<li class="nav-header">Main</li>';

        $profile_id = $auth['profile_id'];

        foreach($this->admin_menu as $option_name => $option){
            if($profile_id > $option['profile']){
                continue;
            }

            echo '<li>';
            //echo Tag::linkTo(array($option['link'], "class" => 'ajax-link'));

            if($option['has_child']){
                echo '<li class="accordion">';
                echo '<a href="#"><i class="glyphicon glyphicon-plus"></i><span>'.$option_name.'</span></a>';
                echo '<ul class="nav nav-pills nav-stacked">';

                foreach($option['child_menu'] as $child_option_name => $child_option){
                    echo '<li>';
                    echo Tag::linkTo(array($child_option['link'], $child_option_name));
                    echo '</li>';
                }

                echo '</ul>';
                echo '</li>';
            }else{
                echo '<a class="ajax-link" href="'.$base_url.$option['link'].'"><i class="'.$option['class'].'"></i><span>'.$option_name.'</span></a>';
            }
            echo '</li>';
        }

    }

    /**
     * Returns menu tabs
     */
    public function getTabs()
    {
        $controllerName = $this->view->getControllerName();
        $actionName = $this->view->getActionName();
        echo '<ul class="nav nav-tabs">';
        foreach ($this->_tabs as $caption => $option) {
            if ($option['controller'] == $controllerName && ($option['action'] == $actionName || $option['any'])) {
                echo '<li class="active">';
            } else {
                echo '<li>';
            }
            echo $this->tag->linkTo($option['controller'] . '/' . $option['action'], $caption), '</li>';
        }
        echo '</ul>';
    }
}
