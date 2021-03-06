<?php

require_once 'channels.php';
require_once 'videos.php';
require_once 'logs.php';

class Controller_channel_video_delete extends Controller {
	private $db = null;
	private $is_valid = true;
	private $is_success = true;
	private $validation_error = array();
	private $submission_error = array();

	function get_url($chain=null) {
		if ((is_null($chain) || $chain == $this->chain) && isset($this->video['id'])) {
			return parent::get_url() . "?id=" . $this->video['id'];
		} else if ($chain == "channel" && isset($this->channel["id"])) {
			return parent::get_url($chain) . "?id=" . $this->channel["id"];
		} else if ($chain == "channel/video" && isset($this->video["id"])) {
			return parent::get_url($chain) . "?id=" . $this->video["id"];
		} else {
			return parent::get_url($chain);
		}
	}

	function get_video() {
		$videos = new Model_videos();
		$this->video = $videos->select($this->get['id']);
	}
	function get_channel() {
		$channels = new Model_channels();
		$this->channel = $channels->select($this->video['channelId']);
	}
	function clean_files() {
		$filename = $this->video["filename"];
		$filepath = $this->config["contents_dir"] . $filename;
		if (empty($filename)) {
			return;
		}
		if (file_exists($filepath)) {
			unlink($filepath);
		}

		$filename = $this->video["nicoVideoId"] . ".xml";
		$filepath = $this->config["contents_dir"] . $filename;
		if (file_exists($filepath)) {
			unlink($filepath);
		}

		$filename = $this->video["nicoVideoId"] . ".jpg";
		$filepath = $this->config["contents_dir"] . $filename;
		if (file_exists($filepath)) {
			unlink($filepath);
		}
	}

	function validate() {
		$this->get_video();
		$this->get_channel();

		if (empty($this->video)) {
			$this->is_valid = false;
			$this->validation_error[] =
				"無効な動画が指定されました。";
		}

		return $this->is_valid;
	}
	function submit() {
		$videos = new Model_videos();
		$logs = new Model_logs();

		$this->clean_files();
		$videos->delete_logically($this->video["id"]);
		$logs->d("front", "channel/video/delete: " . $this->video["title"]);

		$this->is_success = true;
		return $this->is_success;
	}
	function run() {
		if (isset($this->post["confirm"])) {
			$this->validate();
			$this->set("video", $this->video);
			$this->set("channel", $this->channel);
		} else if (isset($this->post["submit"])) {
			$this->validate();
			$this->set("video", $this->video);
			$this->set("channel", $this->channel);

			if ($this->is_valid) {
				$this->submit();
			}
		} else {
			$this->validate();
			$this->set("video", $this->video);
			$this->set("channel", $this->channel);
		}

		$this->set("is_valid", $this->is_valid);
		$this->set("validation_error", $this->validation_error);
		$this->set("is_success", $this->is_success);
		$this->set("submission_error", $this->submission_error);
		$this->render();
	}
}
