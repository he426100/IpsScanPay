<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>扫码支付</title>
<link href="source/style-05.css" rel="stylesheet" />
</head>
<?php
require_once ("IpsPay.Config.php");
require_once ("lib/IpsPayRequest.class.php");
require_once ("lib/IpsPayVerify.class.php");
 
/**
 * ************************请求参数*************************
 */


//商户号
$merCode = $_POST['merCode'];
//商户账户号
$merAccount = $_POST['merAccount'];
//商户名
$merMerName = '';
//商户订单号
$merBillNo = $_POST['merBillNo'];
//支付方式
$gatewayType = $_POST['gatewayType'];
//订单日期
$orderDate = $_POST['orderDate'];
//订单金额
$amount = $_POST['amount'];
//订单有效期
$billEXP = $_POST['billEXP'];
//商品名称
$goodsName = $_POST['goodsName'];
//商户数据包
$attach = $_POST['attach'];
//异步S2S返回
$serverUrl = $_POST['serverUrl'];
//加密方式
$retEncodeType = '17';
//币种
$currencyType = '156';
//语言
$lang ='GB';

$MsgId = '00001';
/************************************************************/

//构造要请求的参数数组
$parameter = array(
    "MsgId"	    => $MsgId,
    "ReqDate"	    => date("YmdHis"),
    "MerCode"	    => $merCode,
    "MerName"	    => $merMerName,
    "Account"	    => $merAccount,
    "MerBillNo"	    => $merBillNo,
    "GatewayType"	    => $gatewayType,
    "Date"	        => $orderDate,
    "RetEncodeType"	        => $retEncodeType,
    "CurrencyType"	        => $currencyType,
    "Amount"	    => $amount,
    "BillEXP"	=> $billEXP,
    "GoodsName"	    => $goodsName,
    "ServerUrl"	    => $serverUrl,
    "Lang"	    => $lang,
    "Attach"	    => $attach 
);
//建立请求
$ipspayRequest = new IpsPayRequest($ipspay_config);
$html_text = $ipspayRequest->buildRequest($parameter);
$xmlResult = new SimpleXMLElement($html_text);
$strRspCode = $xmlResult->GateWayRsp->head->RspCode;
if($strRspCode == "000000")
{
    //返回报文验签
    $ipspayVerify = new IpsPayVerify($ipspay_config);
    $verify_result = $ipspayVerify->verifyReturn($html_text);
    // 验证成功
    if ($verify_result) { 
        $strQrCodeUrl = $xmlResult->GateWayRsp->body->QrCode;
        $message ="交易成功";
    } else {
        $message ="验签失败";
    }
}else{
    $message = $xmlResult->GateWayRsp->head->RspMsg;
}
 
?>
<body> 
      <?php if("交易成功" == $message){?>
      <div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫码支付</div><br/>
	  <div style="overflow: hidden">
      <img alt="扫码支付" src="qrcode.php?data=<?php echo urlencode($strQrCodeUrl);?>" class="codeimg2" style="width:150px;height:150px;"/>
      </div>
      <?php }else {?>
        <div class="roll-out-container">
        <ul>
			<li><span class="set-title">交易结果</span>
                <span class="set-label"><?php echo $message?></span>
            </li>
        </ul>
    </div>
     <?php }?>
</body>
</html>