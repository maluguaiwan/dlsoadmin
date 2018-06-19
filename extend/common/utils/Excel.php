<?php

namespace common\utils;

use think\File;
use think\Loader;
class Excel {
	/**
	 * 
	 * @param File $file
	 * @param number $titleRowCount 标题行数量
	 */
	public static function readToArray(File $file,$titleRowCount=1){
		Loader::import('PHPExcel.PHPExcel');
		Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');
		$extension = strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION));
		$objPHPExcel = \PHPExcel_IOFactory::load($file->getPathname());
		$data = $objPHPExcel->getActiveSheet()->toArray(null,true,true,false);
		for ($i=0;$i<$titleRowCount;$i++){
			unset($data[$i]);
		}
		foreach ($data as $k=>$row){
			foreach ($row as $kk=>$v){
				$row[$kk]=trim($v);
			}
			$data[$k]=$row;
			if(empty($row[0])){
				unset($data[$k]);
			}
		}
		return array_values($data);
	}
	
	public static function report($data,$filename,$properties=['creator'=>'精准云销客']){
		Loader::import('PHPExcel.PHPExcel');
		Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');
		$objPHPExcel = new \PHPExcel();
		$defined=['creator','last_modified_by','title','subject','description','keywords','category'];
		foreach ($properties as $key=> $property){
			$key=Loader::parseName($key, 1);
			$method='set'.$key;
			$objPHPExcel->getProperties()->$method($property);
		}
		$objPHPExcel->getActiveSheet()->fromArray($data);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}

?>