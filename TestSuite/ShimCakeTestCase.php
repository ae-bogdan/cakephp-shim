<?php

abstract class ShimCakeTestCase extends CakeTestCase {

	/**
	 * Opposite wrapper method of assertWithinMargin.
	 *
	 * @param float $result
	 * @param float $expected
	 * @param float $margin
	 * @param string $message
	 * @return void
	 */
	protected static function assertNotWithinMargin($result, $expected, $margin, $message = '') {
		$upper = $result + $margin;
		$lower = $result - $margin;
		return static::assertFalse((($expected <= $upper) && ($expected >= $lower)), $message);
	}

	/**
	 * Outputs debug information during a web tester (browser) test case
	 * since PHPUnit>=3.6 swallowes all output by default
	 * this is a convenience output handler since debug() or pr() have no effect
	 *
	 * @param mixed $data
	 * @param bool $force Should the output be flushed (forced)
	 * @param bool $showHtml
	 * @return void
	 */
	protected static function debug($data, $force = false, $showHtml = null) {
		if (!empty($_GET['debug']) || !empty($_SERVER['argv']) && (in_array('-v', $_SERVER['argv'], true) || in_array('-vv', $_SERVER['argv'], true))) {
			if ($showHtml === null && php_sapi_name() === 'cli') {
				$showHtml = true;
			}
			debug($data, $showHtml);
		} else {
			return;
		}
		if (!$force) {
			return;
		}
		ob_flush();
	}

	/**
	 * Outputs debug information during a web tester (browser) test case
	 * since PHPUnit>=3.6 swallowes all output by default
	 * this is a convenience output handler
	 *
	 * This method will not be part of 3.x! Please switch to debug().
	 *
	 * @param mixed $data
	 * @param bool $force Should the output be flushed (forced)
	 * @return void
	 */
	protected static function out($data, $plain = false, $force = false) {
		if (php_sapi_name() === 'cli') {
			return;
		}
		if (!$plain || is_array($data)) {
			pr($data);
		} else {
			echo '<div>' . $data . '</div>';
		}
		if (!$force) {
			return;
		}
		ob_flush();
	}

	/**
	 * MyCakeTestCase::isDebug()
	 *
	 * @return bool Success
	 */
	protected static function isDebug() {
		if (!empty($_GET['debug']) || !empty($_SERVER['argv']) && in_array('--debug', $_SERVER['argv'], true)) {
			return true;
		}
		return false;
	}

	/**
	 * OsFix method
	 *
	 * @param string $string
	 * @return string
	 */
	protected function _osFix($string) {
		return str_replace(["\r\n", "\r"], "\n", $string);
	}

}
