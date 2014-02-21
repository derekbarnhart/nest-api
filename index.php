<?php

    require_once("nest.class.php");
    include("credentials.php");
    define('RE_READ','/^get/');
    define('RE_WRITE','/^set/');

    header('Content-type: application/json');

    $nest = new Nest();
    switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        if($_REQUEST['method'] == 'api'){

            echo json_encode(buildApi());
        } else {

        $methodName = getMethod($_REQUEST['method'],RE_READ);
        if($methodName){
            $result = call_user_func(array($nest,$methodName));
        }else{
            $result['error']="The api does not support that method";
        }

            echo json_encode($result);
        }
        break;
    case 'POST':
    case 'PUT':
        //TODO Implement this part
        //getMethod($_REQUEST['method'],RE_WRITE);
        break;
    default:
        break;
    }

    function getMethod($pMethod,$regex){
        $nestClass = new ReflectionClass('Nest');
        foreach($nestClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
            if(preg_match($regex, $method->name)){

                $compName = strtolower(preg_replace($regex,'',$method->name));
                if(trim($compName) == $pMethod){
                    return $method->name;
                }
            }
        }
        return false;
    }

    function buildApi(){
        $nestClass = new ReflectionClass('Nest');

        foreach($nestClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method){

            if(preg_match(RE_READ, $method->name)){

                $compName = preg_replace(RE_READ,'',$method->name);
                $api['get'][]=$compName;
            }else{
                $compName = preg_replace(RE_WRITE,'',$method->name);
                $api['post'][]=$compName;

            }
        }
        return $api;
    }



?>
