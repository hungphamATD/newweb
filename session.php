<?php

class session_gafam {
		/**
		 * Start session
		 *
		 * @return void
		 */
		static function start() {
				session_start();
		}
		/**
		 * Assign value to session
		 *
		 * @param string $name A session ID
		 * @param string $value A value
		 *
		 * @return void
		 */
		static function assign($name = '', $value = '') {
				if(!empty($value)) {
						$_SESSION[$name] = $value;
				}
		}
		/**
		 * Remove session
		 *
		 * @param string $name A session ID
		 *
		 * @return void
		 */
		static function unassign($name = '') {
				if(isset($_SESSION[$name])) {
						unset($_SESSION[$name]);
				}
		}
		/**
		 * Check if the session exists or not
		 *
		 * @param string $name A session ID
		 *
		 * @return boolean
		 */
		static function isSession($name = '') {
				if(isset($_SESSION[$name])) {
						return true;
				}
				return false;
		}
} //class
