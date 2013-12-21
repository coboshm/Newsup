<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AdminsTaula extends Zend_Db_Table
{
        protected $_name = "admins";
        protected $_primary = "idAdmin";

        public function insertRow($username,$password,$nom,$email)
        {
            $admins = $this->createRow();
            $admins->username = $username;
            $admins->password = $password;
            $admins->nom = $nom;
            $admins->email = $email;
            $admins->save();
        }

        public function getAllAdmins(){
            $AdminsOk = array();
            $select = $this->select();
            $select->order("username ASC");
            $admins = $this->fetchAll($select);
            $count=0;
            foreach($admins as $admin){
                $AdminsOk[$count] = new Admin($admin->idAdmin, $admin->username, $admin->password ,$admin->nom, $admin->email);
                $count++;
            }
            return $AdminsOk;
        }

        public function getAdmin($id){
            $AdminsOk = array();
            $select = $this->select();
            $select->where("idAdmin = ?", $id);
            $admins = $this->fetchAll($select);
            $count=0;
            foreach($admins as $admin){
                $AdminsOk[$count] = new Admin($admin->idAdmin, $admin->username, $admin->password ,$admin->nom, $admin->email);
                $count++;
            }
            return $AdminsOk;
        }

        public function removeAdmin($id){
            $idAdmin = (int)$id;
            $admin = $this->getAdmin($idAdmin);
            $admin = $this->find($idAdmin)->current();
            $admin->delete();
        }

        public function editAdmin($id, $username, $password, $nom, $email){
            $idAdmin = (int)$id;
            $consulta = $this->find($idAdmin)->current();
            $consulta->username = $username;
            $consulta->password = $password;
            $consulta->nom = $nom;
            $consulta->email = $email;
            $consulta->save();
        }
}
?>