<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo isset($sub_judul)?APP_INFO['name'].' - '.$sub_judul:APP_INFO['name']; ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="<?php echo base_url('assets/pengguna/') ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url('assets/pengguna/') ?>bower_components/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo base_url('assets/pengguna/') ?>bower_components/Ionicons/css/ionicons.min.css">
	<link rel="stylesheet" href="<?php echo base_url('assets/pengguna/') ?>dist/css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?php echo base_url('assets/pengguna/') ?>plugins/iCheck/square/blue.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	<style type="text/css">
		.help-block.error {
			color: red;
		}
	</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
	<div class="login-logo">
		<a href="<?php echo base_url() ?>">Selamat datang di <b>CV. Simatahari</b></a>
	</div>
	<div class="login-box-body">
		<p class="login-box-msg">Masuk untuk memulai sesi anda</p>
		<?php 
		if (!empty($this->session->userdata('masuk')))
		{
			?>
			<div class="alert alert-danger"><?php echo $this->session->userdata('masuk'); ?></div>
			<?php
		}

		if (!empty($this->session->userdata('daftar')))
		{
			?>
			<div class="alert alert-success"><?php echo $this->session->userdata('daftar'); ?></div>
			<?php
		}
		?>
		<form action="<?php echo base_url('pengguna/masuk') ?>" method="post">
			<div class="form-group has-feedback">
				<input type="text" class="form-control" placeholder="Email / Nama Pengguna" name="identity" value="<?php echo set_value('identity') ?>">
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
				<?php echo form_error('identity', '<span class="help-block error">', '</span>'); ?>
			</div>
			<div class="form-group has-feedback">
				<input type="password" class="form-control" placeholder="Kata Sandi" name="password">
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
				<?php echo form_error('password', '<span class="help-block error">', '</span>'); ?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Masuk</button>
				</div>
			</div>
		</form>

		<!-- <a href="#">I forgot my password</a><br> -->
		<br>
		<a href="<?php echo base_url('pengguna/daftar') ?>" class="text-center">Mendaftar</a>

	</div>
</div>

<script src="<?php echo base_url('assets/pengguna/') ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url('assets/pengguna/') ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url('assets/pengguna/') ?>plugins/iCheck/icheck.min.js"></script>
<script>
	$(function () {
		$('input').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%' /* optional */
		});
	});
</script>
</body>
</html>