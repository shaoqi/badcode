<?php
require('chinese.php');
require_once(ROOT_PATH."modules/borrow/borrow.class.php");
require_once(ROOT_PATH."modules/borrow/borrow.tender.php");
require_once(ROOT_PATH."modules/borrow/borrow.loan.php");
require_once(ROOT_PATH."modules/articles/articles.class.php");
$borrow_nid = $_REQUEST['nid'];
$result_head = borrowClass::GetNewView(array("borrow_nid"=>$borrow_nid,'tid'=>$_REQUEST['tid']));
if($result_head['sell']==0){
$result_tender = borrowTenderClass::GetTenderList(array("borrow_nid"=>$borrow_nid,"limit"=>"all"));
$result_repay = borrowLoanClass::GetRepayList(array("borrow_nid"=>$borrow_nid,"limit"=>"all","order"=>"order"));
$articles = articlesClass::GetPageOne(array("id"=>13));
$pdf=new PDF_Chinese('P', 'mm', 'A4');
$pdf->Open();
$pdf->AddPage();
$pdf->AddGBFont();
$pdf->SetFont('GB','B',20);
$pdf->Cell('80');

$pdf->Cell(80,15,'借款担保服务合同',1,0,'C');

$pdf->SetFont('GB','',10);
$pdf->Cell(100,30,'',0,1);

$pdf->Cell(100,5,'贷款号：'.$result_head['borrow_nid'],0,1);
$pdf->Cell(100,5,'借款人：'.$result_head['username'],0,1);
$pdf->Cell(100,5,'还款方式：'.$result_head['style_title'],0,1);
$pdf->Cell(100,5,'出借人：详见出借人名录',0,1);
$pdf->Cell(100,5,'签订日期：'.date("Y-m-d",$result_head['borrow_success_time']),0,1);

//出借人名录
$pdf->Cell(100,10,'',0,1);
$pdf->Cell(100,5,'1、出借人名录：',0,1);
$header=array('出借人','借款金额','借款期限','年利率','借款开始日','借款到期日','月截止还款日','总还款本息'); //设置表头
$data=array(); //设置表体
//$data[0] = array('wdf111','3000.00元','6个月','16.0%','2012-12-28','2013-06-28','每月28日','3141.54 元');
foreach($result_tender as $key=>$value){

	$data[$key] = array($value['username'],$value['account'],$value['borrow_period_name'],$value['borrow_apr'],date("Y-m-d",$result_head['borrow_success_time']),date("Y-m-d",$result_head['repay_last_time']),$result_head['repay_each_time'],$value['recover_account_all']);

}
$width=array(25,25,20,20,25,25,25,25); //设置每列宽度
for($i=0;$i<count($header);$i++){ //循环输出表头
	$pdf->Cell($width[$i],6,$header[$i],1,0,'C');
}
$pdf->Ln();
foreach($data as $row){//循环输出表体
	for($i=0;$i<count($header);$i++){
		$pdf->Cell($width[$i],6,$row[$i],1,0,'C');
	}
	$pdf->Ln();
}
$pdf->SetFont('GB','B',12);

	$total=array('总金额：',$result_head['account'],'',$result_head['borrow_apr'],date("Y-m-d",$result_head['borrow_success_time']),date("Y-m-d",$result_head['repay_last_time']),'总还款本息：',$result_head['repay_account_all']);

for($i=0;$i<count($total);$i++){ 
	$pdf->Cell($width[$i],6,$total[$i],1,0,'C');
}

//还款明细
$pdf->SetFont('GB','',10);
$pdf->Cell(60,15,'',0,1);
$pdf->Cell(60,5,'2、还款明细：',0,1);
$header=array('借款期数','年利率','应还时间','还款本息','还款本金','还款利息'); //设置表头
$data=array(); //设置表体

foreach($result_repay as $key=>$value){
	$data[$key] = array($value['repay_period'],$value['borrow_apr'],date("Y-m-d",$value['repay_time']),$value['repay_account'],$value['repay_capital'],$value['repay_interest']);
}
$width=array(25,25,35,35,35,35); //设置每列宽度
for($i=0;$i<count($header);$i++){ //循环输出表头
	$pdf->Cell($width[$i],6,$header[$i],1,0,'C');
}
$pdf->Ln();
foreach($data as $row){//循环输出表体
	for($i=0;$i<count($header);$i++){
		$pdf->Cell($width[$i],6,$row[$i],1,0,'C');
	}
	$pdf->Ln();
}
$articles = strip_tags($articles['contents']);
$contents = str_replace("&nbsp;", "", $articles);
$pdf->Cell(100,10,'',0,1);
$pdf->MultiCell(0, 4, $contents);
//$pdf->Cell(920,0,$articles,0, 1, 'C');
//$pdf->Image('themes/ryr/images/gongzhang.jpg',40);
$pdf->SetFont('GB','B',16);
//$pdf->Cell(180,0,'签订日期：'.date("Y-m-d",$result_head['borrow_success_time']),0,'','C');
/* $save_path = '../data/images/pdf/';
*/
$filename = $borrow_nid.'.pdf'; 
//I查看 D直接下载  F生成pdf文件
$pdf->Output($save_path.$filename, 'D');
}else{
	require(ROOT_PATH.'libs/tcpdf/config/lang/chi.php');
	require(ROOT_PATH.'libs/tcpdf/tcpdf.php');
	$img = ROOT_PATH.'themes/ryr/images/tuzhang.png';
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('rongerong.com');
	$pdf->SetTitle(iconv('gb2312','utf-8','债权转让及回购合同'));
	$pdf->SetSubject('rongerong.com');
	$pdf->SetKeywords('rongerong.com');
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('droidsansfallback', '', 9);
	$pdf->setFontSubsetting(false);
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	// Image example with resizing
	// add a page
	$pdf->AddPage();
	//$pdf->Image($img, '', '', 220, 220, 'PNG');
	$pdf->writeHTML(iconv('gb2312','utf-8',$result_head['content']),true, 0, true, 0);
	$pdf->lastPage();
	$filename = $borrow_nid.'.pdf'; 
	//I查看 D直接下载  F生成pdf文件
	$pdf->Output($save_path.$filename, 'D');
}