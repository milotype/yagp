<?php
if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access this file directly");
}

class PluginYagpConfig extends CommonDBTM {
	static private $_instance = NULL;
	
	public function __construct() {
      global $DB;
      if ($DB->tableExists(self::getTable())) {
         $this->getFromDB(1);
      }
   }
	/**
	* Summary of canCreate
	* @return boolean
	*/
	static function canCreate() {
		return Session::haveRight('config', UPDATE);
	}

	/**
	* Summary of canView
	* @return boolean
	*/
	static function canView() {
		return Session::haveRight('config', READ);
	}

	/**
	* Summary of canUpdate
	* @return boolean
	*/
	static function canUpdate() {
		return Session::haveRight('config', UPDATE);
	}

	/**
	* Summary of getTypeName
	* @param mixed $nb plural
	* @return mixed
	*/
	static function getTypeName($nb=0) {
		return __("Yagp","yagp");
	}

	/**
	* Summary of getInstance
	* @return PluginProcessmakerConfig
	*/
	static function getInstance() {

		if (!isset(self::$_instance)) {
			self::$_instance = new self();
			if (!self::$_instance->getFromDB(1)) {
				self::$_instance->getEmpty();
			}
		}
		return self::$_instance;
	}

	public static function getConfig($update = false) {
		static $config = null;
      if (is_null($config)) {
         $config = new self();
      }
      if ($update) {
         $config->getFromDB(1);
      }
      return $config;
	}

	/**
	* Summary of showConfigForm
	* @param mixed $item is the config
	* @return boolean
	*/
	static function showConfigForm() {
		global $CFG_GLPI;
		
		$config = new self();
		$config->getFromDB(1);

		$config->showFormHeader(['colspan' => 4]);

		echo "<tr class='tab_bg_1'>";
		echo "<td >".__("Ticketsolveddate","yagp")."</td><td >";
		Dropdown::showYesNo("ticketsolveddate", $config->fields["ticketsolveddate"]);
		echo "</td></tr>\n";

		$config->showFormButtons(array('candel'=>false));

		return false;
	}

	function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
		global $LANG;

		if ($item->getType()=='Config') {
			return __("Yagp","yagp");
		}
		return '';
	}

	static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

		if ($item->getType()=='Config') {
			self::showConfigForm($item);
		}
		return true;
	}

	public static function install(Migration $migration) {
      global $DB;

      $table  = self::getTable();
      $config = new self();
      //This class is available since version 1.3.0
      if (!$DB->tableExists($table) && !$DB->tableExists("glpi_plugin_yagp_config")){
			$migration->displayMessage("Installing $table");
			//Install
			$query = "CREATE TABLE `$table` (
							`id` int(11) NOT NULL auto_increment,
                     `actualtime` tinyint(1) NOT NULL default '0',
                     PRIMARY KEY  (`id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
         $DB->query($query) or die ($DB->error());
         $config->add([
                  'id'                          => 1,
                  'ticketsolveddate'              		=> 1,
               ]);
      }
   }
}