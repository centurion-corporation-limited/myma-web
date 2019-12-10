<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Merchant Page</title>
<script src="https://uat2.enets.sg/GW2/js/jquery-3.1.1.min.js" type="text/javascript"></script>
<script src="https://uat2.enets.sg/GW2/pluginpages/env.jsp"></script>
<script type="text/javascript" src="{{ static_file('/js/enets/apps.js') }}"></script>
<!-- <script type="text/javascript" src="https://uat2.enets.sg/GW2/js/apps.js"></script> -->
</head>
<body>
    <form>
    <input type="hidden" id="txnReq" name="txnReq" value='{{ $txnReq }}'>
    <input type="hidden" id="keyId" name="keyId" value='{{ $KEY_ID }}'>
    <input type="hidden" id="hmac" name="hmac" value='{{ $HMAC }}'>
    <div id="anotherSection">
        <fieldset>
            <div id="ajaxResponse"></div>
        </fieldset>
    </div>
</form>
<script>
    window.onload = function() {
        var txnReq = document.forms[0].txnReq.value;
        var keyId = document.forms[0].keyId.value; // once api key is available,                       assign a value
        var hmac = document.forms[0].hmac.value; // once hmac is available, assign a value
        sendPayLoad(txnReq, hmac, keyId);
    };
</script>

</body>
</html>
