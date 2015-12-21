<?php

class Speed extends Controller
{
    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/songs/index
     */
	public function index(){
		       // load views. within the views we can echo out $songs and $amount_of_songs easily
			 	$testTargets = $this->model->getTestTargets();

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

	public function getTestConfigs(){
		header("location:http://$_SERVER[HTTP_HOST]/speed/checkIncompleteTests");
		$string = file_get_contents(dirname(__FILE__)."/../config/testTargets.json");
		$testConfig = json_decode($string, true);
		$webPageTestUrl = "http://www.webpagetest.org/runtest.php";
		//header('Content-Type: application/json');

		foreach ($testConfig as $test) {
			// use key 'http' even if you send the request to https://...
			$options = array(
				'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($test),
				),
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($webPageTestUrl, false, $context);
			$resultObject = json_decode($result, true);

			$currentTestId = $resultObject["data"]["testId"];
			$currentTestUrl = $resultObject["data"]["jsonUrl"];
			$currentCode = $resultObject["statusCode"];

			//$this->model->addTestRun($currentTestId, $currentTestUrl, $currentCode);
			//echo $test["url"].": $currentCode : $currentTestId : $currentTestUrl<br />";

			sleep(.25);

			$this->_getResult($currentTestId);
			//header("location:/speed/addResult?testId=".$currentTestId);
		}


	}

	function _getResult($testId){
			$result = file_get_contents("http://www.webpagetest.org/jsonResult.php?test=".$testId);
			$resultObject = json_decode($result, true);
			$currentTestId = $resultObject["data"]["id"];
			$currentTestUrl = "http://www.webpagetest.org/jsonResult.php?test=$currentTestId";
			$currentCode = $resultObject["statusCode"];

			if ($currentCode == 200){
				echo file_get_contents("http://$_SERVER[HTTP_HOST]/speed/addResult?testId=$currentTestId/");
			} else {
				echo $resultObject["data"]["testInfo"]["url"];
				echo "($currentCode): <a href='/speed/readResult?testId=$testId'>Read Test</a><br />";
			}

			$this->model->addTestRun($currentTestId, $currentTestUrl, $currentCode);

	}

	/**
 * ACTION: readResult
 */
	public function readResult(){
		if (isset($_GET["testId"])){
			$testId = $_GET["testId"];
		}
		$this->_getResult($testId);
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
				"ttfb" => array(),
				"speedIndex" => array(),
				"render" => array(),
				"firstPaint" => array(),
				"loadTime" => array(),
				"requests" => array(),
				"visualComplete" => array(),
				"lastVisualChange" => array()
			)
		);

		foreach ($results as $testRun) {
			$loadTime = $this->getTestRunItem($testRun->completed, $testRun->loadTime, $testRun->type);
			$ttfb = $this->getTestRunItem($testRun->completed, $testRun->TTFB, $testRun->type);
			$requests = $this->getTestRunItem($testRun->completed, $testRun->requests, $testRun->type);
			$speedIndex = $this->getTestRunItem($testRun->completed, $testRun->speedIndex, $testRun->type);
			$render = $this->getTestRunItem($testRun->completed, $testRun->render, $testRun->type);
			$visualComplete = $this->getTestRunItem($testRun->completed, $testRun->visualComplete, $testRun->type);
			$lastVisualChange = $this->getTestRunItem($testRun->completed, $testRun->lastVisualChange, $testRun->type);
			$firstPaint = $this->getTestRunItem($testRun->completed, $testRun->firstPaint, $testRun->type);

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
				"y" => $value,
				"group" => $group
		);
	}

public function checkIncompleteTests(){
	$test = $this->model->getCurrentTestRuns();

	echo count($test);
	foreach ($test as $run) {
		$this->_getResult($run->testId);
	}
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
