<?php
/*
 * API соединения с
 */
class apiXDRIVE
{

	private $lang_error = 'ru';

	function __construct($token) {
		$this->token = $token;
	}

	/*
	 * Send verification code
	 * phone:integer
	 * method:string
	 * response:array
	 * comment: string(100) - необязательный параметр. Будет полезен, если один токен используется на нескольких площадках,
	 * например, определение источника СПАМа. Можно добавить какой-то личный идентификатор в свободной форме.
	 */
	public function sendSMS($phone, $comment = '') {
		$method = 'sms.send';
		$data_arr = array(
			'phone' => $phone,
			'comment' => $comment,
		);
		$response = self::sendRequest($method, $data_arr);
		return $response;
	}

	/*
	 * Add lead
	 * $method:string
	 * $data_arr:array
	 * * * sponsor_id: integer
	 * * * surname: string
	 * * * name: string
	 * * * patronymic: string
	 * * * birthday dd.mm.yyyy
	 * * * phone: integer
	 * * * email: string
	 * * * sex: string:f:m
	 * * * country: string Alpha2
	 * $response:array
	 */
	public function addLead($data_arr) {
		$method = 'lead.add';
		$response = self::sendRequest($method, $data_arr);
		return $response;
	}

	/*
	 * Add lead
	 * $method:string
	 * $lang ru / en
	 * $response:array
	 */
	public function errorList() {
		$method = 'error.list';
		$response = self::sendRequest($method);
		return $response;
	}

	/*
	 * check Phone In Faberlic
	 * $method:string
	 * $lang ru / en
	 * $response:array
	 */
	public function checkPhoneInFaberlic($phone) {
		$method = 'user.checkPhoneInFaberlic';
		$data_arr = array(
			'phone' => $phone
		);
		$response = self::sendRequest($method, $data_arr);
		return $response;
	}


	/*
	 * Send request to apiXDRIVE
	 * $data_arr:array
	 * $response:array
	 */
	private function sendRequest($method, $data_arr = array()) {
		$url = "https://api.x-drive.online/method/" . $method;
		$data_arr['lang_error'] = $this->lang_error;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Accept: application/json';
		$headers[] = 'Authorization: Token ' . $this->token;
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data_arr));
		$json_response = curl_exec($curl);
		curl_close($curl);
		$response = json_decode($json_response, true);
		return $response;
	}
}