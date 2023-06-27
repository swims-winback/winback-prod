<?php
namespace App\Controller;

require_once dirname(__FILE__, 3).'/configServer/config.php';
require_once dirname(__FILE__, 3).'/configServer/dbConfig.php'; 
//require_once './configServer/dbConfig.php';
//include_once "../softUpdate/Ressource/Config/dbConfig.php";
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class SnCheckController extends AbstractController
{
#[Route('/{_locale<%app.supported_locales%>}/snCheck', name: 'sn_check')]
	public function index()
	{
		if(isset($_GET['sn'])){
			$dbhandle = mysqli_connect(HOSTNAME, ADMIN, PWD, 'winback');
			
			if(mysqli_connect_errno()){
				echo 'Connection to database failed: '.mysqli_connect_error();
				exit();
			}
			$sn=$_GET['sn'];
			$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$sn;
			$res=mysqli_query($dbhandle, $request);
			
			if (mysqli_num_rows($res)==0){
				echo ('oh no, not found');
				if($pos=strpos($sn,'-',0)){
					echo ('1'.$sn);
					$sn=str_replace('-','',$sn);
					echo ('1'.$sn);				
					$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$sn;
					$res=mysqli_query($dbhandle, $request);
				}
				if($pos=strpos($sn,'_',0)){
					echo ('2'.$sn);
					$sn=str_replace('_','',$sn);
					echo ('2'.$sn);				
					$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$sn;
					$res=mysqli_query($dbhandle, $request);
				}
				else{
					for($i=1;$i<strlen($sn);$i++){
						$snTemp=substr($sn,0,$i).'-'.substr($sn,$i,strlen($sn));				
						$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$snTemp;
						$res=mysqli_query($dbhandle, $request);
						if (mysqli_num_rows($res)!=0)break;
					}
					if (mysqli_num_rows($res)==0){
						for($i=1;$i<strlen($sn);$i++){
							$snTemp=substr($sn,0,$i).'_'.substr($sn,$i,strlen($sn));				
							$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$snTemp;
							$res=mysqli_query($dbhandle, $request);
							if (mysqli_num_rows($res)!=0)break;
						}
					}
				}
			}
				
			if ($res) {
				$emparray = array();
				while($row =mysqli_fetch_assoc($res))
				{
					$emparray[] = $row;
				}
				if(!empty($emparray)){
					echo json_encode($emparray);
					return new Response(true);
				}
				else header("HTTP/1.0 404 Not Found");
				return new Response(false);
			}else{
				header("HTTP/1.0 404 Not Found");
				return new Response(false);
			}
		}else{
			header("HTTP/1.0 404 Not Found");
			return new Response(false);
		}
	}
}