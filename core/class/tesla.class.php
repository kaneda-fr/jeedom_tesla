<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/******************************* Includes *******************************/ 
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';




class tesla extends eqLogic {
    /******************************* Attributs *******************************/ 
    /* Ajouter ici toutes vos variables propre à votre classe */

    /***************************** Methode static ****************************/ 

    /*
    // Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function cron() {

    }
    */

    /*
    // Fonction exécutée automatiquement toutes les heures par Jeedom
    public static function cronHourly() {

    }
    */

    /*
    // Fonction exécutée automatiquement tous les jours par Jeedom
    public static function cronDayly() {

    }
    */
	
	// Retrieve data for all vehicles
	public static function pull($_options) {
		//log::add('tesla', 'error', "Vehicle data updated through API");
		//if 
		
		$eqLogics = eqLogic::byType('tesla'); // ne pas oublier de modifier pour le nom de votre plugin
    	// la liste des équipements
		try {
			$list = tesla::listAllVehicles();
		} catch (Exception $e) {
			$message = displayExeption($e); //TODO: Not Used
			log::add('tesla', 'error', $e);
		}
		
    	foreach ($eqLogics as $eqLogic) {
    		log::add('tesla', 'error', "Pulling data for " . $eqLogic->getHumanName(false, false) . " VIN: ". $eqLogic->getConfiguration('vin'));
    		
    		foreach ($list->{'response'} as $car){
    			if ($car->{'vehicle_id'} == $eqLogic->getLogicalId()){
    				$eqLogic->setConfiguration('state', $car->{'state'});	
    			}	
    		}
    		
    		try {
    			log::add('tesla', 'error', "Getting Data " );
    			$carData = tesla::getVehicleData($eqLogic->getConfiguration('id'));
    			log::add('tesla', 'error', $carData);
    			
    			foreach ($eqLogic->getCmd('info') as $cmd) {
    				$cmd->event($carData->{$cmd->getLogicalId()});
    				log::add('tesla', 'error', "Adding " . $cmd->getLogicalId() . " : ". $carData->{$cmd->getLogicalId()});
    			}
    		} catch (Exception $e) {
    			$message = displayExeption($e); //TODO: Not Used
    			log::add('tesla', 'error', $e);
    		}
    	}
	}
	
	public static function getVehicleData($vehicle_id=null){
		if ($vehicle_id == null){
			throw new Exception(__("no vehicle_id provided", __FILE__));
		}
					
		log::add('tesla', 'error', "Getting Data for " . $vehicle_id);
				
		// Need to complete request URL
		$url = "https://owner-api.teslamotors.com/api/1/vehicles/" . $vehicle_id . "/data_request/charge_state";
		$token = config::byKey('token', 'tesla');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer " . $token,
				'Content-Type: application/json'
		));
		$response = curl_exec($ch);
		$error = curl_error($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		
		log::add('tesla', 'error', "API Response " . $response . " error " .$errno );
		
		if($errno) {
			throw new Exception(__("Curl Error : " . curl_strerror($errno), __FILE__));
			return null;
		}
		
		return json_decode($response)->{'response'};
		
	}
	

	public static function checkAPI() {
		$url = "https://owner-api.teslamotors.com/api/1/vehicles";
		
		$token = config::byKey('token', 'tesla');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer " . $token,
				'Content-Type: application/json'
		));
		$response = curl_exec($ch);
		$error = curl_error($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		
		if($errno) {
			log::add('tesla', 'Error', "Curl Error : " . curl_strerror($errno));
		}
		
		return json_decode($response)->{'count'};		
	}
	
	public static function listAllVehicles() {
		$url = "https://owner-api.teslamotors.com/api/1/vehicles";
	
		$token = config::byKey('token', 'tesla');
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer " . $token,
				'Content-Type: application/json'
		));
		$response = curl_exec($ch);
		$error = curl_error($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		
		if($errno) {
			throw new Exception(__("Curl Error : " . curl_strerror($errno), __FILE__));
		}
	
		$list = json_decode($response);
		return $list;
	}
	
	public static function createToken($email=null, $password=null) {
		$owner_API = "https://owner-api.teslamotors.com/oauth/token";
		$portal_API = "https://owner-api.teslamotors.com/api/1/vehicles";
		
		$json = array(
				"grant_type" => "password",
				"client_id" => "e4a9949fcfa04068f59abb5a658f2bac0a3428e4652315490b659d5ab3f35a9e",
				"client_secret"=> "c75f14bbadc8bee3a7594412c31416f8300256d7668ea7e6e7f06727bfb9d220",
				"email" => $email,
				"password" => $password
				);
		
		$data = json_encode($json);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $owner_API);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$response = curl_exec($ch);
		$error = curl_error($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		
		if ($response === false) {
			log::add('tesla', 'Error', $error);
			throw new Exception(__($error, __FILE__));
		} 
		
		if($errno) {
			throw new Exception(__("Curl Error : " . curl_strerror($errno), __FILE__));
		}

		if (json_decode($response)->{'access_token'} == null){
			if (json_decode($response)->{'reponse'} != null){
				throw new Exception(__(json_decode($response)->{'response'}, __FILE__));
			} else {
				throw new Exception(__(json_decode($response)->{'error'} . " - " . json_decode($response)->{'error_description'}, __FILE__));
			}
		}
				
		return json_decode($response)->{'access_token'};
	}
	
	public static function syncEqLogicWithTeslaSite($_logical_id = null) {
		$message = '';
		try {
			$list = self::listAllVehicles();
			$num = $list->{'count'};
		} catch (Exception $e) {
			$num = -1;
			$message = displayExeption($e);
			log::add('tesla', 'error', $e);
		}

		if ( $num <= 0) {
			if ($message == '')
				$message = "Aucun vehicule identifie via l'API Tesla";
			nodejs::pushUpdate('jeedom::alert', array(
					'level' => 'warning',
					'message' => __($message, __FILE__),
			));
			return;
		}
		
		foreach ($list->{'response'} as $car){
			
			$eqLogic = self::byLogicalId($car->{'vehicle_id'}, 'tesla');
			if (!is_object($eqLogic)) {
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('tesla');
				$eqLogic->setIsEnable(1);
				$eqLogic->setLogicalId($car->{'vehicle_id'});
				$eqLogic->setName($car->{'display_name'});
				$eqLogic->setConfiguration('options_codes', $car->{'options_codes'});
				$eqLogic->setConfiguration('vin', $car->{'vin'});
				$eqLogic->setConfiguration('state', $car->{'state'});
				$eqLogic->setConfiguration('id', $car->{'id'});
				$eqLogic->setIsVisible(1);
				$eqLogic->save();
				//$eqLogic = tesla::byId($eqLogic->getId());
				//$include_device = $eqLogic->getId();
				//$eqLogic->createCommand(false, $result);
				
				log::add('tesla', 'error', ' Voiture ajoutee, VIN: ' . $car->{'vin'});
				
				nodejs::pushUpdate('jeedom::alert', array(
						'level' => 'info',
						'message' => 'Nouvelle voiture ajoutee',
				));
			} else {
				// update data here
				
				$eqLogic->setName($car->{'display_name'});
				$eqLogic->setConfiguration('options_codes', $car->{'options_codes'});
				$eqLogic->setConfiguration('vin', $car->{'vin'});
				$eqLogic->setConfiguration('state', $car->{'state'});
				
				log::add('tesla', 'error', 'Voiture mise a jour, VIN: ' . $car->{'vin'});
				$eqLogic->save();
			
			}
		}
		return;
	}
		
		
		
 
    /*************************** Methode d'instance **************************/ 
 
	

    /************************** Pile de mise à jour **************************/ 
    
    /* fonction permettant d'initialiser la pile 
     * plugin: le nom de votre plugin
     * action: l'action qui sera utilisé dans le fichier ajax du pulgin 
     * callback: fonction appelé coté client(JS) pour mettre à jour l'affichage 
     */ 
    public function initStackData() {
        nodejs::pushUpdate('tesla::initStackDataEqLogic', array('plugin' => 'tesla', 'action' => 'saveStack', 'callback' => 'displayEqLogic'));
    }
    
    /* fonnction permettant d'envoyer un nouvel équipement pour sauvegarde et affichage, 
     * les données sont envoyé au client(JS) pour être traité de manière asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function stackData($params) {
        if(is_object($params)) {
            $paramsArray = utils::o2a($params);
        }
        nodejs::pushUpdate('tesla::stackDataEqLogic', $paramsArray);
    }
    
    /* fonction appelé pour la sauvegarde asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function saveStack($params) {
        // inserer ici le traitement pour sauvegarde de vos données en asynchrone
        
    }

    /* fonction appelé avant le début de la séquence de sauvegarde */
    public function preSave() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion 
     * dans la base de données pour une mise à jour d'une entrée */
    public function preUpdate() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde après l'insertion 
     * dans la base de données pour une mise à jour d'une entrée */
    public function postUpdate() {
    }

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion 
     * dans la base de données pour une nouvelle entrée */
    public function preInsert() {

    }

    /* fonction appelé pendant la séquence de sauvegarde après l'insertion 
     * dans la base de données pour une nouvelle entrée */
    public function postInsert() {
        
    }

    /* fonction appelé après la fin de la séquence de sauvegarde */
    public function postSave() {
    	$state = $this->getCmd(null, 'battery_heater_on');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('battery_heater_on');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Battery Heater On', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('binary');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charge_port_door_open');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charge_port_door_open');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Charge Port Door Open', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('binary');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charging_state');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charging_state');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Charging State', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('string');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'battery_current');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('battery_current');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Battery Current', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('string');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'battery_level');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('battery_level');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(true);
    		$state->setName(__('Battery Level', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charger_voltage');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charger_voltage');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(true);
    		$state->setName(__('Charger Voltage', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charger_pilot_current');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charger_pilot_current');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(true);
    		$state->setName(__('Charger Pilot Current', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charger_actual_current');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charger_actual_current');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(true);
    		$state->setName(__('Charger Actual Current', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charger_power');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charger_power');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(true);
    		$state->setName(__('Charger Power', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'time_to_full_charge');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('time_to_full_charge');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Time to Full Charge', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('string');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charge_current_request');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charge_current_request');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Charge Current Request', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('string');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charge_current_request_max');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charge_current_request_max');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Charge Current Request Max', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('string');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charge_energy_added');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charge_energy_added');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Charge Energy Added', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'charge_miles_added_ideal');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('charge_miles_added_ideal');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Charge Miles Added Ideal', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'battery_range');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('battery_range');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Battery Range', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'est_battery_range');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('est_battery_range');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Estimated Battery Range', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	$state = $this->getCmd(null, 'ideal_battery_range');
    	if (!is_object($state)) {
    		$state = new teslaCmd();
    		$state->setLogicalId('ideal_battery_range');
    		$state->setIsVisible(1);
    		$state->setIsHistorized(false);
    		$state->setName(__('Ideal Battery Range', __FILE__));
    	}
    	$state->setType('info');
    	$state->setSubType('numeric');
    	$state->setEventOnly(1);
    	$state->setEqLogic_id($this->getId());
    	$state->save();
    	
    	log::add('tesla', 'Error', "Car saved");
    }

    /* fonction appelé avant l'effacement d'une entrée */
    public function preRemove() {
        
    }

    /* fonnction appelé après l'effacement d'une entrée */
    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class teslaCmd extends cmd {
    /******************************* Attributs *******************************/ 
    /* Ajouter ici toutes vos variables propre à votre classe */

    /***************************** Methode static ****************************/ 

    /*************************** Methode d'instance **************************/ 

    /* Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
    public function dontRemoveCmd() {
        return true;
    }
    */

    public function execute($_options = array()) {
    	log::add('tesla', 'Error', "in function execute");
    	throw new Exception(__("test dans execute", __FILE__));
    	
    	if (!isset($_options['title']) && !isset($_options['message'])) {
    		throw new Exception(__("Le titre ou le message ne peuvent être tous les deux vide", __FILE__));
    	}
    	$eqLogic = $this->getEqLogic();
    	//To be continued ...
    }

    /***************************** Getteur/Setteur ***************************/ 

    
}

?>
