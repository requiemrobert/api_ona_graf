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

		      case 'registrar_Stock_MP':

		       echo $this->registrar_Stock_MP($dataArray['data']);
		            
		      break;

		      case 'consultar_Stock_MP':		      	

		        echo $this->consultar_Stock_MP();
		            
		      break;

		      case 'modificar_Stock_MP':

		        echo $this->modificar_Stock_MP($dataArray['data']);
		            
		      break;

		      case 'eliminar_Stock_MP':

		        echo $this->eliminar_Stock_MP($dataArray['data']);
		            
		      break;

		      case 'registrar_proveedor':

		        echo $this->registrar_proveedor($dataArray['data']);
		            
		      break;

		      case 'consultar_proveedor':

		        echo $this->consultar_proveedor();
		            
		      break;

		      case 'consultar_disponible_MP':

		        echo $this->consultar_disponible_MP();
		            
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