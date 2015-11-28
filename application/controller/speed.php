<?php

class Speed extends Controller
{
    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/songs/index
     */
	public function index(){
		       // load views. within the views we can echo out $songs and $amount_of_songs easily
        require APP . 'view/_templates/header.php';
        require APP . 'view/speed/index.php';
        require APP . 'view/_templates/footer.php';
	}

	/**
	 * ACTION: getResults
	 */
	public function getResults(){
		$results = $this->model->getAllTestResults();

		header('Content-Type: application/json');
		echo json_encode($results);
	}

		/**
	 * ACTION: getResults
	 */
	public function getEnhancedResults(){
		$results = $this->model->getAllTestResults($_GET["src"]);
		$enhanced = array(
			"url" => $results[0]->testUrl,
			"browser" => $results[0]->fromBrowser,
			"indicators" => array(
				"speedIndex" => array(),
				"render" => array(),
				"firstPaint" => array(),
				"loadTime" => array(),
				"requests" => array(),
				"ttfb" => array(),
				"visualComplete" => array(),
				"lastVisualChange" => array()
			)
		);

		foreach ($results as $testRun) {
			$loadTime = $this->getTestRunItem($testRun->completed, $testRun->loadTime);
			$ttfb = $this->getTestRunItem($testRun->completed, $testRun->TTFB);
			$requests = $this->getTestRunItem($testRun->completed, $testRun->requests);
			$speedIndex = $this->getTestRunItem($testRun->completed, $testRun->speedIndex);
			$render = $this->getTestRunItem($testRun->completed, $testRun->render);
			$visualComplete = $this->getTestRunItem($testRun->completed, $testRun->visualComplete);
			$lastVisualChange = $this->getTestRunItem($testRun->completed, $testRun->lastVisualChange);
			$firstPaint = $this->getTestRunItem($testRun->completed, $testRun->firstPaint);

			array_push($enhanced["indicators"]["loadTime"], $loadTime);
			array_push($enhanced["indicators"]["ttfb"], $ttfb);
			array_push($enhanced["indicators"]["requests"], $requests);
			array_push($enhanced["indicators"]["speedIndex"], $speedIndex);
			array_push($enhanced["indicators"]["render"], $render);
			array_push($enhanced["indicators"]["visualComplete"], $visualComplete);
			array_push($enhanced["indicators"]["lastVisualChange"], $lastVisualChange);
			array_push($enhanced["indicators"]["firstPaint"], $firstPaint);
		}



		header('Content-Type: application/json');
		echo json_encode($enhanced);
	}

	function getTestRunItem($key, $value, $group = 1){
		return array(
				"x" => $key,
				"y" => $value
		);
	}

    /**
     * ACTION: addSong
     * This method handles what happens when you move to http://yourproject/songs/addsong
     * IMPORTANT: This is not a normal page, it's an ACTION. This is where the "add a song" form on songs/index
     * directs the user after the form submit. This method handles all the POST data from the form and then redirects
     * the user back to songs/index via the last line: header(...)
     * This is an example of how to handle a POST request.
     */
    public function addResult()
    {
		header('Content-Type: application/json');

        // if we have POST data to create a new song entry
        if (isset($_GET["testId"])) {
            // do addSong() in model/model.php
            //$this->model->addSong($_POST["artist"], $_POST["track"],  $_POST["link"]);
            $parameter = $_GET["testId"];
            try {
            	$xml = simplexml_load_file("http://www.webpagetest.org/xmlResult/".$parameter);
	    		
	    		$data = $xml->data;
	    		$result = $xml->data->median->firstView;

	    		$this->model->addTestResult(
	    			$data->testId,
	    			$data->testUrl,
	    			$data->from,
	    			$data->completed,
	    			"first",
	    			$result->loadTime,
	    			$result->TTFB,
	    			$result->requests,
	    			$result->SpeedIndex,
	    			$result->render,
	    			$result->visualComplete,
	    			$result->lastVisualChange,
	    			$result->firstPaint
				);

	    		$result = $xml->data->median->repeatView;

	    		$this->model->addTestResult(
	    			$data->testId,
	    			$data->testUrl,
	    			$data->from,
	    			$data->completed,
	    			"repeat",
	    			$result->loadTime,
	    			$result->TTFB,
	    			$result->requests,
	    			$result->SpeedIndex,
	    			$result->render,
	    			$result->visualComplete,
	    			$result->lastVisualChange,
	    			$result->firstPaint
				);
				
				http_response_code(201);
				echo json_encode($data->testId);
			} catch (Exception $e) {
				http_response_code(400);
				echo json_encode("Bad Request, no testrun for testID"+ $parameter);
    		}

        } else {
        	http_response_code(400);
			echo json_encode("Bad Request, missing parameter testId");

        }
    }
}