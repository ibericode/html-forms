<?php
namespace HTML_Forms;

class SubmissionException extends \Exception {

	public function __toString() {
		return $this->getMessage();
	}
}