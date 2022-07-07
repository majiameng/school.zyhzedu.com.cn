<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Org\Util;
import("Org.Util.PHPExcel");
import("Org.Util.PHPExcel.Reader.Excel5");
import("Org.Util.PHPExcel.Reader.Excel2007");
class ExcelReader {
    /**
     * 读取excel
     * @param unknown_type $excelPath：excel路径
     * @param unknown_type $allColumn：读取的列数
     * @param unknown_type $sheet：读取的工作表
     */
    private $excelName;   //xls文件名，包括生成路径
    public $exam_year;   //年份
    public function __construct($name = '学员报名信息',$exam_year = '')
    {
        if($name !="")
        {
            $this->excelName = $name;
        }
        $this->exam_year = $exam_year;
    }
    /*通过PHPExcel类生成Excel文件
    *@param $data 包含excel文件内容的数组
    * @param $txArr 包含excel表头信息（中文)  例如array('编号',"姓名")
    * @param $txArrEn excel表头信息（英文） 例如array('id','username')
    * @param $excelVersion 生成excel文件的版本  可选值为other,2007
    * @param $width 单元格宽度，如果设置为‘auto’ 表示自适应宽度，也可是具体的数字，用来设置具体的单元格宽度
    * @renturn excel文件的绝对路径
    * **/
    public function getExcel($data,$titleArr,$width="auto",$excelVersion = "2007")
    {
        $txArr   = array_values($titleArr);
        $txArrEn = array_keys($titleArr);
        $excelObj = new \PHPExcel();

        $excelObj->setActiveSheetIndex(0);
        $objActSheet = $excelObj->getActiveSheet();
        if(count($txArr) != count($txArrEn) && count($txArrEn) != count($data['0']) && !empty($data)){
            echo "表头数组错误，请仔细检查！";
            exit();
        }
        /*确定表头宽度，将表头内容添加到excel文件里*/
        foreach($txArr as $key =>$value){
            $pos = $this->numToEn($key);
            // $objActSheet->getDefaultRowDimension()->setRowHeight(60);
            /*冻结标题*/
            //$objActSheet->freezePane($pos."1");
            /*自动填充到页面的宽度和高度*/
            // $objActSheet->getPageSetup()->setFitToWidth('1');
            // $objActSheet->getPageSetup()->setFitToHeight('1');

            $objActSheet->setCellValue($pos."1",$value);
            /*设置对齐方式*/
            $objActSheet->getStyle($pos."1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /*自动换行*/
            //$objActSheet->getStyle($pos."1")->getAlignment()->setWrapText(true);

            /*设置字体加粗*/
            $objActSheet->getStyle($pos."1")->getFont()->setBold(true);

            /*	设置行高	*/
            // $objActSheet->getRowDimension($pos)->setRowHeight(50);
            if($key==4){
                $objActSheet->getColumnDimension($pos)->setWidth(20);
            }
            else{
                $width == "auto"? $objActSheet->getColumnDimension($pos)->getAutoSize(true): $objActSheet->getColumnDimension($pos)->setWidth($width);
            }

        }
        /*将数据添加到excel里*/
        foreach($data as $key=>$value){
            foreach($txArrEn as $k => $val){
                $pos = $this->numToEn($k);
                $objActSheet->getStyle($pos.($key+2))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);

                $objActSheet->setCellValue($pos.($key+2)," ".$value[$val]);  //在写入Excels单元格的内容之前加一个空格，防止长数字被转化成科学计数法
                $objActSheet->getStyle($pos.($key+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            }
        }
        /*判断生成excel文件版本*/
        $objWriter = "";
        if($excelVersion == "other"){
            $objWriter = new \PHPExcel_Writer_Excel5($excelObj);
        }
        if($excelVersion == "2007"){
            $objWriter = new \PHPExcel_Writer_Excel2007($excelObj);
        }
        // ob_end_clean();
        $name = $this->excelName . date('Y-m-d').'.xlsx';
        // $filePath = WEB_PATH . DIRECTORY_SEPARATOR .$name;
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type:application/download");
        header('Content-Disposition: attachment;filename="'.$name.'"');
        header('Cache-Control: max-age=0');
        header ('Pragma: public');
        $objWriter->save('php://output');
        // $objWriter->save($filePath);
        // readfile($filePath);
        // unlink($filePath);
        exit;
    }

    public function export($data,$titleArr,$tpl,$width="auto",$excelVersion = "2007",$is_down = true)
    {
        $txArrEn = array_keys($titleArr);
        $excelObj = new \PHPExcel();
        if(!file_exists($tpl)) {
            echo '模板文件不存在';exit;
        }
        $objWriter = $objReader = "";
        // $objReader = new \PHPExcel_Reader_Excel5($excelObj);
        $objReader = new \PHPExcel_Reader_Excel2007();
        $PHPExcel = $objReader->load($tpl);
        $objActSheet = $PHPExcel->getActiveSheet();

        if(empty($txArrEn) || empty($data)){
            echo "表头数组错误，请仔细检查！";
            exit();
        }
        $start = 2;
        /*将数据添加到excel里*/
        foreach($data as $key=>$value){
            foreach($txArrEn as $k => $val){
                $pos = $this->numToEn($k);
                // $objActSheet->getStyle($pos.$start)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                if($val == 'avatar' || $val == 'card_front' || $val == 'card_behind' || $val == 'study_img' || $val == 'operation_img' || $val == 'train_img' || $val == 'skill_img' || $val == 'graduation_img') {//file_exists($base['avatar'])
                    if (empty($value[$val])) {
                        continue;
                    }
                    if(is_array($value[$val])){
                        $start_image = $start;
                        foreach ($value[$val] as $va){
                            //设置列宽
                            $objActSheet->getColumnDimension($pos)->setWidth('15');
                            // $objActSheet->getStyle($pos.$start)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            // $objActSheet->getStyle($pos.$start)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            // 设置行高
                            $objActSheet->getRowDimension($start)->setRowHeight(100);
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath($va); //图片引入位置

                            $objDrawing->setCoordinates($pos.$start_image); //图片添加位置
                            $objDrawing->setOffsetX(2);
                            $objDrawing->setOffsetY(4);
                            // $objDrawing->setResizeProportional(true);
                            $objDrawing->setHeight(154);
                            $objDrawing->setWidth(100);
                            $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
                            // $objActSheet->getColumnDimension($pos.$start)->setWidth('auto');
                            // $objActSheet->getColumnDimension($pos.$start)->getAutoSize(true);
                            $start_image += 3;
                        }
                        continue;
                    }else{
                        //设置列宽
                        $objActSheet->getColumnDimension($pos)->setWidth('15');
                        // $objActSheet->getStyle($pos.$start)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        // $objActSheet->getStyle($pos.$start)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        // 设置行高
                        $objActSheet->getRowDimension($start)->setRowHeight(100);
                        $objDrawing = new \PHPExcel_Worksheet_Drawing();
                        $objDrawing->setPath($value[$val]); //图片引入位置

                        $objDrawing->setCoordinates($pos.$start); //图片添加位置
                        $objDrawing->setOffsetX(2);
                        $objDrawing->setOffsetY(4);
                        // $objDrawing->setResizeProportional(true);
                        $objDrawing->setHeight(154);
                        $objDrawing->setWidth(100);
                        $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
                        // $objActSheet->getColumnDimension($pos.$start)->setWidth('auto');
                        // $objActSheet->getColumnDimension($pos.$start)->getAutoSize(true);
                        continue;
                    }
                }
                $objActSheet->setCellValue($pos.$start," ".$value[$val]);  //在写入Excels单元格的内容之前加一个空格，防止长数字被转化成科学计数法
                // $objActSheet->getStyle($pos.$start)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            }
            $start++;
        }
        /*判断生成excel文件版本*/
        $objWriter = "";
        if($excelVersion == "other"){
            $objWriter = new \PHPExcel_Writer_Excel5($PHPExcel);
        }
        if($excelVersion == "2007"){
            $objWriter = new \PHPExcel_Writer_Excel2007($PHPExcel);
        }
        //ob_end_clean();
        // ob_clean();
        $name = $this->excelName .'.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        if($is_down) {
            // header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'"');
            header('Cache-Control: max-age=0');
            header("Content-Type:application/download");
            $objWriter->save('php://output');exit;
        }else{
            $objWriter->save(WEB_PATH.'/upload/excel/'.$name);
            return true;
        }

    }


    public function getExcelBao($data,$width="auto",$excelVersion = "other",$is_down = true)
    {
        $excelObj = new \PHPExcel();
        if(empty($data)){
            echo "导出信息为空";exit;
        }
        $objWriter = $objReader = "";

        /*判断生成excel文件版本*/
        /*
        if($excelVersion == "other"){
            $objReader = new \PHPExcel_Reader_Excel5($excelObj);
        }
        if($excelVersion == "2007"){
            $objReader = new \PHPExcel_Reader_Excel2007($excelObj);
        }
        */
        // $objReader = new \PHPExcel_Reader_Excel5($excelObj);
        $objReader = new \PHPExcel_Reader_Excel2007($excelObj);
        $excel_path = APP_PATH . "../rh.xlsx";
        $PHPExcel = $objReader->load($excel_path);
        $objActSheet = $PHPExcel->getActiveSheet();

        $title = $this->exam_year . $objActSheet->getCellByColumnAndRow()->getValue();

        $base = $data['base'];
        $objActSheet->setTitle ($base['name'].$this->excelName);
        // $objActSheet->getDefaultRowDimension()->setRowHeight(100);
        $objActSheet->setCellValue("A1",$title);
        $objActSheet->setCellValue("B3",$base['name']);
        $objActSheet->setCellValue("D3",$base['sex']);
        $objActSheet->setCellValue("G3",$base['birth']);

        $objActSheet->setCellValue("B4",$base['house_address']);
        $objActSheet->setCellValue("D4",$base['political_status']);
        $objActSheet->setCellValue("G4",$base['health']);

        $objActSheet->setCellValue("B5",$base['marriage']);
        $objActSheet->setCellValue("D5",$base['height']);
        $objActSheet->setCellValue("G5",$base['weight']);

        $objActSheet->setCellValue("B6",$base['nation']);
        $objActSheet->setCellValue("D6",$base['education']);
        $objActSheet->setCellValue("E7",$base['full_time']);
        $objActSheet->setCellValue("G6",$base['profession']);

        $objActSheet->setCellValue("B8",$base['school']);
        $objActSheet->setCellValue("B9",$base['unit']);
        $objActSheet->setCellValue("G9",$base['job']);
        $objActSheet->setCellValue("B10",' '.$base['card']);
//        $objActSheet->setCellValue("G10",$base['is_huibi']);
//        $objActSheet->setCellValue("I10",$base['is_famous']);
        $objActSheet->setCellValue("G10",$base['is_famous']);

        // $objActSheet->getRowDimension("B9")->setRowHeight(50);
        // $objActSheet->getRowDimension("B8")->setRowHeight(50);

        if(file_exists($base['avatar'])){
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($base['avatar']); //图片引入位置
            $objDrawing->setCoordinates('H3'); //图片添加位置
            $objDrawing->setOffsetX(2);
            $objDrawing->setOffsetY(4);
            // $objDrawing->setRotation(25); //旋转
            // $objDrawing->setWidth(100);
            // $objDrawing->setHeight(130);

            // $objDrawing->setHeight(195);
            // $objDrawing->setWidth(150);

            $objDrawing->setResizeProportional(false);
            $objDrawing->setHeight(200);
            $objDrawing->setWidth(130);

            $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
            // $objActSheet->getColumnDimension('H3')->setWidth('auto');
            $objActSheet->getColumnDimension('H3')->getAutoSize(true);
        }
        $objActSheet->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objActSheet->getStyle("H")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $pos = 13;
        if(!empty($data['edu'])){  //教育经历
            $num = count($data['edu']);
            if($num > 1) {
                $objActSheet->insertNewRowBefore($pos, $num-1);   //在行6前添加10行
            }
            foreach($data['edu'] as $k=>$r){
                $objActSheet->mergeCells("B".$pos . ":E".$pos);//A28:B28合并
                $objActSheet->setCellValue("A".$pos,$r['start_time'].'-'.$r['end_time']);
                $objActSheet->setCellValue("B".$pos,$r['school']);
                $objActSheet->setCellValue("F".$pos,$r['profession']);
                $objActSheet->setCellValue("G".$pos,$r['education']);
                $objActSheet->setCellValue("H".$pos,$r['full_time']);
                $objActSheet->setCellValue("I".$pos,$r['degree']);
                $pos++;
            }
        }
        $pos+=2;
        if(!empty($data['work'])){  //教育经历
            $num = count($data['work']);
            if($num > 1) {
                $objActSheet->insertNewRowBefore($pos, $num-1);   //在行6前添加10行
            }
            foreach($data['work'] as $k=>$r){
                $objActSheet->mergeCells("B".$pos . ":F".$pos);
                $objActSheet->mergeCells("G".$pos . ":I".$pos);
                $objActSheet->setCellValue("A".$pos,$r['start_time'].'-'.$r['end_time']);
                $objActSheet->setCellValue("B".$pos,$r['company']);
                $objActSheet->setCellValue("G".$pos,$r['job']);
                $pos++;
            }
        }
        $pos+=1;$skill = $data['skill'];	// 专业技能
        $objActSheet->setCellValue("B".$pos,$skill['certificate']);
        $objActSheet->setCellValue("B".($pos+1),$skill['other']);
        $objActSheet->setCellValue("B".($pos+2),$skill['rewards']);
        $pos+=5;
        if(!empty($data['family'])){  //家庭情况
            $num = count($data['family']);
            if($num > 1) {
                $objActSheet->insertNewRowBefore($pos, $num-1);   //在行6前添加10行
            }
            foreach($data['family'] as $k=>$r){
                $objActSheet->mergeCells("C".$pos . ":G".$pos);
                $objActSheet->mergeCells("H".$pos . ":I".$pos);
                $objActSheet->setCellValue("A".$pos,$r['relation']);
                $objActSheet->setCellValue("B".$pos,$r['name']);
                $objActSheet->setCellValue("C".$pos,$r['work_place']);
                $objActSheet->setCellValue("H".$pos,$r['job']);
                $pos++;
            }
        }

        $pos+=1;	// 专业技能
        $objActSheet->setCellValue("B".$pos,$base['address']);
        $objActSheet->setCellValue("G".$pos,$base['postcode']);

        $objActSheet->setCellValue("B".($pos+1),$base['mobile']);
        $objActSheet->setCellValue("G".($pos+1),$base['tel']);

        $objActSheet->setCellValue("B".($pos+2),$base['email']);

        $objActSheet->setCellValue("B28",$base['is_free']);
        $objActSheet->setCellValue("B29",$base['is_operation']);
        $objActSheet->setCellValue("B30",$base['is_train']);

        $cellName = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

        $pos = $pos+6;	// 身份证正反面
        if(!empty($base['card_front'])){
            $objActSheet->setCellValue("A".$pos,"身份证正反面:	");

            $cell = $cellName[(1)];
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($base['card_front']); //图片引入位置
            $objDrawing->setCoordinates($cell.$pos); //图片添加位置
            $objDrawing->setOffsetX(2);
            $objDrawing->setOffsetY(4);
            $objDrawing->setResizeProportional(false);
            $objDrawing->setHeight(145);
            $objDrawing->setWidth(200);
            $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中

            $cell = $cellName[(5)];
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($base['card_behind']); //图片引入位置
            $objDrawing->setCoordinates($cell.$pos); //图片添加位置
            $objDrawing->setOffsetX(2);
            $objDrawing->setOffsetY(4);
            $objDrawing->setResizeProportional(false);
            $objDrawing->setHeight(145);
            $objDrawing->setWidth(200);
            $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
        }
        
        $pos = $pos+6;	// 毕业证书
        if(!empty($data['graduation_img'])){
            $objActSheet->setCellValue("A".$pos,"毕业证书:	");
            foreach ($data['graduation_img'] as $key=>$graduation_img){
                $cell = $cellName[(4*$key+1)];
                $objDrawing = new \PHPExcel_Worksheet_Drawing();
                $objDrawing->setPath($graduation_img); //图片引入位置
                $objDrawing->setCoordinates($cell.$pos); //图片添加位置
                $objDrawing->setOffsetX(2);
                $objDrawing->setOffsetY(4);
                $objDrawing->setResizeProportional(false);
                $objDrawing->setHeight(145);
                $objDrawing->setWidth(200);
                $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
            }
        }

        $pos = $pos+6;	// 学信网照片
        $cell = $cellName[1];
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setPath($base['study_img']); //图片引入位置
        $objDrawing->setCoordinates($cell.$pos); //图片添加位置
        $objDrawing->setOffsetX(2);
        $objDrawing->setOffsetY(4);
        $objDrawing->setResizeProportional(false);
        $objDrawing->setHeight(145);
        $objDrawing->setWidth(200);
        $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中

        $pos = $pos+6;	// 执业资格证
        if(!empty($data['resume_qualifications']['operation_img'])){
            $cell = $cellName[1];
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($data['resume_qualifications']['operation_img']); //图片引入位置
            $objDrawing->setCoordinates($cell.$pos); //图片添加位置
            $objDrawing->setOffsetX(2);
            $objDrawing->setOffsetY(4);
            $objDrawing->setResizeProportional(false);
            $objDrawing->setHeight(145);
            $objDrawing->setWidth(200);
            $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
        }

        $pos = $pos+6;	// 规培证
        if(!empty($data['resume_qualifications']['train_img'])) {
            $cell = $cellName[1];
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($data['resume_qualifications']['train_img']); //图片引入位置
            $objDrawing->setCoordinates($cell.$pos); //图片添加位置
            $objDrawing->setOffsetX(2);
            $objDrawing->setOffsetY(4);
            $objDrawing->setResizeProportional(false);
            $objDrawing->setHeight(145);
            $objDrawing->setWidth(200);
            $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
        }

        $pos = $pos+6;	// 专业技能 / 其他
        if(!empty($data['skill_img'])){
            $objActSheet->setCellValue("A".$pos,"技能证书/其他:	");
            foreach ($data['skill_img'] as $key=>$graduation_img){
                $cell = $cellName[(4*$key+1)];
                $objDrawing = new \PHPExcel_Worksheet_Drawing();
                $objDrawing->setPath($graduation_img); //图片引入位置
                $objDrawing->setCoordinates($cell.$pos); //图片添加位置
                $objDrawing->setOffsetX(2);
                $objDrawing->setOffsetY(4);
                $objDrawing->setResizeProportional(false);
                $objDrawing->setHeight(145);
                $objDrawing->setWidth(190);
                $objDrawing->setWorksheet($objActSheet);//把图片写到当前的表格中
            }
        }

        /*判断生成excel文件版本*/
        if($excelVersion == "other"){
            $name = $this->excelName . '.xls';
            $objWriter = new \PHPExcel_Writer_Excel5($PHPExcel);
        }
        if($excelVersion == "2007"){
            $name = $this->excelName . '.xlsx';
            $objWriter = new \PHPExcel_Writer_Excel2007($PHPExcel);
        }
        $no = !empty($base['no'])?$base['no']:time();
        $name = $base['unit'].'-'.$base['job'].'-'.$base['name'].'-'.$no.' '.$name;

        // $objWriter = new \PHPExcel_Writer_Excel2007($PHPExcel);

        if($is_down) {
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.'"');
            header('Cache-Control: max-age=0');
            header("Content-Type:application/download");
            $objWriter->save('php://output');
            exit;
        }

        $objWriter->save(WEB_PATH . DIRECTORY_SEPARATOR . 'upload'.DIRECTORY_SEPARATOR .'bao'. DIRECTORY_SEPARATOR .$name);
        return true;
    }

    /**
    根据给定的数字生成至多两位对应EXCEL文件列的字母
     */
    private function numToEn($num){
        $asc = 0;
        $en = "";
        $num =(int)$num+1;
        if($num<=26){                      //判断指定的数字是否需要用两个字母表示{
            if((int)$num<10){
                $asc = ord($num);
                $en =chr($asc+16);
            }
            else{
                $num_g = substr($num,1,1);
                $num_s = substr($num,0,1);
                $asc = ord($num_g);
                $en =chr($asc+16+10*$num_s);
            }
        }
        else{
            $num_complementation = floor($num/26);
            $en_q = $this->numToEn($num_complementation-1);
            $en_h = $num%26 != 0 ? $this->numToEn($num-$num_complementation*26-1):"A";
            $en = $en_q.$en_h;
        }
        return $en;
    }
}