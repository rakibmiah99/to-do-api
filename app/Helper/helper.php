<?php
    function validationFormatter($errors){
        $newErrors = [];
        foreach ($errors->getMessageBag()->toArray() as $key=>$error){
            $newErrors[$key] = $error[0];
        }
        return $newErrors;
    }
