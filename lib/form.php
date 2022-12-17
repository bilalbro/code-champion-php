<?php

// Form class

class FormInputDescriptor {
   public $trim = true;
   public $required = true;
   public $pattern = null;
   public $fx = null;
   public $type = null;
   public $name = null;
   public $value = null;
   public $validated = false;
   
   public function __construct($name) {
      $this->name = $name;
   }
}


class FormInputGroupDescriptor {
   public $keys;
   public $fx;

   public function __construct($keys, $fx) {
      $this->keys = $keys;
      $this->fx = $fx;
   }
}


class Form {

   static private function _validate($key, $value, $inputDescriptor, &$_value) {

      // normalize value by trimming it off, but only if it's actually a string
      if ($inputDescriptor->trim and is_string($value)) {
         $value = trim($value);
      }

      // Check 1:
      // Get done with the 'required' field
      if ($inputDescriptor->required and $value === "") {
         $_value = ucfirst($inputDescriptor->name) . " field is required.";
         return false;
      }

      // If the given value is an empty string after trimming, it's impossible
      // to do further checks on it. Hence immediately return.
      if ($value === "") {
         $_value = $value;
         return true;
      }

      // Check 2:
      // Get done with patterns
      if ($inputDescriptor->pattern and
            !preg_match($inputDescriptor->pattern, $value)) {
         $_value = ucfirst($inputDescriptor->name) . " field is invalid.";
         return false;
      }

      // Check 3:
      // Get done with executing given functions
      if ($inputDescriptor->fx) {
         $returnValue = call_user_func($inputDescriptor->fx, $value);
         if ($returnValue !== true) {
            $_value = $returnValue;
            return false;
         }
      }

      $inputDescriptor->validated = true;

      $_value = $value;
      return true;
   }

   static private function _resolveStringValue($value, $inputDescriptor) {
      // if $value is "email", set the internal $pattern property
      if ($value === "email") {
         $inputDescriptor->pattern = "/[A-Za-z0-9]{1,}@[A-Za-z0-9]{1,}\\.[A-Za-z0-9]{2,}/";
      }

      elseif ($value === "required") {
      }

      else {
         echo "required\n";
         $inputDescriptor->pattern = $value;
      }
   }

   static private function _resolveArrayValue($value, $inputDescriptor) {
      foreach ($inputDescriptor as $key => $_value) {
         if (array_key_exists($key, $value)) {
            $inputDescriptor->$key = $value[$key];
         }
      }

      if (array_key_exists("type", $value)) {
         Form::_resolveStringValue($value["type"], $inputDescriptor);
      }
   }


   // contains an associative array mapping each key to a FormInputDescriptor object
   private $inputDescriptorMap;
   private $inputGroupDescriptorList;
   private $schemaSet = false;


   // go over each key in the given $schema dictionary, and add it to the
   // private $inputDescriptorMap dictionary based on its corresponding value
   public function setSchema($schema) {
      if ($this->schemaSet) {
         throw 'Problem';
      }

      $this->schemaSet = true;
      $this->inputDescriptorMap = array();

      foreach ($schema as $key => $value) {
         $inputDescriptor = new FormInputDescriptor($key);

         if (is_string($value)) {
            Form::_resolveStringValue($value, $inputDescriptor);
         }
         elseif (is_array($value)) {
            Form::_resolveArrayValue($value, $inputDescriptor);
         }

         $this->inputDescriptorMap[$key] = $inputDescriptor;
      }

   }


   private function validateGroup($inputGroupDescriptor) {
      $allValidated = true;
      $keyValues = array();

      // Go over all keys and confirm if all have been validated.
      foreach ($inputGroupDescriptor->keys as $key) {
         $inputDescriptor = $this->inputDescriptorMap[$key];

         $keyValues[$key] = $inputDescriptor->value;

         if ( !($inputDescriptor->validated) ) {
            $allValidated = false;
            break;
         }
      }

      // If all keys associated with this input group have been validated, go
      // on and call the associated function.
      if ($allValidated) {
         $returnValue = call_user_func_array(
               $inputGroupDescriptor->fx, $keyValues);
         if ($returnValue !== true) {
            return $returnValue;
         }
      }

      return true;
   }


   // Go over each key in the given associative array $keyValueMap and for each
   // key, validate it individually by passing it to the Form::_validate()
   // internal function.
   // If the function returns false, add the returned error message to the list
   // of error messages $errorMessageList. 
   public function validate($keyValueMap, &$_validatedValues, &$_errorMessageList) {
      $errorMessageList = array();


      // First get done with per-key validation

      foreach ($keyValueMap as $key => $value) {
         $inputDescriptor = $this->inputDescriptorMap[$key];
         $isValid = Form::_validate($key, $value, $inputDescriptor, $_value);

         if (!$isValid) {
            array_push($errorMessageList, $_value);
         }
         else {
            $inputDescriptor->value = $_value;
         }
      }


      // Next, get done with per-group validation

      if ($this->inputGroupDescriptorList) {
         foreach ($this->inputGroupDescriptorList as $inputGroupDescriptor) {
            $isValid = $this->validateGroup($inputGroupDescriptor);
   
            if ($isValid !== true) {
               array_push($errorMessageList, $isValid);
            }
         }
      }

      // If $errorMessageList is not empty, this means that there are problems
      // and so we must fill the given reference parameter $_errorMessageList
      // with those problems.
      if (count($errorMessageList) !== 0) {
         $_errorMessageList = $errorMessageList;
         return false;
      }

      // Everything went perfectly upto this point, hence we must fill the
      // $_validatedValues reference parameter.
      $validatedValues = array();

      foreach ($keyValueMap as $key => $value) {
         $validatedValues[$key] = $this->inputDescriptorMap[$key]->value;
      }

      $_validatedValues = $validatedValues;
      return true;
   }

   public function setValidationFx($keys, $fx) {
      // check if $inputGroupDescriptorList is already an array
      if (!is_array($this->inputGroupDescriptorList)) {
         $this->inputGroupDescriptorList = array();
      }

      $inputGroupDescriptor = new FormInputGroupDescriptor($keys, $fx);
      array_push($this->inputGroupDescriptorList, $inputGroupDescriptor);

   }
}

// $form = new Form();
// $form->setSchema([
//    'email' => [
//       'type' => 'email',
//       'trim' => true
//    ],
//    'name' => 'required'
// ]);

// $success = $form->validate([
//    'email' => '   Bilal@dm.c',
//    'name' => '  Bill',
// ], $values, $errorMessageList);

// print_r($values);
// print_r($errorMessageList);

?>