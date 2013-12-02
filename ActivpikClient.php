<?php

/**
 * Activpik Client Service ALPHA
 * 
 * @author Ludovic Bouguerra <ludovic.bouguerra@kalyzee.com>
 *
 */
class ActivpikClient {
		
    private $apiUrl;
    private $apiId;
    private $apiKey;
	
    
    /**
     * 
     * @param string $api_id : Your Activpik Account API ID
     * @param string $api_key : Your Activpik Account API KEY
     * @param string $url : The URL of service
     */
    function __construct($apiId, $apiKey, $url = 'https://api.activpik.com') {
		$this->apiId = $apiId;
		$this->apiKey = $apiKey;
		$this->apiUrl = $url;
    }
	
    
    /**
     * Upload a video on your personnal space
     * 
     * @param string $videoPath : Path to your video
     * @return videoID
     */
    function upload($mediaPath){
    	$vars = array(
    			"file" => "@".$mediaPath
    	);
    	
    	return $this->request("media/upload", "post", true, $vars);
    }

    /**
     * Ensemble des vidéos disponibles sur mon compte
     */
    function listVideos(){
    	 return $this->request("/media", "get", true);
    }
    
    /**
     * Ensemble des vidéos disponibles sur mon compte
     * @param integer id : id du media 
     */
    function getMediaById($id){
    	return $this->request("/media/".$id, "get", true);
    }

    /*********************************************************************************
    *	Encoding PART																 *
    **********************************************************************************/
    
    /**
    *	Stream this media media
    *
    */
    function addAudioTrack($idMedia, $idAudio){
		return $this->request("/media/".$idMedia."/encode/".$idAudio, "post", true);
    }
    
    /*********************************************************************************
    *	Diffusion PART																 *
    **********************************************************************************/
    
    /**
    *	Stream this media media
    *
    */
    function publish($id){
		return $this->request("/media/".$id."/publish", "post", true);
    }
    
    /**
    * Unpublish media
    *
    **/    
    function unpublish($id){
	return $this->request("/media/".$id."/publish", "delete", true);
    }


    /**
    *	Obtenir la liste des langues disponibles
    *
    */
    function languages(){
		return $this->request("/languages", "get", true);
    }
    
    /**
     * 
     * @param int $videoId : Video ID
     * @param string $texte : Texte d'un alignement
     * @param string $language : Language 
     */
    function align($videoId, $texte, $language){
    	$this->request($url);
    }
    
    /**
     * Launch a transcription
     * /media/{idMedia}/speech/transcribe/{modelName} 
     * 
     */
    function transcribe($mediaId, $language){
    	return $this->request("media/".$mediaId."/speech/transcribe/".$language, "post", true);
    }
    
    /**
     * 
     * @param int $mediaId : media id 
     * @return multitype:number mixed
     */
    function getTranscriptionJobsByMedia($mediaId){
    	return $this->request("media/".$mediaId."/speech", "get", true);
    }
    
    /**
     * Get transcription status
     * @param int $videoId
     */
    function getTranscriptionStatus($videoId){
    	$this->request($url);
    }
    
    /**
     * 
     * 
     * @param integer $mediaId : Identifiant du media
     * @param integer $transcriptionId : Identifiant de la transcription 
     * 
     */
    function getTranscriptionResult($mediaId, $transcriptionId){
    	return $this->request("media/".$mediaId."/speech/".$transcriptionId."/result", "get", true);
   	}
    
   
	public function request($url, $method = 'post', $auth = true, $vars = array()) {
	
		
		$opts = array(
				CURLOPT_URL => $this->apiUrl . '/' . $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_INFILESIZE => -1,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_SSL_VERIFYPEER => false,
		);
	
		
		echo $opts[CURLOPT_URL];
		
		if ($auth)
			$opts[CURLOPT_USERPWD] = $this->apiId . ':' . $this->apiKey;
		switch ($method) {
			case 'get':
			case 'GET':
				$vars = http_build_query($vars);
				$opts[CURLOPT_HTTPGET] = true;
				$opts[CURLOPT_URL] .= '?' . $vars;
				break;
			case 'post':
			case 'POST':
			default:
				$opts[CURLOPT_POST] = true;
				$opts[CURLOPT_POSTFIELDS] = $vars;
				break;
		}
		$curl = curl_init();
		curl_setopt_array($curl, $opts);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		return array((int) $status, $response);
	}
}
