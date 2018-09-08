<?php

class MultidepartmentComponent extends Controller {
    public $models = array('Backend');
    public $user_id = null;
    public $departmentsList = array();
    public $isRoled = false;
    public $currentRole = null;
    
    private $keyList = array();
    public static $roleFiledInUserSession = 'current_role';
    public static $roleFiledIDInUserSession = 'current_role_id';
    public static $roleList = 'roles_disponibles';
    
    
    public function check ( ) {
        // INIT CHECK
        if(!$this -> Session -> is_logged()) return false;
        
        // NOT IS ROLED
        $this -> departmentsList = $this -> Backend -> getDeparments($this -> user_id);
        $this -> keyList = array_keys($this -> departmentsList);
        $user = $this -> Session -> user();
        if(!isset($user[self::$roleFiledIDInUserSession]) or is_null($user[self::$roleFiledIDInUserSession]))
            $this -> loadDeparment ();
        
    }
    
    private function check_is_roled(){
        if($this -> Session -> user(self::$roleFiledIDInUserSession)){
            $this -> isRoled = true;
        } else {
            $this -> isRoled = false;
        }
    }
    
    public function loadDeparment($role = null){
        if(is_null($role))
            $role = $this -> keyList[0];
        
        if(!isset($this -> departmentsList[$role]))
            die("[MultidepartmentComponent::loadDeparment] Hubo un problema con el enrolamiento del usuario. No existe el rol.");
        
        $this -> isRoled = true;
        $this -> currentRole = $this -> departmentsList[$role];
        
        // LOADS NEW SESSION CONTEXT.
        $user = $this -> Session -> user();
        $user[self::$roleFiledInUserSession] = $this -> currentRole;
        $user[self::$roleFiledIDInUserSession] = $role;
        $user[self::$roleList] = $this -> departmentsList;
        $this -> Session -> update($user);
        // breakpoint($this -> Session -> user());
        return true;
    }
}