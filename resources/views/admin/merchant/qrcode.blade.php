<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>QR</title>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,500i,700,700i" rel="stylesheet">
<style>
body > iframe {
	display: none;
}
body {
float: left;
width: 100%;
margin: 0 auto;
font-family: 'Roboto', sans-serif;
box-sizing: border-box;
border: 15px solid #fff;
}
.bg-red {
	float: left;
	width: 100%;
	background: #DE1F2B;
	padding: 15px;
	box-sizing: border-box;
}
.bg-white {
	float: left;
	width: 100%;
	margin: 0 auto;
	padding: 15px;
	box-sizing: border-box;
	background: #fff;
}
.qr-wrap {
width: 100%;
margin: 0 auto;
padding: 0;
text-align: center;
box-sizing: border-box;
max-width: 480px;
}
.qr-title {
font-size: calc(30px + 2vw);
font-weight: 700;
color: #fff;
letter-spacing: 1px;
margin-top: 3vh;
}

.qr-btn a {
width: auto;
margin: 0 auto;
display: inline-block;
border: 1px solid #000;
padding: 4px 15px;
border-radius: 4px;
font-size: calc(12px + 0.5vw);
}
.qr-footer {
float: left;
width: 100%;
margin: 0 auto;
padding: 0;
}
.img-sec {
	float: left;
	width: 100%;
	margin: 0 0 10px;
}
.img-sec img {
	max-height: 80px;
	width: auto;
}

img {
    max-width: 100%;
}
.qr-box {
	margin-bottom: 0;
	float: left;
	width: 100%;
}

.scan-text {
	float: left;
	width: 100%;
	color: #fff;
	margin: 0 auto 15px;
}
.scan-text h1 {
	font-size: 35px;
	font-weight: 900;
	margin: 0;
}
.scan-text h3 {
	font-size: 17px;
	margin: 5px 0;
	letter-spacing: 0.5px;
}
.scan-text h4 {
	font-size: 18px;
	letter-spacing: 0.6px;
	margin: 0;
}
.qr-btn {
	float: left;
	width: 100%;
	margin-top: 15px;
}
.flex-box {
	display: flex;
	align-items: center;
	text-align: center;
	justify-content: center;
}
.power {
	float: left;
	width: 50%;
	padding: 0 15px;
	text-align: center;
	box-sizing: border-box;
}
.power .myma-logo {
	max-height: 70px;
	width: auto;
}
.power span {
	width: 100%;
	text-align: left;
	display: inline-block;
	font-weight: 600;
	margin-bottom: 5px;
	padding: 0 5px;
}
.power .flexm-logo {
	max-width: 200px;
	max-height: 40px;
	padding-right: 35px;
	box-sizing: border-box;
}

@media print {
         body {background-color: #1a4567 !important;
-webkit-print-color-adjust: exact; }
      }
    </style>
</head>

<body>
  <div class="bg-red">
    <div class="qr-wrap">
        <div class="qr-box">
            <div class="img-sec">
              <img src="{{ static_file('signup/images/myMALogo - Copy.png') }}">
            </div>
            <div class="scan-text">
              <h1>Scan to Pay</h1>
              <h3>{{ $item->merchant?$item->merchant->merchant_name:$item->merchant_name}}</h3>
              <h4>{{ $item->location }}</h4>
            </div>
        </div>
        <div class="qr-box">
            <img src="{{ static_file($item->qr_code) }}">
        </div>
    </div>
  </div>
  <div class="bg-white">
    <div class="qr-wrap">
      <div class="qr-footer">
          <div class="flex-box">
            <div class="power">
              <img class="myma-logo" src="{{ static_file('signup/images/myMALogo - Copy.png') }}">
            </div>
            <div class="power">
              <span>Powered By</span>
              <img class="flexm-logo" src="{{ static_file('images/flexMpay_final1.png') }}">
            </div>
          </div>
          <div class="qr-btn">
              <a>ACCEPTED HERE</a>
          </div>
      </div>
    </div>
  </div>
</body>

</html>
