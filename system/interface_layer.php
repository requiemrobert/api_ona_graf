<?php
include 'DB_Model.php';
/**
* 
*/

class InterfaceLayer extends DB_Model 
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function route($dataArray){
       	   
   
		  switch ($dataArray['rc']):
		      case 'get_login':
					
		        echo $this->get_login($dataArray['data']);
		            
		      break;

		      case 'get_menu':
		      	
		        echo $this->get_menu($dataArray['data']);
		            
		      break;

		      case 'registrar_cliente':

		        echo $this->registrar_cliente($dataArray['data']);
		            
		      break;

		      case 'consultar_cliente':

		        echo $this->consultar_cliente();
		            
		      break;

		      case 'modificar_cliente':

		        echo $this->modificar_cliente($dataArray['data']);
		            
		      break;

		      case 'eliminar_cliente':

		        echo $this->eliminar_cliente($dataArray['data']);
		            
		      break;

		      case 'ping':

		        echo $this->ping($dataArray['data']);
		            
		      break;
		      
		      default:
		          
		        echo "No response del WS!!!";//$this->no_response();

		      break;
	      endswitch;

    }	


}