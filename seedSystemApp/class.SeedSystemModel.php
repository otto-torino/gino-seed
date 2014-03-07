<?php
/**
 * @file class.SeedSystemModel.php
 * @brief Class SeedSystemModel
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 * @version 0.1
 * @date 2014-03-06
 */
class SeedSystemModel extends Model
{
    private $_controller;
    public static $table = 'seed_system_app_seed_model';

    public function __construct($id)
    {
        $this->_controller = new seedSystemApp();
        $this->_tbl_data = self::$table;

        $this->_fields_label = array(
            //'field_name' => _('field_label'),
        );

        parent::__construct($id);

        $this->_model_label = _('SeedSystemModel');
    }

    /**
     * @brief Casting a stringa
     *
     * @return rappresentazione a stringa dell'oggetto
     */
    function __toString()
    {
        return (string) $this->id;
    }

    /**
     * @brief Definizione della struttura del modello
     *
     * @param $id id dell'istanza
     *
     * @return struttura del modello
     */
    public function structure($id)
    {
        $structure = parent::structure($id);

        return $structure;
    }
    
}

