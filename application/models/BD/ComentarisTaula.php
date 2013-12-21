<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'NoticiaTaula.php';

class ComentarisTaula extends Zend_Db_Table
{
        protected $_name = "comentaris";
        protected $_primary = "id_comentari";

        public function getComentarisNoticia($idNoticia){
            $comentarisOk = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_noticia = ?", $idNoticia);
            $select->where("id_pare is null");
            $select->order("data DESC");
            $comentaris = $this->fetchAll($select);
            $x=0;
            foreach($comentaris as $comentari){
                $_VotsNeg = new VotsComentarisNeg();
                $_VotsPos = new VotsComentarisPos();
                $user = $_User->getUsuari($comentari->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                        $comentarisOk[$x] = new Comentari($comentari->id_comentari, $comentari->id_noticia, $usuarifinal, $comentari->data, $comentari->text);
                    }
                }
                $VotsPositius = $_VotsPos->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsPos($VotsPositius);
                $VotsNegatius = $_VotsNeg->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsNeg($VotsNegatius);
                //Comentaris Fills
                $Fills = $this->getFills($comentari->id_comentari);
                $comentarisOk[$x]->addFills($Fills);
                $x++;
            }
            return $comentarisOk;
        }

        public function getFills($id){
            $comentarisOk = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_pare = ?", $id);
            $select->order("data ASC");
            $comentaris = $this->fetchAll($select);
            $x=0;
            foreach($comentaris as $comentari){
                $_VotsNeg = new VotsComentarisNeg();
                $_VotsPos = new VotsComentarisPos();
                $user = $_User->getUsuari($comentari->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                        $comentarisOk[$x] = new Comentari($comentari->id_comentari, $comentari->id_noticia, $usuarifinal, $comentari->data, $comentari->text);
                    }
                }
                $VotsPositius = $_VotsPos->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsPos($VotsPositius);
                $VotsNegatius = $_VotsNeg->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsNeg($VotsNegatius);
                //Comentaris Fills
                $Fills = $this->getFills2($comentari->id_comentari);
                $comentarisOk[$x]->addFills($Fills);
                $x++;
            }
            return $comentarisOk;
        }

        public function getFills2($id){
            $comentarisOk = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_pare = ?", $id);
            $select->order("data ASC");
            $comentaris = $this->fetchAll($select);
            $x=0;
            foreach($comentaris as $comentari){
                $_VotsNeg = new VotsComentarisNeg();
                $_VotsPos = new VotsComentarisPos();
                $user = $_User->getUsuari($comentari->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                        $comentarisOk[$x] = new Comentari($comentari->id_comentari, $comentari->id_noticia, $usuarifinal, $comentari->data, $comentari->text);
                    }
                }
                $VotsPositius = $_VotsPos->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsPos($VotsPositius);
                $VotsNegatius = $_VotsNeg->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsNeg($VotsNegatius);
                //Comentaris Fills
                $Fills = $this->getFills3($comentari->id_comentari);
                $comentarisOk[$x]->addFills($Fills);
                $x++;
            }
            return $comentarisOk;
        }

        public function getFills3($id){
            $comentarisOk = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_pare = ?", $id);
            $select->order("data ASC");
            $comentaris = $this->fetchAll($select);
            $x=0;
            foreach($comentaris as $comentari){
                $_VotsNeg = new VotsComentarisNeg();
                $_VotsPos = new VotsComentarisPos();
                $user = $_User->getUsuari($comentari->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                        $comentarisOk[$x] = new Comentari($comentari->id_comentari, $comentari->id_noticia, $usuarifinal, $comentari->data, $comentari->text);
                    }
                }
                $VotsPositius = $_VotsPos->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsPos($VotsPositius);
                $VotsNegatius = $_VotsNeg->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsNeg($VotsNegatius);

                $x++;
            }
            return $comentarisOk;
        }

        public function getComentariIdUsuari($id){
            $select = $this->select();
            $select->where("id_comentari = ?", $id);
            $comentari = $this->fetchAll($select);
            foreach($comentari as $coment){
                return $coment->id_user;
            }
        }

        public function getComentari($id){
            
            $comentariOk = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_comentari = ?", $id);
            $comentari = $this->fetchAll($select);
            $x=0;
            foreach($comentari as $coment){
                $_VotsNeg = new VotsComentarisNeg();
                $_VotsPos = new VotsComentarisPos();
                $user = $_User->getUsuari($coment->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                        $comentariOk[] = new Comentari($coment->id_comentari,$coment->id_noticia,$user , $coment->data, $coment->text);
                    }
                }
                $VotsPositius = $_VotsPos->getVotsComentari($coment->id_comentari);
                $comentariOk[$x]->addVotsPos($VotsPositius);
                $VotsNegatius = $_VotsNeg->getVotsComentari($coment->id_comentari);
                $comentariOk[$x]->addVotsNeg($VotsNegatius);
                $x++;
            }
            return $comentariOk;

        }

        public function afegirComentari($idNoticia, $Usuari,$dataHora, $text){
            
            $comentari = $this->createRow();
            $comentari->id_noticia = $idNoticia;
            $comentari->id_user = $Usuari->id_user;
            $comentari->data = $dataHora;
            $comentari->text = $text;
            return $comentari->save();

        }

        public function afegirComentariFill($idNoticia, $Usuari,$dataHora, $text,$id_pare){
            $comentari = $this->createRow();
            $comentari->id_noticia = $idNoticia;
            $comentari->id_user = $Usuari->id_user;
            $comentari->data = $dataHora;
            $comentari->text = $text;
            $comentari->id_pare = $id_pare;
            return $comentari->save();
        }

        public function setPunt($id){
            $_VotsPos = new VotsComentarisPos();
            $_VotsNeg = new VotsComentarisNeg();
            $VotsPositius = $_VotsPos->getVotsComentari($id);
            $VotsNegatius = $_VotsNeg->getVotsComentari($id);
            $comentari = $this->find($id)->current();
            $comentari->punts = count($VotsPositius)-count($VotsNegatius);
            $comentari->save();
        }

         public function getComentarisNoticiaPunts($idNoticia){
            $comentarisOk = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_noticia = ?", $idNoticia);
            $select->where("id_pare is null");
            $select->order("punts DESC");
            $comentaris = $this->fetchAll($select);
            $x=0;
            foreach($comentaris as $comentari){
                $_VotsNeg = new VotsComentarisNeg();
                $_VotsPos = new VotsComentarisPos();
                $user = $_User->getUsuari($comentari->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                        $comentarisOk[$x] = new Comentari($comentari->id_comentari, $comentari->id_noticia, $usuarifinal, $comentari->data, $comentari->text);
                    }
                }
                $VotsPositius = $_VotsPos->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsPos($VotsPositius);
                $VotsNegatius = $_VotsNeg->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsNeg($VotsNegatius);
                //Comentaris Fills
                $Fills = $this->getFills($comentari->id_comentari);
                $comentarisOk[$x]->addFills($Fills);
                $x++;
            }
            return $comentarisOk;
        }

        public function getComentarisNoticiaAntic($idNoticia){
            $comentarisOk = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_noticia = ?", $idNoticia);
            $select->where("id_pare is null");
            $select->order("data ASC");
            $comentaris = $this->fetchAll($select);
            $x=0;
            foreach($comentaris as $comentari){
                $_VotsNeg = new VotsComentarisNeg();
                $_VotsPos = new VotsComentarisPos();
                $user = $_User->getUsuari($comentari->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                        $comentarisOk[$x] = new Comentari($comentari->id_comentari, $comentari->id_noticia, $usuarifinal, $comentari->data, $comentari->text);
                    }
                }
                $VotsPositius = $_VotsPos->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsPos($VotsPositius);
                $VotsNegatius = $_VotsNeg->getVotsComentari($comentari->id_comentari);
                $comentarisOk[$x]->addVotsNeg($VotsNegatius);
                //Comentaris Fills
                $Fills = $this->getFills($comentari->id_comentari);
                $comentarisOk[$x]->addFills($Fills);
                $x++;
            }
            return $comentarisOk;
        }

        //AGAFAR TOTS ELS COMENTARIS, PANELL DADMIN!
        public function getAllComentaris(){
            $comentariOk = array();
            $_User = new Users();
            $_Noticia = new NoticiaTaula();
            $select = $this->select();
            $select->order("data DESC");
            $comentari = $this->fetchAll($select);
            $count=0;
            foreach($comentari as $coment){
                $user = $_User->getUsuariTot($coment->id_user);
                $noticia = $_Noticia->getTitol($coment->id_noticia);
                //Zend_Debug::dump($noticia);
                if($noticia != null && $user!=null){
                    $comentariOk[$count] = new Comentari($coment->id_comentari, $noticia, $user , $coment->data, $coment->text);
                    $count++;
                    //Zend_Debug::dump($noticia);
                }
                //Zend_Debug::dump($noticia);

            }
            return $comentariOk;
        }

        public function getComentariId($idComentari){
            $comentariOk = array();
            $_User = new Users();
            $_Noticia = new NoticiaTaula();
            $select = $this->select();
            $select->where("id_comentari = ?", $idComentari);
            $comentari = $this->fetchAll($select);
            $count=0;
            foreach($comentari as $coment){
                $user = $_User->getUsuariTot($coment->id_user);
                $noticia = $_Noticia->getTitol($coment->id_noticia);
                //Zend_Debug::dump($noticia);
                if($noticia != null && $user!=null){
                    $comentariOk[$count] = new Comentari($coment->id_comentari, $noticia, $user , $coment->data, $coment->text);
                    $count++;
                    //Zend_Debug::dump($noticia);
                }
                //Zend_Debug::dump($noticia);

            }
            return $comentariOk;
        }

        public function removeComentari($id){
            $id_comentari = (int)$id;
            $comentari = $this->getComentariId($id_comentari);
            $borrar = $this->find($id_comentari)->current();
            $borrar->delete();
        }

        public function getComentarisUser($id){
            $comentariOk = array();
            $_User = new Users();
            $_Noticia = new NoticiaTaula();
            $select = $this->select();
            $select->where("id_user = ?", $id);
            $select->order("data DESC");
            $comentari = $this->fetchAll($select);
            $count=0;
            foreach($comentari as $coment){
                $user = $_User->getUsuariTot($coment->id_user);
                $noticia = $_Noticia->getTitol($coment->id_noticia);
                //Zend_Debug::dump($noticia);
                if($noticia != null && $user!=null){
                    $comentariOk[$count] = new Comentari($coment->id_comentari, $noticia, $user , $coment->data, $coment->text);
                    $count++;
                    //Zend_Debug::dump($noticia);
                }
                //Zend_Debug::dump($noticia);

            }
            return $comentariOk;
        }

        public function getComentarisText($id){
            $comentariOk = array();
            $_User = new Users();
            $_Noticia = new NoticiaTaula();
            $select = $this->select();
            $select->where("MATCH(text) AGAINST(?)", $id);
            $select->order("data DESC");
            $comentari = $this->fetchAll($select);
            $count=0;
            foreach($comentari as $coment){
                $user = $_User->getUsuariTot($coment->id_user);
                $noticia = $_Noticia->getTitol($coment->id_noticia);
                //Zend_Debug::dump($noticia);
                if($noticia != null && $user!=null){
                    $comentariOk[$count] = new Comentari($coment->id_comentari, $noticia, $user , $coment->data, $coment->text);
                    $count++;
                    //Zend_Debug::dump($noticia);
                }
                //Zend_Debug::dump($noticia);

            }
            return $comentariOk;
        }

        public function getComentarisIdNoticia($id){
            $comentariOk = array();
            $_User = new Users();
            $_Noticia = new NoticiaTaula();
            $select = $this->select();
            $select->where("id_noticia = ?", $id);
            $select->order("data DESC");
            $comentari = $this->fetchAll($select);
            $count=0;
            foreach($comentari as $coment){
                $user = $_User->getUsuariTot($coment->id_user);
                $noticia = $_Noticia->getTitol($coment->id_noticia);
                //Zend_Debug::dump($noticia);
                if($noticia != null && $user!=null){
                    $comentariOk[$count] = new Comentari($coment->id_comentari, $noticia, $user , $coment->data, $coment->text);
                    $count++;
                    //Zend_Debug::dump($noticia);
                }
                //Zend_Debug::dump($noticia);

            }
            return $comentariOk;
        }



}


?>