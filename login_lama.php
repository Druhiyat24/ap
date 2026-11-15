<?php
/* Halaman login TAMPILAN LAMA (sebelum restyle Nov 2026).
   Dipakai index.php kalau TAMPILAN_BARU = false (lihat tampilan.php).
   $next sudah disiapkan index.php - fitur "kembali ke halaman terakhir" tetap jalan. */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>
		SIGNAL BIT | Log in</title>
  <link rel="icon" type="image/jpeg" href="include/icon2.jpg">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/util.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/main.css">
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="include/img-01.png" alt="-">
					<br>
					<h5>
					Jl. Raya Rancaekek - Majalaya No. 289<br>Desa. Solokan Jeruk Kec. Solokan Jeruk<br>Bandung - Jawa Barat					</h5>
				</div>

				<form class="login100-form validate-form" method="post" action="auth.php">
					<span class="login100-form-title">
						<img src="images/logo_s.jpg" width="100%" alt="-">					</span>
					<?php if ($next !== '') { ?>
					<p style="color:#b45309; font-size:13px; margin-bottom:12px;">Your session has expired. Log in again to return to the page you were on.</p>
					<input type="hidden" name="next" value="<?php echo htmlspecialchars($next, ENT_QUOTES); ?>">
					<?php } ?>
					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="text" name="user" placeholder="User Name">
						<span class="symbol-input100">
							<i class="fa fa-address-book" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="pass" placeholder="Password">
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<select class="input100" style='width: 100%;' name='txtbahasa'>
				          <option>Indonesia</option>
				          <option>English</option>
				          <option>Korean</option>
				        </select>
				        <span class="symbol-input100">
							<i class="fa fa-sign-language" aria-hidden="true"></i>
						</span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">Login</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</body>
</html>
