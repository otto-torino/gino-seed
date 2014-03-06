<?php
/**
 * @file class_seedApp.php
 * @brief Seed App
 * @author marco guidotti
 * @author abidibo
 * @version 0.1
 * @date 2014-03-06
 */
require_once('class.SeedModel.php');
/**
 * Class seedApp
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class seedApp extends Controller
{

    protected $_instance,
              $_data_dir,
              $_data_www,
              $_view_dir;

    private $_action,
            $_block;

    /**
     * @brief Costruttore
     *
     * @param $mdlId id istanza
     *
     * @return oggetto di tipo seedApp
     */
    public function __construct($mdlId)
    {
        parent::__construct();

        $this->_instance = $mdlId;

        $this->_data_dir = $this->_data_dir.OS.$this->_instance_name;
        $this->_data_www = $this->_data_www."/".$this->_instance_name;

        $this->_view_dir = dirname(__FILE__).OS.'views';

        $this->_action = cleanVar($_REQUEST, 'action', 'string', '');
        $this->_block = cleanVar($_REQUEST, 'block', 'string', '');
    }

    /**
     * @brief Restituisce alcune proprietà della classe utili per la generazione di nuove istanze
     *
     * @static
     * @return lista delle proprietà utilizzate per la creazione di istanze di tipo news
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
            ),
            "folderStructure"=>array (
                CONTENT_DIR.OS.'seedApp'=> null
            )
        );
    }

    /**
     * @brief Metodo invocato quando viene eliminata un'istanza di tipo blog
     *
     * Si esegue la cancellazione dei dati da db e l'eliminazione di file e directory 
     * 
     * @access public
     * @return bool il risultato dell'operazione
     */
    public function deleteInstance() 
    {
        $this->requirePerm('can_admin');

        /*
         * delete SeedModel
         */
        $query = "SELECT id FROM ".SeedModel::$table." WHERE instance='$this->_instance'";
        $a = $this->_db->selectquery($query);
        if(sizeof($a)>0) {
            foreach($a as $b) {
                translation::deleteTranslations(SeedModel::$table, $b['id']);
            }
        }

        $query = "DELETE FROM ".SeedModel::$table." WHERE instance='$this->_instance'";	
        $result = $this->_db->actionquery($query);

        /*
         * delete css files
         */
        $classElements = $this->getClassElements();
        foreach($classElements['css'] as $css) {
            unlink(APP_DIR.OS.$this->_className.OS.baseFileName($css)."_".$this->_instance_name.".css");
        }

        /*
         * delete folder structure
         */
        foreach($classElements['folderStructure'] as $fld=>$fldStructure) {
            $this->_registry->pub->deleteFileDir($fld.OS.$this->_instance_name, true);
        }

        return $result;
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
     * @brief Getter della proprietà instance_name 
     * 
     * @return nome dell'istanza
     */
    public function getInstanceName() 
    {
        return $this->_instance_name;
    }

    public function view()
    {
        // codice...
        $view = new View($this->_view_dir, 'seed_view');
        $dict = array();

        return $view->render($dict);
    }

    /**
     * @brief Backoffice
     *
     * @return interfaccia di backoffice
     */
    public function manageDoc()
    {
        $this->requirePerm('can_admin');

        $method = 'manageDoc';

        $link_frontend = "<a href=\"".$this->_home."?evt[$this->_instance_name-$method]&block=frontend\">"._("Frontend")."</a>";
        $link_dft = "<a href=\"".$this->_home."?evt[".$this->_instance_name."-$method]\">"._("SeedModel")."</a>";

        $sel_link = $link_dft;

        if($this->_block == 'frontend' && $this->userHasPerm('can_admin')) {
            $buffer = $this->manageFrontend();
            $sel_link = $link_frontend;
        }
        else {
            $buffer = $this->manageSeedModel();
        }

        // groups privileges
        $links_array = array($link_frontend, $link_dft);

        $dict = array(
          'title' => _('SeedModel'),
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
    public function manageSeedModel()
    {
        $registry = registry::instance();
        $admin_table = Loader::load('AdminTable', array($this, array()));

        $buffer = $admin_table->backoffice(
            'SeedModel',
            array(), // display options
            array(), // form options
            array()  // fields options
        );

        return $buffer;
    }

}
