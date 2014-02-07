<?php
/**
 * Site Controller
 *
 * The main Controller class for the entire application. This handles all the action requests
 * and redirects or routes them through the proper channels. Setting up classes and processing
 * forms are done in the appropriate action functions.
 * 
 * This class conforms to the Model-View-Controller paradigm.
 * 
 */
class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    protected function beforeAction($event)
    {
        # Do this for every page except the update, error, and install pages
        if($event->getId() != "update" and $event->getId() != "error" and $event->getId() != "install") {
            # Takes ~0.15 seconds to check system
            $system = new System;
            if(!$system->healthy()) {
                # Let's destroy any sessions currently, just in case.
                @Yii::app()->session->destroy();
                # Update the system
                if($system->update()) {
                    if(YII_DEBUG) {
                        Yii::app()->user->setFlash("warning","The system has automatically updated the database and table structures.");
                    }
                }
                else {
                    Yii::app()->user->setFlash("error","The system is not configured properly and could not update itself. Error Log: ".$system->get_error());
                    $this->redirect(Yii::app()->createUrl('error'));
                    exit;
                }
            }
        }
        return $event;
    }

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
	    if(isset($_REQUEST["upgrade"]) and $_REQUEST["upgrade"]=="true") {
	        Yii::app()->user->setFlash("success","Successfully upgraded the Yii framework.");
            $this->redirect(Yii::app()->createUrl('index'));
            exit;
	    }
        if(isset($_REQUEST["install"]) and $_REQUEST["install"]=="true") {
            Yii::app()->user->setFlash("success","Successfully installed the Yii framework.");
            $this->redirect(Yii::app()->createUrl('index'));
            exit;
        }
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * Loads information about the site on a page.
	 */
    public function actionSiteInfo()
    {
        $this->render('siteinfo');
    }

    public function actionNeed()
    {
        # Load the user if they're logged in
        if(!Yii::app()->user->isGuest) {
            $user = new UserObj(Yii::app()->user->name);
        }
        else {
            $user = new UserObj();
        }
        
        if(isset($_POST["issuesform"])) {
            # Submit issue here
        }
        
        $params["user"] = $user;
        
        $this->render('need',$params);
    }

    /**
     * Leave an issue for the developers.
     */
    public function actionIssues()
    {
        $this->noGuest();
        
        $this->render('issues');
    }
    
	/**
	 * Installation action. To be done only once when the application is first being setup.
	 */
	public function actionInstall()
	{
        # Let's destroy any sessions currently, just in case.
        @Yii::app()->session->destroy();
        
        if(isset($_REQUEST["yii"]) and $_REQUEST["yii"] == "installed") {
            Yii::app()->user->setFlash("success","Succssfully installed the Yii Framework! Continue the installation by filling in the form below.");
            $this->redirect("install");
            exit;
        }
        
        # Does the application need installing? Check if database exists.
        $config_ext = Yii::app()->basePath."\\config\\main-ext.php";
        if(is_file($config_ext)) {
            Yii::app()->user->setFlash("warning","This application is already installed. Please delete the main-ext.php file to re-install.");
            $this->redirect(Yii::app()->createUrl('index'));
        }
        
        # Submitted form. Technically only one stage but verifies form was submitted.
        if(isset($_REQUEST["stage"]) and $_REQUEST["stage"] == "init") {
            # Create a new System without initializing
            $system = new System(false);
            
            # Install the system
            if($system->install()) {
                # Redirect to main page, the system will catch that we have no tables installed and will install them.
                $this->redirect(Yii::app()->baseUrl);
                exit;
            }
            else {
                Yii::app()->user->setFlash("error","There was an error installing the application: ".$system->get_error());
            }
        }
        
        # Render the form interface
        $this->render("install");
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * This is the action to post CU Equipment to the CU Property Board.
	 */
    public function actionPost()
    {
        # Logged in users only
        $this->noGuest();
        
		# Initialize variables
        $params = array();
        $property = new PropertyObj();
        $error = "";
        
		# Setup page if we're editing an existing post
        if(isset($_GET["id"])) {
            $property->propertyid = $_GET["id"];
            $property->load();
            
            # Could not find post
            if(!$property->loaded) {
                Yii::app()->user->setFlash("error","Could not find post with this id.");
                $this->redirect(Yii::app()->baseUrl);
                exit;
            }
        }
        
        try {
		    # If user submitted form...
            if(isset($_POST["propertyform"])) {
                
                # If the user set up the form to remove post
                if(isset($_POST["remove-property"]) and $_POST["remove-property"]==1) {
                    $property->status = "removed";
                    if(!$property->save()) {
                        $error = $property->error_field;
                        throw new Exception($property->get_error());
                    } else {
                        Yii::app()->user->setFlash("success","Successfully removed post.");
                        $this->redirect(Yii::app()->createUrl('index'));
                        exit;
                    }
    				
    			# Otherwise lets save the updated/new post information
                } else {
                	
                	# Set property values from form
                    $property->department       = $_POST["department"];
                    $property->contactname      = $_POST["contactname"];
                    $property->contactemail     = $_POST["contactemail"];
                    $property->description      = $_POST["description"];
                    
                    # Save post
                    if(!$property->save()) {
                        $error = $property->error_field;
                        throw new Exception($property->get_error());
                    }
                    
                    # Save the image files
                    $count = 0;
                    while(isset($_POST["html5_uploader_".$count."_tmpname"])) {
                        $image = new ImageObj();
                        $image->propertyid = $property->propertyid;
                        $image->location = "images/property/".Yii::app()->user->name."/".$_POST["html5_uploader_".$count."_tmpname"];
                        $image->sorder = $count;
                        $image->who_uploaded = Yii::app()->user->name;
                        $image->date_uploaded = date("Y-m-d H:i:s");
                        if(!$image->save()) {
                            throw new Exception("Error saving image: ".$image->get_error());
                        }
                        $count++;
                    }
                    
    				# If no errors occured then run cron, set flash, and redirect home
                    if(!Yii::app()->user->hasFlash("error")) {
                        # Run Cron which will update every user's watchlists
                        Cron::run_cron();                   
                        # Success message and redirect!                   
                        Yii::app()->user->setFlash("success","Successfully updated CU Property advertisement.");
                        $this->redirect(Yii::app()->createUrl('index'));
                        exit;
                    }
                }
            }
        }
        catch(Exception $e) {
            Yii::app()->user->setFlash("error",$e->getMessage());
        }
      
        # Attach the loaded variables for the view
        $params["property"] = $property;
        $params["error"] = $error;
        
        # Load the view
        $this->render('post',$params);
    }

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
        # Force log out
        if(!Yii::app()->user->isGuest) Yii::app()->user->logout();
        
		# Force SSL
        $this->makeSSL();
        
        # Initialize variables and Login model
        $params = array();
        $model = new LoginForm;
        $redirect = (isset($_REQUEST["redirect"])) ? $_REQUEST["redirect"] : "index";
        $error = "";
        
        # Collect user input data
        if (isset($_POST['username']) and isset($_POST["password"])) {
            $model->username = $_POST["username"];
            $model->password = $_POST["password"];
            # Validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                $user = new UserObj($model->username);
                Yii::app()->user->setFlash("success","Logged In! Welcome, ".$user->name."!");
                $this->redirect($redirect);
            } else {
                Yii::app()->user->setFlash("error",$model->error);
            }
        }
        
        $params["model"] = $model;
        
        # Display the login form
        $this->render('login',$params);
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
        Yii::app()->session->open();
        Yii::app()->user->setFlash("success","You have successfully logged out of your session.");
		$this->redirect(Yii::app()->homeUrl);
	}

    /**
     * AJAX Function calls below
     */
     
    /**
	 * Remove image from property post (called from post.php when editing)
	 */
    public function action_remove_image()
    {
    	# Load image
        $image = new ImageObj($_REQUEST["imageid"]);
		
		# If not loaded return error
        if(!$image->loaded) {
            return false;
        }
        
		# Remove the actual image from disk space
        unlink(getcwd()."/".$image->location);
		# Delete the entry in the database images-table
        $image->delete();
        return true;
    } 
    
    public function action_upload_images()
    {
        
        /**
         * upload.php
         *
         * Copyright 2009, Moxiecode Systems AB
         * Released under GPL License.
         *
         * License: http://www.plupload.com/license
         * Contributing: http://www.plupload.com/contributing
         */
        
        // HTTP headers for no cache etc
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // Settings
        $targetDir = getcwd().'/images/property/'.Yii::app()->user->name."/";
        
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        
        // 5 minutes execution time
        @set_time_limit(5 * 60);
        
        // Uncomment this one to fake upload time
        // usleep(5000);
        
        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        
        // Clean the fileName for security reasons
        $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
        
        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);
        
            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;
        
            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }
        
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        
        // Create target dir
        if (!file_exists($targetDir))
            @mkdir($targetDir);
        
        // Remove old temp files    
        if ($cleanupTargetDir) {
            if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
                while (($file = readdir($dir)) !== false) {
                    $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        
                    // Remove temp file if it is older than the max age and is not the current file
                    if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                        @unlink($tmpfilePath);
                    }
                }
                closedir($dir);
            } else {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
        }   
        
        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        
        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];
        
        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = @fopen($_FILES['file']['tmp_name'], "rb");
        
                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    @fclose($in);
                    @fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = @fopen("php://input", "rb");
        
                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
        
                @fclose($in);
                @fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        
        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off 
            rename("{$filePath}.part", $filePath);
        }
        
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

    } 
    
    public function action_email_contact()
    {
    	# If not all the correct information was passed, return error
        if(!isset($_REQUEST["contact"],$_REQUEST["postid"],$_REQUEST["message"])) {
            Yii::app()->user->setFlash("error","Message did not send.");
            return true;
        }

		# Load up email and property post
        $email = new EmailObj();
        $property = new PropertyObj($_REQUEST["postid"]);
        
        # Send email using passed in information
        if($email->send_response_to_post($_REQUEST["contact"],$_REQUEST["message"],$property)) {
            Yii::app()->user->setFlash("success","Successfully sent response to contact about post #".$property->propertyid);
        } else {
            Yii::app()->user->setFlash("error",$email->error);
        }
        
		# End all be all
        return true;
    }
    
	/**
	 * Important function for enforcing SSL on a page.
	 * Mainly used for the Login page.
	 */
	private function makeSSL()
	{
		if($_SERVER['SERVER_PORT'] != 443) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			exit();
		}
	}

	/**
	 * Forces page to not be SSL.
	 */
	private function makeNonSSL()
	{
		if($_SERVER['SERVER_PORT'] == 443) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			exit();
		}
	}
    
	/**
	 * Checks to see if a user is logged into the application.
	 * If not then it will redirect to the login page with a warning.
	 */
    private function noGuest()
    {
        if(Yii::app()->user->isGuest) {
            Yii::app()->user->setFlash("warning","You must be signed in to access this page.");
            $this->redirect(Yii::app()->createUrl('login')."?redirect=".urlencode("https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
            exit;
        }
    }
}