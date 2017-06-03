<?php

/**
 * A basic PHP client for the Fieldbook API
 *
 * Includes methods for basic CRUD capabilities and to retrieve metadata about Fieldbook books, sheets and fields. $this->response_info gives detailed information about the session result.
 *
 * @package PhieldBook
 * @author Joe Winter
 */
class PhieldBook {

	public $api_key;
	public $api_secret;
	public $book_id;
	public $sheet_id;
	public $sheet_title;
	public $record_id;
	public $response_info;
	public $limit;
	public $offset;
	public $include = array();
	public $exclude = array();

	private $url;
	private $session;
	private $ch;

	/**
	 * PhieldBook constructor with options defined in the array
	 *
	 * 'api_key'    => string Your Fieldbook API key
	 * 'api_secret' => string Your Fieldbook API secret
	 *
	 * @param array $args (see above)
	 */
	public function __construct( $args ) {
		foreach($args as $key => $value) {
			$this->$key = $value;
		}
	}

	private function build_session() {
		$this->set_url();
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_USERPWD, $this->api_key . ":" . $this->api_secret);
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
	}

	private function build_meta_session($meta) {
		$this->set_url($meta);
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_USERPWD, $this->api_key . ":" . $this->api_secret);
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
	}

	private function exec_session() {
		$this->session = curl_exec($this->ch);
		$this->response_info = curl_getinfo($this->ch);
		curl_close($this->ch);
	}

	private function session_result() {
		return json_decode($this->session, true);
	}

	private function set_url($meta = false) {
		if($meta == 'books') {
			$this->url = "https://api.fieldbook.com/v1/books/{$this->book_id}";
		} elseif($meta == 'sheets') {
			$this->url = "https://api.fieldbook.com/v1/books/{$this->book_id}/sheets";
		} elseif($meta == 'fields') {
			$this->url = "https://api.fieldbook.com/v1/sheets/{$this->sheet_id}/fields";
		} else {
			$this->url = implode( '/', array('https://api.fieldbook.com/v1', $this->book_id, $this->sheet_title, $this->record_id ));
			$this->url = rtrim($this->url, '/');
			if(strlen($this->limit) > 0 || strlen($this->offset) > 0) {
				if(strpos($this->url, '?') == false) $this->url .= "?";
				$params = array();
				if(strlen($this->limit) > 0) $params['limit'] = $this->limit;
				if(strlen($this->offset) > 0) $params['offset'] = $this->offset;
				$this->url .= http_build_query($params);
			}
			if(count($this->include) > 0 || count($this->exclude) > 0) {
				if(strpos($this->url, '?')) $this->url .= "&";
				else $this->url .= "?";
				if(count($this->include) > 0 && count($this->exclude) == 0) {
					$fields = implode(",", $this->include);
					$params = array('include' => $fields);
					$this->url .= http_build_query($params);
				} elseif(count($this->exclude) > 0 && count($this->include) == 0) {
					$fields = implode(",", $this->exclude);
					$params = array('exclude' => $fields);
					$this->url .= http_build_query($params);
				}
			}
		}
	}

	/**
	 * Retrieve a record or a set of records
	 *
	 * @return array An associative array if $record_id is specified, or else an array of arrays
	 */
	public function get() {
		$this->build_session();
		$this->exec_session();
		return $this->session_result();
	}

	/**
	 * Retrieve a set of records based on search criteria
	 *
	 * @param array $params An associative array where field names are keys
	 *
	 * @return array An associative array of matching records
	 */
	public function search($params) {
		$this->build_session();
		$this->url .= "?";
		$this->url .= http_build_query($params);
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		$this->exec_session();
		return $this->session_result();
	}

	/**
	 * Create a new record
	 *
	 * @param array $params An associative array where field names are keys
	 *
	 * @return array An associative array of the resulting record
	 */
	public function create($params) {
		$this->build_session();
		$fields_string = json_encode($params);
		curl_setopt($this->ch, CURLOPT_POST, count($params));
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$this->exec_session();
		return $this->session_result();
	}

	/**
	 * Update an existing record
	 *
	 * @param array $params An associative array where field names are keys. Specify only the fields you need to update.
	 *
	 * @return array An associative array of the updated record
	 */
	public function update($params) {
		$this->build_session();
		$fields_string = json_encode($params);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$this->exec_session();
		return $this->session_result();
	}

	/**
	 * Delete a record. No warnings are given if the specified record does not exist.
	 *
	 * @return integer The HTTP Code 204
	 */
	public function delete() {
		$this->build_session();
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$this->exec_session();
		return $this->response_info['http_code'];
	}

	/**
	 * Retrieve basic information about a book.
	 *
	 * @return array An array with the book's id, title, and url.
	 */
	public function book_meta() {
		$this->build_meta_session('books');
		$this->exec_session();
		return $this->session_result();
	}

	/**
	 * Retrieve information about the sheets in a book.
	 *
	 * @return array An array of sheets, with each sheet's id, title, slug, and url.
	 */
	public function sheet_meta() {
		$this->build_meta_session('sheets');
		$this->exec_session();
		return $this->session_result();
	}

	/**
	 * Retrieve information about the fields in a sheet. The instance's $sheet_id must be set.
	 *
	 * @return array An array of fields, with each field's key, name, slug, fieldType, and inputType. If a field is required, the boolean value "required" will also be returned. If a fieldType is picklist, an "enum" array will also be returned.
	 */
	public function field_meta() {
		$this->build_meta_session('fields');
		$this->exec_session();
		return $this->session_result();
	}

}
