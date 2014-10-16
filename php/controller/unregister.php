<?php

require_once 'channels.php';
require_once 'videos.php';

class Controller_unregister extends Controller {
	private $db = null;
	private $is_valid = true;
	private $is_success = true;
	private $validation_error = array();
	private $submission_error = array();

	function get_url($chain=null) {
		if ((is_null($chain) || $chain == $this->chain) && isset($this->channel['id'])) {
			return parent::get_url() . "?id=" . $this->channel['id'];
		} else {
			return parent::get_url($chain);
		}
	}

	function get_channel() {
		$channels = new Model_channels();
		$this->channel = $channels->select($this->get['id']);
	}
	function clean_files($video) {
		$filename = $video["filename"];
		$filepath = $this->config["contents"] . $filename;
		if (empty($filename)) {
			return;
		}
		if (file_exists($filepath)) {
			unlink($filepath);
		}

		$filename = $video["nicoVideoId"] . ".xml";
		$filepath = $this->config["contents"] . $filename;
		if (file_exists($filepath)) {
			unlink($filepath);
		}

		$filename = $video["nicoVideoId"] . ".jpg";
		$filepath = $this->config["contents"] . $filename;
		if (file_exists($filepath)) {
			unlink($filepath);
		}
	}

	function validate() {
		$this->get_channel();

		if (empty($this->channel)) {
			$this->is_valid = false;
			$this->validation_error[] =
				"無効なタイトルが指定されました。";
		}
		if ((isset($this->post["confirm"]) || isset($this->post["submit"])) &&
				$this->post["nicoChannelId"] != $this->channel["nicoChannelId"]) {
			$this->is_valid = false;
			$this->validation_error[] =
				"確認用のチャンネル名が正しくありません。";
		}

		return $this->is_valid;
	}
	function submit() {
		$channels = new Model_channels();
		$videos = new Model_videos();

		$video_array = $videos->select_all_by_channel_id($this->get["id"]);
		foreach ($video_array as $video) {
			$this->clean_files($video);
		}
		$videos->delete_by_channel_id($this->get["id"]);
		$channels->delete($this->get["id"]);

		$this->is_success = true;
		return $this->is_success;
	}
	function run() {
		if (isset($this->post["confirm"])) {
			$this->validate();
			$this->set("channel", $this->channel);
		} else if (isset($this->post["submit"])) {
			$this->validate();
			$this->set("channel", $this->channel);

			if ($this->is_valid) {
				$this->submit();
			}
		} else {
			$this->validate();
			$this->set("channel", $this->channel);
		}

		$this->set("is_valid", $this->is_valid);
		$this->set("validation_error", $this->validation_error);
		$this->set("is_success", $this->is_success);
		$this->set("submission_error", $this->submission_error);
		$this->render();
	}
}