# mvc


#### Setup Controller
##### add new controller
##### add new view with partials
##### update Routes.php with
#####     use App\Controllers\{IndexController, AboutController, RegisterController};
#####     $app->get("/register", [RegisterController::class, "index"]);


#### Setup Validator
##### Add new vaildator in folder Frameware/Rules
##### Update App/Services/ValidatorService.php
#####   Update use on line 8
#####   Add new line in construct to call add function on the validator class
#####   Add the new Validator in the validate{form} function. 

#### Setup new Service
##### create service php file in App/Services
##### add to containeridefinitions.php file
##### 
##### 


### Stop Session Hijacking

##### session_set_cookie_params([
#####             'secure' => $_ENV['APP_ENV'] === "production",
#####             'httponly' => true,
#####             'samesite' => 'lax'
#####         ]);
##### session_regenerate_id();