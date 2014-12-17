<?php
/**
 * @file class.SeedModel.php
 * @brief Contiene la definizione ed implementazione della classe Gino.App.SeedApp.SeedModel
 *
 * @copyright 2015 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */

namespace Gino\App\SeedApp;

/**
 * @brief Classe di tipo Gino.Model che rappresenta un SeedModel
 *
 * @version 0.1.0
 * @copyright 2015 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class SeedModel extends \Gino\Model
{
    public static $table = 'seed_app_seed_model';

    /**
     * @brief Costruttore
     *
     * @param int $id id del record
     * @param \Gino\App\SeedApp\seedApp $instance istanza di Gino.App.SeedApp.seedApp
     * @return istanza di Gino.App.SeedApp.SeedModel
     */
    public function __construct($id, $instance)
    {
        $this->_controller = $instance;
        $this->_tbl_data = self::$table;

        $this->_fields_label = array(
            //'field_name' => _('field_label'),
        );

        parent::__construct($id);

        $this->_model_label = _('SeedModel');
    }

    /**
     * @brief Rappresentazione a stringa dell'oggetto
     *
     * @return id
     */
    function __toString()
    {
        return (string) $this->id;
    }

    /**
     * @brief Definizione della struttura del modello
     *
     * @see Gino.Model::structure()
     * @param $id id dell'istanza
     *
     * @return array, struttura del modello
     */
    public function structure($id)
    {
        $structure = parent::structure($id);

        return $structure;
    }

}
