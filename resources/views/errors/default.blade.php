<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
  <title>MyMA</title>
  <style>
    body {
    	font-family: 'Roboto', sans-serif;
    	background: url("{{ static_file('images/bg.png') }}");
    	margin: 0;
    	background-size: cover;
    	padding: 0;
    	text-align: center;
    }
    .back-screen {
    	float: left;
    	width: 100%;
    	margin: 0 auto;
    	position: relative;
    	height: 100vh;
    	display: flex;
    	align-items: center;
    	justify-content: center;
    }
    .msg-box {
	display: inline-block;
	width: auto;
}
h1 {
	display: inline-block;
	margin: 0 auto 10px;
	width: 100%;
}
img {
	max-height: 100px;
}
h3 {
	margin: 0 auto;
	display: inline-block;
	font-size: 28px;
	font-weight: 400;
	color: #B90E3B;
}
h6 {
	font-size: 17px;
	font-weight: 500;
	margin: 10px 0 0;
}
p {
	color: #666;
	font-weight: 400;
	font-size: 16px;
	margin: 8px 0 0;
}

  </style>
</head>
<body>
  <div class="back-screen">
    <div class="msg-box text-center">
      <h1><img src="{{ static_file('signup/images/myMALogo - Copy.png') }}"></h1>
      <h3>Will be right back...</h3>
      <h6>Thank you for your patience.</h6>
      <p>Our engineers are working quickly to <br>resolve the issue.</p>
    </div>
  </div>

</body>
</html>
