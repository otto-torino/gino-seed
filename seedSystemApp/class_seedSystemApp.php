<?php
/**
 * @file class_seedSystemApp.php
 * @brief Seed App
 * @author marco guidotti
 * @author abidibo
 * @version 0.1
 * @date 2014-03-06
 */
require_once('class.SeedSystemModel.php');
/**
 * Class seedSystemApp
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class seedSystemApp extends Controller
{

    protected $_data_dir,
              $_data_www,
              $_view_dir;

    /**
     * @brief Costruttore
     *
     * @return oggetto di tipo seedSystemApp
     */
    public function __construct()
    {
        parent::__construct();

        $this->_data_dir = $this->_data_dir.OS.$this->_instance_name;
        $this->_data_www = $this->_data_www."/".$this->_instance_name;

        $this->_view_dir = dirname(__FILE__).OS.'views';

        /* options
        $this->_optionsValue = array(
            'title'=>_('TItolo'),
        );
        $this->_title = htmlChars($this->setOption('title', array('value'=>$this->_optionsValue['title'])));
        $this->_options = loader::load('Options', array($this->_class_name, $this->_instance));
        $this->_optionsLabels = array(
            "title"=>_("Titolo"), 
        );
        */
    }

    /**
     * @brief Restituisce alcune proprietà della classe
     *
     * @static
     * @return lista delle proprietà
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
     * @brief Metodi pubblici disponibili per inserimento in layout a menu
     *
     * @return lista metodi pubblici
     */
    public static function outputFunctions() 
    {
        $list = array(
            "view" => array("label"=>_("Vista"), "permissions"=>array()),
        );

        return $list;
    }

    /**
     * @brief Percorso assoluto alla cartella dei contenuti 
     * 
     * @return percorso assoluto
     */
    public function getBaseAbsPath() 
    {
        return $this->_data_dir;
    }

    /**
     * @brief Percorso relativo alla cartella dei contenuti 
     * 
     * @return percorso relativo
     */
    public function getBasePath() 
    {
        return $this->_data_www;
    }

    /**
     * @brief Output pubblico
     *
     * @return output view
     */
    public function view()
    {
        // codice...
        $view = new View($this->_view_dir, 'seed_system_view');
        $dict = array();

        return $view->render($dict);
    }

    /**
     * @brief Backoffice
     *
     * @return interfaccia di backoffice
     */
    public function manageSeedSystemApp()
    {
        $this->requirePerm('can_admin');

        $method = 'manageSeedSystemApp';

        $link_frontend = "<a href=\"".$this->_home."?evt[$this->_instance_name-$method]&block=frontend\">"._("Frontend")."</a>";
        /* $link_options = "<a href=\"".$this->_home."?evt[$this->_class_name-$method]&block=options\">"._("Opzioni")."</a>"; */
        $link_dft = "<a href=\"".$this->_home."?evt[".$this->_instance_name."-$method]\">"._("SeedModel")."</a>";

        $sel_link = $link_dft;

        if($this->_block == 'frontend' && $this->userHasPerm('can_admin')) {
            $buffer = $this->manageFrontend();
            $sel_link = $link_frontend;
        }
        /*
        elseif($block=='options') {
            $buffer = $this->manageOptions();
            $sel_link = $link_options;
        }
        */
        else {
            $buffer = $this->manageSeedSystemModel();
        }

        // groups privileges
        /* $links_array = array($link_frontend, $link_options, $link_dft); */
        $links_array = array($link_frontend, $link_dft);

        $dict = array(
          'title' => _('SeedSystemApp'),
          'links' => $links_array,
          'selected_link' => $sel_link,
          'content' => $buffer
        );

        $view = new view(null, 'tab');

        return $view->render($dict);
    }

    /**
     * @brief Interfaccia di amministrazione SeedModel
     *
     * @return interfaccia di amministrazione
     */
    public function manageSeedSystemModel()
    {
        $registry = registry::instance();
        $admin_table = Loader::load('AdminTable', array($this, array()));

        $buffer = $admin_table->backoffice(
            'SeedSystemModel',
            array(), // display options
            array(), // form options
            array()  // fields options
        );

        return $buffer;
    }

}
