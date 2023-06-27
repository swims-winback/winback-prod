<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 

include_once "../configServer/dbConfig.php";

	if(isset($_GET['sn'])){
        $dbhandle = mysqli_connect('localhost', 'root', '', 'winback');
        
        if(mysqli_connect_errno()){
            echo 'Connection to database failed: '.mysqli_connect_error();
            exit();
        }
		$sn=$_GET['sn'];
		$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$sn;
        $res=mysqli_query($dbhandle, $request);
		
		if (mysqli_num_rows($res)==0){
			if($pos=strpos($sn,'-',0)){
				$sn=str_replace('-','',$sn);				
				$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$sn;
				$res=mysqli_query($dbhandle, $request);
			}else if($pos=strpos($sn,'_',0)){
				$sn=str_replace('_','',$sn);				
				$request='SELECT * FROM '.SN_TABLE.' WHERE SN='.$sn;
				$res=mysqli_query($dbhandle, $request);
			}else{
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
			if(!empty($emparray))echo json_encode($emparray);
			else header("HTTP/1.0 404 Not Found");
		}else{
			header("HTTP/1.0 404 Not Found");
		}
	}else{
		header("HTTP/1.0 404 Not Found");
	}

