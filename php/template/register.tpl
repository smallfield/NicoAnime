<?php $this->include_template('include/header.tpl') ?>
<?php $this->include_template('include/breadcrumb.tpl') ?>

<div class="page-header">
	<h1><?= h($this->get_title()) ?> <small>チャンネルの登録</small></h1>
</div>

<?php
	$is_valid = $this->get("is_valid");
	$is_success = $this->get("is_success");

	if (isset($this->post["confirm"])) {
		if ($is_valid) {
			$this->include_template("register_confirm.tpl");
		} else {
			$this->include_template("register_default.tpl");
		}
	} else if (isset($this->post["submit"])) {
		$this->include_template("register_submit.tpl");
	} else {
		$this->include_template("register_default.tpl");
	}
?>

<?php $this->include_template('include/footer.tpl') ?>
