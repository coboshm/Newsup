<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cataccio
 *
 * Marc Cobos
 *
 */
require_once 'BD/NoticiaTaula.php';

class Users extends Zend_Db_Table
{
        protected $_name = "users";
        protected $_primary = "id_user";

        public function setClientNou($nombre,$email,$contrasenya,$username,$data)
        {
            $client = $this->createRow();
            $client->email = addslashes($email);
            $client->nombre = addslashes($nombre);
            $client->password = addslashes($contrasenya);
            $client->foto = null;
            $client->username = addslashes($username);
            $client->data = $data;
            $client->save();
        }

        public function setImage($id,$image){
            $client = $this->find($id)->current();
            $client->foto = $image;
            $client->save();

        }

        public function getId($email)
        {
            $consulta = $this->select();
            $consulta->where("email = '".addslashes($email)."'");
            $users = $this->fetchAll($consulta);
           
            if($users != null){
                foreach($users as $user){
                    return $user->id_user;
                }
            }else{
                return null;
            }
        }


        public function setCodi($id,$codi){
            $client = $this->find($id)->current();
            $client->codi = $codi;
            $client->save();
        }

        public function getCodi($codi)
        {
            $select = $this->select();
            $select->where("codi = ?", $codi);
            $users = $this->fetchAll($select);
            foreach($users as $user){
                return new Usuari($user->id_user, $user->nombre, $user->email, $user->username, $user->foto);
            }
        }

        public function setActivat($id){
            $client = $this->find($id)->current();
            $client->activat = 1;
            $client->save();
        }


        public function getIdUsername($username){
            $consulta = $this->select();
            $consulta->where("username = '".addslashes($username)."'");
            $users = $this->fetchAll($consulta);
            foreach($users as $user){
                return $user->id_user;
            }
        }
        
        public function getUsername($id)
        {
            $consulta = $this->select();
            $consulta->where("id_user = ?", $id);
            $users = $this->fetchAll($consulta);
            foreach($users as $user){
                return $user->username;
            }
        }

        //Funcio que Modifica el password d'una ID d'usuari
        public function setPassword($id,$newpass){
            $consulta = $this->find($id)->current();
            $consulta->password = $newpass;
            $consulta->save();
        }

        public function getUsernameOk($username)
        {
            $consulta = $this->select();
            $consulta->where("username = '".addslashes($username)."'");
            $resultat = $this->fetchAll($consulta);
            return $resultat->count();
        }

        public function getEmail($email)
        {
            $consulta = $this->select();
            $consulta->where("email = '".addslashes($email)."'");
            $resultat = $this->fetchAll($consulta);
            return $resultat->count();
        }

        public function getImage($image){
            $consulta = $this->select();
            $consulta->where("foto='".addslashes($image)."'");
            $resultat = $this->fetchAll($consulta);
            return $resultat->count();
        }

        public function getUsuari($id){
            $UsuariOk = array();
            $select = $this->select();
            $select->where("id_user = ?", $id);
            $users = $this->fetchAll($select);
            foreach($users as $user){
                $UsuariOk[] = new Usuari($user->id_user, $user->nombre, $user->email, $user->username, $user->foto ,$user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament);
            }
            return $UsuariOk;
        }

        public function getUsuariTot($id){
            $select = $this->select();
            $select->where("id_user = ?", $id);
            $users = $this->fetchAll($select);
            foreach($users as $user){
                return new Usuari($user->id_user, $user->nombre, $user->email, $user->username, $user->foto, $user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament);
            }
        }

        //consulta per el buscador
        public function getNotciaSearch($paraula){
            $NoticiaTaula = new NoticiaTaula();
            $UsuariOk = array();
            $select = $this->select();
            $select->where('MATCH(username,Localidad,Tuvida,Aficiones) AGAINST(?)',$paraula);
            $users = $this->fetchAll($select);
            $count=0;
            foreach($users as $user){

                $UsuariOk[$count] = new Usuari($user->id_user, $user->nombre, $user->email, $user->username,$user->foto, $user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament);
                $cantitat = $NoticiaTaula->cantitatUserFora($user->id_user);
                $UsuariOk[$count]->addNoticies($cantitat);
                $count++;
            }
            return $UsuariOk;
        }

        public function updateDades($dades, $id)
        {
            $Id = (int)$id;
            $this->update($dades, "id_user = $Id");

        }

        public function getDadesById($id)
        {
            return $this->find($id)->current();
        }

        public function updatePassword($password,$id)
        {
            $consulta = $this->find($id)->current();
            $pass = sha1($password);
            $consulta->password = $pass;
            $consulta->save();

        }

        public function getDadesByEmail($email)
        {
            $select = $this->select();
            $select->where("email = ?", $email);
            $users = $this->fetchAll($select);
            foreach($users as $user){
                return new Usuari($user->id_user, $user->nombre, $user->email, $user->username, $user->foto, $user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament, $user->codi, $user->activat);
            }
        }

        public function setCookie($cookie,$id){
            $consulta = $this->find($id)->current();
            $consulta->cookie = $cookie;
            $consulta->save();
        }

        public function getDadesByCookie($cookie)
        {
            $select = $this->select();
            $select->where("cookie = ?", $cookie);
            $users = $this->fetchAll($select);
            foreach($users as $user){
                //Zend_Debug::dump($user->username);
                //Zend_Debug::dump($user->password);
                //die();
                return $user->username."-".$user->password;
            }
        }

        public function getDadesByUsername($username)
        {
            $select = $this->select();
            $select->where("username = ?", $username);
            $users = $this->fetchAll($select);
            foreach($users as $user){
                return new Usuari($user->id_user, $user->nombre, $user->email, $user->username, $user->foto, $user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament, $user->codi, $user->activat);
            }
        }

        public function allUsers()
        {
            $users = $this->fetchAll();
            foreach($users as $user){
                $UsuariOk[] = new Usuari($user->id_user, $user->nombre, $user->email, $user->username,$user->foto, $user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament, $user->codi, $user->activat);
            }
            return $UsuariOk;
        }

        public function getAllUsers()
        {
            $UsuariOk = array();
            $select = $this->select();
            $select->order("username ASC");
            $users = $this->fetchAll($select);
            $count=0;
            foreach($users as $user){
              $UsuariOk[$count] = new Usuari($user->id_user, $user->nombre,  $user->email, $user->username, $user->foto, $user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament, $user->codi, $user->activat);
              $count++;
            }
            return $UsuariOk;
        }
        
        public function removeUsuari($id){
            $id_usuari = (int)$id;
            $usuari = $this->getUsuariTot($id_usuari);
            $borrar = $this->find($id_usuari)->current();
            $borrar->delete();
        }
        //ADMIN USUARI FILTRE
        public function getUserFiltre($paraula){
            $NoticiaTaula = new NoticiaTaula();
            $UsuariOk = array();
            $select = $this->select();
            $select->where('username LIKE ?',$paraula."%");
            $users = $this->fetchAll($select);
            $count=0;
            foreach($users as $user){

                $UsuariOk[$count] = new Usuari($user->id_user, $user->nombre, $user->email, $user->username, $user->foto,$user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament, $user->codi, $user->activat);
                $cantitat = $NoticiaTaula->cantitatUserFora($user->id_user);
                $UsuariOk[$count]->addNoticies($cantitat);
                $count++;
            }
            return $UsuariOk;
        }

        public function getUserFiltreOtros(){
            $NoticiaTaula = new NoticiaTaula();
            $UsuariOk = array();
            $select = $this->select();
            $select->where('username NOT LIKE ?',"A%");
            $select->where('username NOT LIKE ?',"B%");
            $select->where('username NOT LIKE ?',"C%");
            $select->where('username NOT LIKE ?',"D%");
            $select->where('username NOT LIKE ?',"E%");
            $select->where('username NOT LIKE ?',"F%");
            $select->where('username NOT LIKE ?',"G%");
            $select->where('username NOT LIKE ?',"H%");
            $select->where('username NOT LIKE ?',"I%");
            $select->where('username NOT LIKE ?',"J%");
            $select->where('username NOT LIKE ?',"K%");
            $select->where('username NOT LIKE ?',"L%");
            $select->where('username NOT LIKE ?',"M%");
            $select->where('username NOT LIKE ?',"N%");
            $select->where('username NOT LIKE ?',"Ñ%");
            $select->where('username NOT LIKE ?',"O%");
            $select->where('username NOT LIKE ?',"P%");
            $select->where('username NOT LIKE ?',"Q%");
            $select->where('username NOT LIKE ?',"R%");
            $select->where('username NOT LIKE ?',"S%");
            $select->where('username NOT LIKE ?',"T%");
            $select->where('username NOT LIKE ?',"U%");
            $select->where('username NOT LIKE ?',"V%");
            $select->where('username NOT LIKE ?',"W%");
            $select->where('username NOT LIKE ?',"X%");
            $select->where('username NOT LIKE ?',"Y%");
            $select->where('username NOT LIKE ?',"Z%");
            $users = $this->fetchAll($select);
            $count=0;
            foreach($users as $user){

                $UsuariOk[$count] = new Usuari($user->id_user, $user->nombre, $user->email, $user->username, $user->foto,$user->Localidad, $user->Tuvida,$user->Aficiones, $user->Data_neixament);
                $cantitat = $NoticiaTaula->cantitatUserFora($user->id_user);
                $UsuariOk[$count]->addNoticies($cantitat);
                $count++;
            }
            return $UsuariOk;
        }

        public function allUsersDistinc()
        {
            $consulta = $this->select();
            $consulta->from($this,'DISTINCT(id_user)');
            $consulta->where('activat = 1');
            $consulta->order("id_user ASC");
            $users = $this->fetchAll($consulta);
            return $users;
        }

        public function allUsersDistinc2($limt)
        {
            //Zend_debug::Dump($limt);die;
            $consulta = $this->select();
            $consulta->from($this,'DISTINCT(id_user)');
            $consulta->where('activat = 1');
            $consulta->limit(15,$limt);
            $consulta->order("id_user ASC");
            $users = $this->fetchAll($consulta);
            return $users;
        }
}

