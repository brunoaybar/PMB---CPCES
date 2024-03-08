<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_animationslist_datasource_animations_by_type.class.php,v 1.1.2.2 2022/06/03 13:11:59 jparis Exp $
use Pmb\Animations\Orm\AnimationOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_animationslist_datasource_animations_by_type extends cms_module_common_datasource_animations_list
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->sortable = true;
        $this->limitable = true;
    }

    /*
     * On d�fini les s�lecteurs utilisable pour cette source de donn�e
     */
    public function get_available_selectors()
    {
        return array(
            "cms_module_animationslist_selector_type"
        );
    }

    /*
     * R�cup�ration des donn�es de la source...
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        if (! empty($selector) && $selector->get_value()) {

            $data = array(
                "title" => "",
                "animations" => array()
            );
            $animations = array();
            
            $values = $selector->get_value();
            if(!is_array($selector->get_value())) {
                $values = [$selector->get_value()];
            }
            foreach ($values as $num_type) {
                $animationsOrm = AnimationOrm::find("num_type", $num_type);
                $index = count($animationsOrm);
                for ($i = 0; $i < $index; $i ++) {
                    $animations[] = $animationsOrm[$i]->id_animation;
                }
            }


            $data['animations'] = $this->filter_datas('animations', $animations);
            if (! count($data['animations'])) {
                return false;
            }

            $data = $this->sort_animations($data['animations']);
            $data['title'] = "";

            return $data;
        }
        return false;
    }
}