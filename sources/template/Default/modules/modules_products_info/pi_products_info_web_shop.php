<?php
/**
 * pi_products_info_only_web.php 
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * Academic Free License ("AFL") v. 3.0

 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pi_products_info_web_shop {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_web_shop');
      $this->description = CLICSHOPPING::getDef('module_products_info_web_shop_description');

      if (defined('MODULE_PRODUCTS_INFO_WEB_SHOP_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_WEB_SHOP_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_WEB_SHOP_STATUS == 'True');
      }
    }

    public function execute() {

      if (isset($_GET['products_id']) && isset($_GET['Products']) ) {

        $content_width = (int)MODULE_PRODUCTS_INFO_WEB_SHOP_CONTENT_WIDTH;
        $text_position = MODULE_PRODUCTS_INFO_WEB_SHOP_POSITION;

        $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
        $CLICSHOPPING_Template = Registry::get('Template');

        $products_web_shop = $CLICSHOPPING_ProductsCommon->getProductsWebAndShop();

        if ($products_web_shop  != '') {
          $products_web_shop_content = '<!-- Start only web products -->' . "\n";

          ob_start();
          require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_info_web_shop'));
          $products_web_shop_content .= ob_get_clean();

          $products_web_shop_content .= '<!-- end only web products  -->' . "\n";

          $CLICSHOPPING_Template->addBlock($products_web_shop_content, $this->group);
        }
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_WEB_SHOP_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_WEB_SHOP_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_WEB_SHOP_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'A quel endroit souhaitez-vous afficher le code barre ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_WEB_SHOP_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Affiche le code barre du produit à gauche ou à droite<br><br><i>(Valeur Left = Gauche <br>Valeur Right = Droite <br>Valeur None = Aucun)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-md-right\', \'float-md-left\', \'float-md-none\'),',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_WEB_SHOP_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                              ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                            );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_PRODUCTS_INFO_WEB_SHOP_STATUS',
        'MODULE_PRODUCTS_INFO_WEB_SHOP_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_WEB_SHOP_POSITION',
        'MODULE_PRODUCTS_INFO_WEB_SHOP_SORT_ORDER'
      );
    }
  }
