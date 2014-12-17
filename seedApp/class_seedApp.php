<?php
/**
 * @file class_seedApp.php
 * @brief Contiene la definizione ed implementazione della classe Gino.App.SeedApp.seedApp
 *
 * @copyright 2015 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */

/**
 * @namespace Gino.App.SeedApp
 * @description Namespace dell'applicazione SeedApp
 */
namespace Gino\App\SeedApp;

use \Gino\View;

require_once('class.SeedModel.php');

/**
 * @brief Classe di tipo Gino.Controller del modulo SeedApp
 *
 * @version 0.1.0
 * @copyright 2015 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class seedApp extends \Gino\Controller
{

    // private $_tbl_opt;

    /**
     * @brief Costruttore
     *
     * @param int $instance_id id istanza
     * @return \Gino\App\SeedApp\seedApp istanza di Gino.App.SeedApp.seedApp
     */
    public function __construct($instance_id)
    {
        parent::__construct($instance_id);

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
                'seed_app_seed_model',
            ),
            "css"=>array(
                'seedApp.css',
            ),
            "views" => array(
                'seed_view.php' => _('Vista'),
                'seed_box.php' => _('Box'),
            ),
            /*
            "folderStructure"=>array (
                CONTENT_DIR.OS.'seedApp'=> null
            ),
            */
        );
    }

    /**
     * @brief Metodo invocato quando viene eliminata un'istanza di tipo seedApp
     *
     * Si esegue la cancellazione dei dati da db e l'eliminazione di file e directory
     * @return TRUE
     */
    public function deleteInstance()
    {

        $this->requirePerm('can_admin');

        /** delete SeedModel */
        SeedModel::deleteInstance($this);

        /** delete opzioni */
        /*
        $opt_id = $this->_db->getFieldFromId($this->_tbl_opt, "id", "instance", $this->_instance);
        \Gino\Translation::deleteTranslations($this->_tbl_opt, $opt_id);
        $result = $this->_db->delete($this->_tbl_opt, "instance=".$this->_instance);
        */

        /** delete css files */
        $classElements = $this->getClassElements();
        foreach($classElements['css'] as $css) {
            unlink(APP_DIR.OS.$this->_class_name.OS.\Gino\baseFileName($css)."_".$this->_instance_name.".css");
        }

        /** eliminazione views */
        foreach($classElements['views'] as $k => $v) {
            unlink($this->_view_dir.OS.\Gino\baseFileName($k)."_".$this->_instance_name.".php");
        }

        /** delete folder structure */
        /*
        foreach($classElements['folderStructure'] as $fld=>$fldStructure) {
            \Gino\deleteFileDir($fld.OS.$this->_instance_name, TRUE);
        }
        */

        return TRUE;
    }

    /**
     * @brief Metodi pubblici disponibili per inserimento in layout (non presenti nel file seedApp.ini) e menu (presenti nel file seedApp.ini)
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
        $view = new View($this->_view_dir, 'seed_box');
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
        $view = new View($this->_view_dir, 'seed_view');
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
    public function manageDoc(\Gino\Http\Request $request)
    {
        $this->requirePerm('can_admin');

        $block = cleanVar($request->GET, 'block', 'string');

        $link_frontend = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=frontend'), _('Frontend'));
        /* $link_options = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=options'), _('Opzioni')); */
        $link_dft = sprintf('<a href="%s">%s</a>', $this->linkAdmin(), _('SeedModel'));
        $sel_link = $link_dft;

        if($block == 'frontend') {
            $backend = $this->manageFrontend();
            $sel_link = $link_frontend;
        }
        /*
        elseif($block=='options') {
            $backend = $this->manageOptions();
            $sel_link = $link_options;
        }
        */
        else {
            $backend = $this->manageSeedModel($request);
        }

        if(is_a($backend, '\Gino\Http\Response')) {
            return $backend;
        }

        /* $links_array = array($link_frontend, $link_options, $link_dft); */
        $links_array = array($link_frontend, $link_dft);

        $view = new View(null, 'tab');
        $dict = array(
          'title' => _('SeedModel'),
          'links' => $links_array,
          'selected_link' => $sel_link,
          'content' => $backend
        );

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Interfaccia di amministrazione SeedModel
     *
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return Gino.Http.Redirect oppure html, interfaccia di back office
     */
    public function manageSeedModel(\Gino\Http\Request $request)
    {
        $admin_table = \Gino\Loader::load('AdminTable', array($this, array()));

        $backend = $admin_table->backoffice(
            'SeedModel',
            array(), // display options
            array(), // form options
            array()  // fields options
        );

        return $backend;
    }

}
