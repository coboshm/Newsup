<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Contactar extends Zend_Db_Table
{
        protected $_name = "contactar";
        protected $_primary = "idContactar";

        public function insertRow($username,$nom,$email,$option,$message)
        {
            $contactar = $this->createRow();
            $contactar->username = $username;
            $contactar->nom = $nom;
            $contactar->email = $email;
            $contactar->opcio = $option;
            $contactar->missatge = $message;
            $contactar->save();
        }

        public function getAll(){
            $_User = new Users();
            $ContactarOk = array();
            $select = $this->select();
            $contactar = $this->fetchAll($select);
            $count=0;
            foreach($contactar as $contact){
                $ContactarOk[$count] = new Contactar($contact->idContactar, $contact->username, $contact->nom, $contact->email, $contact->opcio, $contact->missatge);
                $count++;
            }
            return $ContactarOk;
        }

        public function getMissatge($id){
            $_User = new Users();
            $ContactarOk = array();
            $select = $this->select();
            $select->where("idContactar = ?", $id);
            $contactar = $this->fetchAll($select);
            $count=0;
            foreach($contactar as $contact){
                $ContactarOk[$count] = new Contactar($contact->idContactar, $contact->username, $contact->nom, $contact->email, $contact->opcio, $contact->missatge);
                $count++;
            }
            return $ContactarOk;
        }

        public function removeMissatge($id){
            $id_missatge = (int)$id;
            $missatge = $this->getMissatge($id_missatge);
            $missatge = $this->find($id_missatge)->current();
            $missatge->delete();
        }

        public function getMissatgePerOpcio($opcio){
            $_User = new Users();
            $ContactarOk = array();
            $select = $this->select();
            $select->where("opcio = ?", $opcio);
            $contactar = $this->fetchAll($select);
            $count=0;
            foreach($contactar as $contact){
                $ContactarOk[$count] = new Contactar($contact->idContactar, $contact->username, $contact->nom, $contact->email, $contact->opcio, $contact->missatge);
                $count++;
            }
            return $ContactarOk;
        }

        public function getSearch($paraula){
            $_User = new Users();
            $ContactarOk = array();
            $select = $this->select();
            $select->where('username = ?',$paraula);
            $contactar = $this->fetchAll($select);
            $count=0;
            foreach($contactar as $contact){
                $ContactarOk[$count] = new Contactar($contact->idContactar, $contact->username, $contact->nom, $contact->email, $contact->opcio, $contact->missatge);
                $count++;
            }
            return $ContactarOk;
        }
}

?>