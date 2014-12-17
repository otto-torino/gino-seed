<?php
/**
 * @file class_seedSystemApp.php
 * @brief Contiene la definizione ed implementazione della classe Gino.App.SeedSystemApp.seedSystemApp
 *
 * @copyright 2015 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */

/**
 * @namespace Gino.App.SeedSystemApp
 * @description Namespace dell'applicazione SeedSystemApp
 */
namespace Gino\App\SeedSystemApp;

use \Gino\View;

require_once('class.SeedSystemModel.php');

/**
 * @brief Classe di tipo Gino.Controller del modulo SeedSystemApp
 *
 * @version 0.1.0
 * @copyright 2015 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class seedSystemApp extends \Gino\Controller
{
    // private $_tbl_opt;

    /**
     * @brief Costruttore
     *
     * @return \Gino\App\SeedApp\seedSystemApp istanza di Gino.App.SeedSystemApp.seedSystemApp
     */
    public function __construct()
    {
        parent::__construct();

        /* options
        $this->_tbl_opt = 'seedapp_opt';
        $this->_optionsValue = array(
            'title'=>_('TItolo'),
        );
        $this->_title = \Gino\htmlChars($this->setOption('title', array('value'=>$this->_optionsValue['title'])));
        $this->_options = \Gino\Loader::load('Options', array($this));
        $this->_optionsLabels = array(
            "title"=>_("Titolo"), 
        );
        */
    }

    /**
     * @brief Restituisce alcune proprietà della classe utili per la generazione di nuove istanze
     *
     * @return lista delle proprietà utilizzate per la creazione di istanze di tipo events (tabelle, css, viste, folders)
     */
    public static function getClassElements() 
    {
        return array(
            "tables"=>array(
                'seed_system_app_seed_model',
            ),
            "css"=>array(
                'seedSystemApp.css',
            ),
            "views" => array(
                'seed_system_box.php' => _('Box'),
                'seed_system_view.php' => _('Vista'),
            ),
            /*
            "folderStructure"=>array (
                CONTENT_DIR.OS.'seedSystemApp'=> null
            ),
            */
        );
    }

    /**
     * @brief Metodi pubblici disponibili per inserimento in layout (non presenti nel file seedSystemApp.ini) e menu (presenti nel file seedSystemApp.ini)
     *
     * @return lista metodi NOME_METODO => array('label' => LABEL, 'permissions' = PERMISSIONS)
     */
    public static function outputFunctions() 
    {
        $list = array(
            "box" => array("label"=>_("Box"), "permissions"=>array()),
            "view" => array("label"=>_("Vista"), "permissions"=>array()),
        );

        return $list;
    }

    /**
     * @brief Box view
     * @description Vista per inserimento in layout
     *
     * @return html
     */
    public function box()
    {
        // codice...
        $view = new View($this->_view_dir, 'seed_system_box');
        $dict = array();

        return $view->render($dict);
    }

    /**
     * @brief Vista view
     *
     * @param \Gino\Http\Request $request
     * @return Gino.Http.Response
     */
    public function view(\Gino\Http\Request $request)
    {
        // codice...
        $view = new View($this->_view_dir, 'seed_system_view');
        $dict = array();

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Interfaccia amministrazione modulo
     *
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return Gino.Http.Response
     */
    public function manageSeedSystemApp(\Gino\Http\Request $request)
    {
        $this->requirePerm('can_admin');

        $block = cleanVar($request->GET, 'block', 'string');

        $link_frontend = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=frontend'), _('Frontend'));
        /* $link_options = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=options'), _('Opzioni')); */
        $link_dft = sprintf('<a href="%s">%s</a>', $this->linkAdmin(), _('SeedSystemModel'));
        $sel_link = $link_dft;

        if($block == 'frontend') {
            $backend = $this->manageFrontend();
            $sel_link = $link_frontend;
        }
        /*
        elseif($block == 'options') {
            $backend = $this->manageOptions();
            $sel_link = $link_options;
        }
        */
        else {
            $backend = $this->manageSeedSystemModel();
        }

        if(is_a($backend, '\Gino\Http\Response')) {
            return $backend;
        }

        /* $links_array = array($link_frontend, $link_options, $link_dft); */
        $links_array = array($link_frontend, $link_dft);

        $dict = array(
          'title' => _('SeedSystemApp'),
          'links' => $links_array,
          'selected_link' => $sel_link,
          'content' => $backend
        );

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Interfaccia di amministrazione SeedSystemModel
     *
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return Gino.Http.Redirect oppure html, interfaccia di back office
     */
    public function manageSeedSystemModel()
    {
        $admin_table = \Gino\Loader::load('AdminTable', array($this, array()));

        $backend = $admin_table->backoffice(
            'SeedSystemModel',
            array(), // display options
            array(), // form options
            array()  // fields options
        );

        return $backend;
    }

}
