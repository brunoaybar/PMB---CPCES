<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationOrm.php,v 1.20.2.1 2022/01/18 09:12:47 qvarin Exp $

namespace Pmb\Animations\Orm;

use Pmb\Common\Orm\Orm;

class AnimationOrm extends Orm
{

    /**
     * Table name
     * 
     * @var string
     */
    public static $tableName = "anim_animations";

    /**
     * Primary Key
     * 
     * @var string
     */
    public static $idTableName = "id_animation";
    
    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     * 
     * @var integer
     */
    protected $id_animation = 0;

    /**
     * 
     * @var string
     */
    protected $name = "";
    
    /**
     * 
     * @var string
     */
    protected $comment = "";

    /**
     * 
     * @var string
     */
    protected $description  = "";

    /**
     * 
     * @var integer
     */
    protected $global_quota = 0;
    
    /**
     *
     * @var boolean
     */
    protected $allow_waiting_list = false;

    /**
     * 
     * @var integer
     */
    protected $internet_quota = 0;
    
    /**
     *
     * @var boolean
     */
    protected $auto_registration = false;
   
    /**
     * 
     * @var integer
     */
    protected $expiration_delay = 0;
    
    /**
     * 
     * @var boolean
     */
    protected $registration_required = true;

    /**
     * 
     * @var integer
     */
    protected $num_status = 1;
    
    /**
     * @Relation 0n
     * @Orm Pmb\Animations\Orm\AnimationStatusOrm
     * @RelatedKey num_status
     */
    protected $status = null;
    
    /**
     * 
     * @var integer
     */
    protected $num_event = 0;
    
    /**
     * @Relation 0n
     * @Orm Pmb\Animations\Orm\EventOrm
     * @RelatedKey num_event
     */
    protected $event = null;
    
    /**
     * 
     * @var integer
     */
    protected $num_parent = 0;

    /**
     * 
     * @var integer
     */
    protected $num_cart = 0;
    
    /**
     * @Relation 0n
     * @Orm Pmb\Animations\Orm\AnimationOrm
     * @RelatedKey num_parent
     */
    protected $parent = null;
    
    /**
     * @Relation nn
     * @Orm Pmb\Common\Orm\DocsLocationOrm
     * @TableLink anim_animation_locations
     * @RelatedKey num_location
     * @ForeignKey num_animation
     */
    protected $location = null;

    /**
     * @Relation nn
     * @Orm Pmb\Animations\Orm\MailingAnimationOrm
     * @TableLink anim_mailings
     * @RelatedKey id_mailing
     * @ForeignKey num_animation
     */
    protected $mailing = null;
    
    /**
     * @Relation nn
     * @Orm Pmb\Autorities\Orm\CategoryOrm
     * @TableLink anim_animation_categories
     * @RelatedKey num_noeud
     * @ForeignKey num_animation
     */
    protected $categories = null;
    
    /**
     *
     * @var integer
     */
    protected $num_type = 1;
    
    /**
     * Type par d�faut
     * 
     * @var integer
     */
    public const DEFAULT_TYPE = 1;
    
    /**
     * @Relation 0n
     * @Orm Pmb\Animations\Orm\AnimationTypesOrm
     * @RelatedKey num_type
     */
    protected $type = null;
}