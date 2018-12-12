<?php

namespace Tool\Security;

/**
 * User Interface
 */
interface UserInterface
{

   /**
    * The roles, of user
    * @var array, $roles
    */
   private $roles;

   /**
    * User, Password
    * @var string, $password
    */
   private $password;


   public function getRoles()
   {
      return $this->roles;
   }

   public function setRoles(array $roles)
   {
      $this->roles = $roles;
      return $this;
   }

   public function getPassword()
   {
      return $this->password;
   }

   public function setPassword(string $password)
   {
      $this->password = $password;
      return $this;
   }


}
