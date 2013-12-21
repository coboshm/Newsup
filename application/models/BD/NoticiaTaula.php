<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Categories.php';
require_once 'Users.php';
require_once 'VotsTaula.php';
require_once 'Ranking.php';

class NoticiaTaula extends Zend_Db_Table
{
        protected $_name = "news";
        protected $_primary = "id_noticia";

        public function getNoticiesUsuari($Usuari,$y){
            $_Categories = new Categories();
            $user = new Usuari($Usuari->id_user,$Usuari->nombre,$Usuari->email,$Usuari->username,$Usuari->foto);
            $noticies = array();
            $select = $this->select();
            $select->where("id_user = ?", $Usuari->id_user);
            $select->order("data DESC");
            $select->limit(15,$y);
            
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x=0;
            foreach($noticies as $noticia){
                $_Comentaris = new ComentarisTaula();
                $_Vots = new VotsTaula();
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$user,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;

            }
            return $llistaNoticies;
        }

         public function getNoticiesUsuariFora($id,$y){
            $_Categories = new Categories();
            $noticies = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_user = ?", $id);
            $select->order("punts DESC");
            $select->limit(15,$y);

            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x=0;
            foreach($noticies as $noticia){
                $_Comentaris = new ComentarisTaula();
                $_Vots = new VotsTaula();
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = null;
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;

            }
            return $llistaNoticies;
        }


         public function getNoticiesUsuariPunts($Usuari,$y){
            $_Categories = new Categories();
            $user = new Usuari($Usuari->id_user,$Usuari->nombre,$Usuari->primer_apellido,$Usuari->segundo_apellido,$Usuari->email,$Usuari->username,$Usuari->foto);
            $noticies = array();
            $select = $this->select();
            $select->where("id_user = ?", $Usuari->id_user);
            $select->order("punts DESC");
            $select->limit(15,$y);

            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x=0;
            foreach($noticies as $noticia){
                $_Comentaris = new ComentarisTaula();
                $_Vots = new VotsTaula();
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$user,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;

            }
            return $llistaNoticies;
        }

        public function cantitatUser($Usuari){
            $select = $this->select();
            $select->where("id_user = ?", $Usuari->id_user);
            $noticies = $this->fetchAll($select);
            $x = count($noticies);
            return $x;
        }

        public function cantitatUserFora($id){
            $select = $this->select();
            $select->where("id_user = ?", $id);
            $noticies = $this->fetchAll($select);
            $x = count($noticies);
            return $x;
        }

        public function getNoticies($y){
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            // $select->where("id_user = ?", $Usuari->id_user);
            $select->order("punts DESC");
            $select->limit(15,$y);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;
            }
            return $llistaNoticies;
        }

        public function getNoticia($id){
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where("id_noticia = ?", $id);

            $noticies = $this->fetchAll($select);
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $noticiaNova = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $noticiaNova->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $noticiaNova->addComents($ComentarisNoticia);
                return $noticiaNova;
            }

            
        }

        public function setNoticia($imatge, $titol, $descripcio, $data, $usuari,$url,$categoria){
            $projecte = $this->createRow();
            $projecte->titol = $titol;
            $projecte->descripcio = $descripcio;
            $projecte->url = $url;
            $projecte->punts = 0;
            $projecte->id_user = $usuari->id_user;
            $projecte->imatge = $imatge;
            $projecte->id_categoria = $categoria;
            $projecte->data = $data;
            $projecte->save();
        }

        public function setPositionNoticia($id,$latitude,$longitude){
            $noticia = $this->find($id)->current();
            $noticia->lat = $latitude;
            $noticia->longi = $longitude;
            $noticia->save();
        }

        public function setPunt($id){
            $_Vots = new VotsTaula();
            $VotsNoticia = $_Vots->getVotsNoticia($id);
            $noticia = $this->find($id)->current();
            $noticia->punts = count($VotsNoticia);
            $noticia->save();
        }

         public function getNoticiesFiltreData($datatime,$x){
            
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where("data >= ?", $datatime);
            $select->order("punts DESC");
            $select->limit(15,$x);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;
            }
            return $llistaNoticies;
        }

        public function cantitat(){

            $select = $this->select();
            $noticies = $this->fetchAll($select);
            $x = count($noticies);
            return $x;
        }

        public function cantitatData($datatime){
            $select = $this->select();
            $select->where("data >= ?", $datatime);
            $noticies = $this->fetchAll($select);
            $x = count($noticies);
            return $x;
        }

        public function getNoticiesRecientes($x){

            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->order("data DESC");
            $select->limit(15,$x);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;
            }
            return $llistaNoticies;
        }

        public function getNoticiesCategoriaData($id,$y,$data){

            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where("id_categoria = ?", $id);
            $select->where("data >= ?", $data);
            $select->order("punts DESC");
            $select->limit(15,$y);
            $noticies = $this->fetchAll($select);
            //Zend_Debug::dump($datatime);

            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;
            }
            return $llistaNoticies;
        }

        public function cantitatCategori($id){
            $select = $this->select();
            $select->where("id_categoria = ?", $id);
            $noticies = $this->fetchAll($select);
            $x = count($noticies);
            return $x;
        }

        public function cantitatCategoriaData($id,$datatime){
            $select = $this->select();
            $select->where("id_categoria = ?", $id);
            $select->where("data >= ?", $datatime);
            $noticies = $this->fetchAll($select);
            $x = count($noticies);
            return $x;
        }
        public function getNoticiesCategoria($id,$y){

            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where("id_categoria = ?", $id);
            $select->order("punts DESC");
            $select->limit(15,$y);
            $noticies = $this->fetchAll($select);
            //Zend_Debug::dump($datatime);

            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;
            }
            return $llistaNoticies;
        }

        public function getNoticiesCategoriaRecientes($id,$y){

            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where("id_categoria = ?", $id);
            $select->order("data DESC");
            $select->limit(15,$y);
            $noticies = $this->fetchAll($select);
            //Zend_Debug::dump($datatime);

            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;
            }
            return $llistaNoticies;
        }


//consulta per el buscador
        public function getNotciaSearch($paraula){
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where('MATCH(url,titol,descripcio) AGAINST(?)',$paraula);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x=0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;

            }
            return $llistaNoticies;
        }
        
        //consulta per el buscador filtre de una data
        public function getNotciaSearchUnDia($paraula,$data){
            //Zend_Debug::dump($paraula);
            $_Categories = new Categories();
            $_User = new Users();
            $_user2=new Users();
            $noticies = array();
            $select = $this->select();
            $select->where('MATCH(url,titol,descripcio) AGAINST(?)',$paraula);
            $select->where('data>=?',$data);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            //Zend_Debug::dump(count($noticies));
            $x=0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;

            }
            return $llistaNoticies;
        }

         //consulta per el buscador filtre de una data
        public function getNotciaSearchUnDiaCategoria($paraula,$data,$idCategoria){
           // Zend_Debug::dump($paraula);
            $_Categories = new Categories();
            $_User = new Users();
            $_user2=new Users();
            $noticies = array();
            $select = $this->select();
            $select->where('MATCH(url,titol,descripcio) AGAINST(?)',$paraula);
            $select->where('data>=?',$data);
            $select->where('id_categoria = ?',$idCategoria);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            //Zend_Debug::dump(count($noticies));
            $x=0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;

            }
            return $llistaNoticies;
        }


        public function getNotciaSearchCategoria($paraula,$id){
            //Zend_Debug::dump($paraula);
            //Zend_Debug::dump($id);
            $_Categories = new Categories();
            $_User = new Users();
            $_user2=new Users();
            $noticies = array();
            $select = $this->select();
            $select->where('MATCH(url,titol,descripcio) AGAINST(?)',$paraula);
            $select->where('id_categoria = ?',$id);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            //Zend_Debug::dump($llistaNoticies);
            $x=0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;

            }
            return $llistaNoticies;
        }

        //funcio per agafar les noticies per el mapa amb data

       public function getNoticiesMapa(){

            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where('lat IS NOT NULL and longi IS NOT NULL');
            $select->order("punts DESC");
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $llistaNoticies[$x]->addComents($ComentarisNoticia);
                $x++;
            }
            return $llistaNoticies;
        }
    
        public function getUserNoticia($id){
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where("id_noticia = ?", $id);

            $noticies = $this->fetchAll($select);
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       return $usuarifinal->getId();
                    }
                }
            }

        }

        public function getTitol($id){
    $consulta = $this->select();
    $consulta->where("id_noticia = ?", $id);
    $noticies = $this->fetchAll($consulta);
    foreach($noticies as $noticia){
        return $noticia->titol;
    }
}

        public function getDescripcio($id){
            $consulta = $this->select();
            $consulta->where("id_noticia = ?", $id);
            $noticies = $this->fetchAll($consulta);
            foreach($noticies as $noticia){
                return $noticia->descripcio;
            }
        }

        public function getNoticiesSeguides($id,$x,$datatime){
     
            $_Categories = new Categories();
            $_User = new Users();
            $consulta = $this->select();
            $consulta->where("id_user IN(?)", $id);
            $consulta->where("data >= ?", $datatime);
            $consulta->order("punts DESC");
            $consulta->limit(15,$x);
            $noticies = $this->fetchAll($consulta);
            $noticiesOk = array();
            $contador = 0;
            $_Comentaris = new ComentarisTaula();

            foreach($noticies as $noticia){
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = null;
                $user = $_User->getUsuari($noticia->id_user);
                $_Vots = new VotsTaula();
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $noticiesOk[$contador] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $noticiesOk[$contador]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $noticiesOk[$contador]->addComents($ComentarisNoticia);
                $contador++;
            }
            return $noticiesOk;
        }

        public function cantitatNoticiesSeguides($id,$datatime){

                    $consulta = $this->select();
                    $consulta->where("id_user IN(?)", $id);
                    $consulta->where("data >= ?", $datatime);
                    $noticies = $this->fetchAll($consulta);
                    return count($noticies);
        }

        public function cantitatNoticiesSeguidesTotes($id){

                    $consulta = $this->select();
                    $consulta->where("id_user IN(?)", $id);
                    $noticies = $this->fetchAll($consulta);
                    return count($noticies);
        }

         public function getNoticiesSeguidesTotes($id,$x){

            $_Categories = new Categories();
            $_User = new Users();
            $consulta = $this->select();
            $consulta->where("id_user IN(?)", $id);
            $consulta->order("punts DESC");
            $consulta->limit(15,$x);
            $noticies = $this->fetchAll($consulta);
            $noticiesOk = array();
            $contador = 0;
            $_Comentaris = new ComentarisTaula();

            foreach($noticies as $noticia){
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = null;
                $user = $_User->getUsuari($noticia->id_user);
                $_Vots = new VotsTaula();
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $noticiesOk[$contador] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $noticiesOk[$contador]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $noticiesOk[$contador]->addComents($ComentarisNoticia);
                $contador++;
            }
            return $noticiesOk;
        }

            public function getNoticiesSeguidesTotesRecientes($id,$x){

            $_Categories = new Categories();
            $_User = new Users();
            $consulta = $this->select();
            $consulta->where("id_user IN(?)", $id);
            $consulta->order("data DESC");
            $consulta->limit(15,$x);
            $noticies = $this->fetchAll($consulta);
            $noticiesOk = array();
            $contador = 0;
            $_Comentaris = new ComentarisTaula();

            foreach($noticies as $noticia){
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = null;
                $user = $_User->getUsuari($noticia->id_user);
                $_Vots = new VotsTaula();
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $noticiesOk[$contador] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                $noticiesOk[$contador]->addVots($VotsNoticia);
                $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                $noticiesOk[$contador]->addComents($ComentarisNoticia);
                $contador++;
            }
            return $noticiesOk;
        }

        //PANELL ADMINISTRACIÓ
        public function getAllNoticies(){
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            // $select->where("id_user = ?", $Usuari->id_user);
            $select->order("punts DESC");
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                if($_User->getAllUsers()!=NULL){
                    $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                    $llistaNoticies[$x]->addVots($VotsNoticia);
                    $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                    $llistaNoticies[$x]->addComents($ComentarisNoticia);
                }
                $x++;
            }
            return $llistaNoticies;
        }

        public function removeNoticia($id){
            $id_noticia = (int)$id;
            $noticia = $this->getNoticia($id_noticia);
            $borrar = $this->find($id_noticia)->current();
            $borrar->delete();
        }

        public function editNoticia($id, $titol, $descripcio){
            $id_noticia = (int)$id;
            $projecte = $this->find($id_noticia)->current();
            $projecte->titol = $titol;
            $projecte->descripcio = $descripcio;
            $projecte->save();
        }
        public function getNoticiesUsuariFilre($id){
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where("id_user = ?", $id);
            $select->order("punts DESC");
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x = 0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $user = null;
                $categoria = null;
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                if(count($user)==1){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                if($_User->getAllUsers()!=NULL){
                    $VotsNoticia = $_Vots->getVotsNoticia($noticia->id_noticia);
                    $llistaNoticies[$x]->addVots($VotsNoticia);
                    $ComentarisNoticia = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                    $llistaNoticies[$x]->addComents($ComentarisNoticia);
                }
                $x++;
            }
            return $llistaNoticies;
        }

        public function getNotciaSearchAmbComentaris($paraula){
            $_Categories = new Categories();
            $_User = new Users();
            $noticies = array();
            $select = $this->select();
            $select->where('MATCH(url,titol,descripcio) AGAINST(?)',$paraula);
            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x=0;
            foreach($noticies as $noticia){
                $_Vots = new VotsTaula();
                $_Comentaris = new ComentarisTaula();
                $categoria = $_Categories->getCategoria($noticia->id_categoria);
                $user = $_User->getUsuari($noticia->id_user);
                $comentari = $_Comentaris->getComentarisNoticia($noticia->id_noticia);
                if(count($user)==1 && count($comentari)>0){
                    foreach($user as $usuarifinal){
                       $llistaNoticies[$x] = new Noticia($noticia->id_noticia,$categoria,$noticia->titol,$noticia->descripcio,$usuarifinal,$noticia->data,$noticia->url,$noticia->imatge,$noticia->lat,$noticia->longi);
                    }
                }
                $x++;

            }
            return $llistaNoticies;
        }

        public function setNullNoticiaMapa($id){
            $id_noticia = (int)$id;
            $noticia = $this->find($id_noticia)->current();
            $noticia->lat = null;
            $noticia->longi = null;
            $noticia->save();
        }

        
        public function allNoticiasRanking()
        {   
            $_User = new Users();
            $users = $_User->allUsersDistinc();
            $puntos  = array();
            $userRanking = array();
            /**$consulta = $this->select();
            $consulta->from($this,'DISTINCT(id_user)'); 
            $consulta->order("id_user ASC");
            $noticiasRowSet = $this->fetchAll($consulta);*/
            foreach ($users as $noticiasRow)
            {
                $puntosNoticia = 0;
                $contadorNoticias = 0;                         
                $notciaUserRowSet = $this->getNoticiesUsuariRanking($noticiasRow->id_user);
                
                foreach ($notciaUserRowSet as $notciaUserRow)
                {                   
                    $puntosNoticia += (int)$notciaUserRow;
                    $contadorNoticias++;
                }
                $puntos[] = $puntosNoticia;
                $userRanking[] = new Ranking($noticiasRow->id_user,$puntosNoticia,$contadorNoticias);
            }
            $n = count($puntos);

            for ($i = 1; $i<$n; $i++) {
                for ($j = $n-1; $j >= $i; $j--) {
                    if ($userRanking[$j-1]->getPuntos() < $userRanking[$j]->getPuntos()) {
                        $aux = $userRanking[$j];
                        $userRanking[$j] = $userRanking[$j-1];
                        $userRanking[$j-1] = $aux;
                    }
                }
            }

            
            return $userRanking;               
        }
        /*public function getNoticiesUsuariRanking($id){
            //$_Categories = new Categories();
            $noticies = array();
            $_User = new Users();
            $select = $this->select();
            $select->where("id_user = ?", $id);
            $select->order("punts DESC");

            $noticies = $this->fetchAll($select);
            $llistaNoticies = array();
            $x=0;
            $punts = array();;
            foreach($noticies as $noticia){
               $punts[] = $noticia->punts;

            }
            return $punts;
        }*/
        /*public function allNoticiasRankingMedia()
        {   
            $_User = new Users();
            $users = $_User->allUsersDistinc();
            $puntos  = array();
            $userRanking = array();
            /*$consulta = $this->select();
            $consulta->from($this,'DISTINCT(id_user)'); 
            $consulta->order("id_user ASC");
            $noticiasRowSet = $this->fetchAll($consulta);
            foreach ($users as $noticiasRow)
            {
                $puntosNoticia = 0;
                $contadorNoticias = 0;                         
                $notciaUserRowSet = $this->getNoticiesUsuariRanking($noticiasRow->id_user);
                
                foreach ($notciaUserRowSet as $notciaUserRow)
                {                   
                    $puntosNoticia += (int)$notciaUserRow;
                    $contadorNoticias++;
                }
                
                $userRanking[] = new Ranking($noticiasRow->id_user,$puntosNoticia,$contadorNoticias);
            }
            
            foreach ($userRanking as $user){
                $puntos[] = $user->getMediaPuntos();
            }
            $n = count($puntos);
            
            for ($i = 1; $i<$n; $i++) {
                for ($j = $n-1; $j >= $i; $j--) {
                    if ($userRanking[$j-1]->getMediaPuntos() < $userRanking[$j]->getMediaPuntos()) {
                        $aux = $userRanking[$j];
                        $userRanking[$j] = $userRanking[$j-1];
                        $userRanking[$j-1] = $aux;
                    }
                }
            }
            
            return $userRanking;               
        }*/
        
        /////////////////////////////////////////////////////////////
        
        /*public function getNoticiasRankingLimit($limt)
        {   
            $_User = new Users();
            $users = $_User->allUsersDistinc();
            
            $userRanking = array();
            /*$consulta = $this->select();
            $consulta->from($this,'DISTINCT(id_user)'); 
            $consulta->order("id_user ASC");
            $consulta->limit(15,$limt);
            $noticiasRowSet = $this->fetchAll($consulta);
            foreach ($users as $noticiasRow)
            {
                $puntosNoticia = 0;
                $contadorNoticias = 0;                         
                $notciaUserRowSet = $this->getNoticiesUsuariRanking($noticiasRow->id_user);
                
                foreach ($notciaUserRowSet as $notciaUserRow)
                {                   
                    $puntosNoticia += (int)$notciaUserRow;
                    $contadorNoticias++;
                }
                $puntos[] = $puntosNoticia;
                $userRanking[] = new Ranking($noticiasRow->id_user,$puntosNoticia,$contadorNoticias);
            }
            $n = count($puntos);

            for ($i = 1; $i<$n; $i++) {
                for ($j = $n-1; $j >= $i; $j--) {
                    if ($userRanking[$j-1]->getPuntos() < $userRanking[$j]->getPuntos()) {
                        $aux = $userRanking[$j];
                        $userRanking[$j] = $userRanking[$j-1];
                        $userRanking[$j-1] = $aux;
                    }
                }
            }

            
            return $userRanking;               
        }*/
        
        public function countRanking()
        {
            $_User = new Users();
            $users = $_User->allUsersDistinc();   
            return count($users);
        }
        
        /*public function getNoticiasRankingMediaLimit($limt)
        {   
            $_User = new Users();
            $users = $_User->allUsersDistinc();
            
            $puntos  = array();
            $userRanking = array();
            /*$consulta = $this->select();
            $consulta->from($this,'DISTINCT(id_user)'); 
            $consulta->order("id_user ASC");
            $consulta->limit(15,$limt);
            $noticiasRowSet = $this->fetchAll($consulta);
            foreach ($users as $noticiasRow)
            {
                $puntosNoticia = 0;
                $contadorNoticias = 0;                         
                $notciaUserRowSet = $this->getNoticiesUsuariRanking($noticiasRow->id_user);
                
                foreach ($notciaUserRowSet as $notciaUserRow)
                {                   
                    $puntosNoticia += (int)$notciaUserRow;
                    $contadorNoticias++;
                }
                
                $userRanking[] = new Ranking($noticiasRow->id_user,$puntosNoticia,$contadorNoticias);
            }
            
            foreach ($userRanking as $user){
                $puntos[] = $user->getMediaPuntos();
            }
            //$puntos[] = $puntosNoticia;
            $n = count($puntos);
            
            for ($i = 1; $i<$n; $i++) {
                for ($j = $n-1; $j >= $i; $j--) {
                    if ($userRanking[$j-1]->getMediaPuntos() < $userRanking[$j]->getMediaPuntos()) {
                        $aux = $userRanking[$j];
                        $userRanking[$j] = $userRanking[$j-1];
                        $userRanking[$j-1] = $aux;
                    }
                }
            }
            
            return $userRanking;               
        }*/

        /*public function getPuntosByIdUser()
        {
            $select = $this->select();
            
            $select->where('MATCH(url,titol,descripcio) AGAINST(?)',$paraula);
            $noticies = $this->fetchAll($select);
            
        }*/
        
        public function getNoticiasRankingLimit($limt)
        {
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('u' => 'users'), array());
            $select->joinleft(array('n' => 'news'), 'u.id_user = n.id_user', array('u.id_user', 'IFNULL(SUM(n.punts),0) as punts', 'COUNT(n.id_noticia) as news'));
            $select->where('u.activat = 1');
            $select->group('u.id_user');
            $select->order('punts DESC');
            
            $select->limit(15,$limt);
            //Zend_Debug::Dump($select->__toString());die;
            $newsRowSet = $this->fetchAll($select);
            foreach ($newsRowSet as $newsRow)
            {
                if($newsRow->id_user != null){
                    $userRanking[] = new Ranking($newsRow->id_user,$newsRow->punts,$newsRow->news);
                }
            }
            return $userRanking;
        }
        
        public function getNoticiasRankingMediaLimit($limt)
        {  
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('u' => 'users'), array());
            $select->joinleft(array('n' => 'news'), 'u.id_user = n.id_user', array('u.id_user', 'AVG(IFNULL(n.punts,0)) as punts', 'COUNT(n.id_noticia) as news'));
            $select->where('u.activat = 1');
            $select->group('u.id_user');
            $select->order('punts DESC');
            
            $select->limit(15,$limt);
            $newsRowSet = $this->fetchAll($select);
            foreach ($newsRowSet as $newsRow)
            {
                if($newsRow->id_user != null){
                    $userRanking[] = new Ranking($newsRow->id_user,$newsRow->punts,$newsRow->news);
                }
            }
            return $userRanking;
        }
        //funcio per trobar la posicio
        public function getNoticiasRanking()
        {
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('u' => 'users'), array());
            $select->joinleft(array('n' => 'news'), 'u.id_user = n.id_user', array('u.id_user', 'IFNULL(SUM(n.punts),0) as punts', 'COUNT(n.id_noticia) as news'));
            $select->where('u.activat = 1');
            $select->group('u.id_user');
            $select->order('punts DESC');
            $newsRowSet = $this->fetchAll($select);
            foreach ($newsRowSet as $newsRow)
            {
                if($newsRow->id_user != null){
                    $userRanking[] = new Ranking($newsRow->id_user,$newsRow->punts,$newsRow->news);
                }
            }
            return $userRanking;
        }
        
        //funcio per trobar la posicio en la media
        public function getNoticiasRankingMedia()
        {  
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('u' => 'users'), array());
            $select->joinleft(array('n' => 'news'), 'u.id_user = n.id_user', array('u.id_user', 'AVG(IFNULL(n.punts,0)) as punts', 'COUNT(n.id_noticia) as news'));
            $select->where('u.activat = 1');
            $select->group('u.id_user');
            $select->order('punts DESC');
            $newsRowSet = $this->fetchAll($select);
            foreach ($newsRowSet as $newsRow)
            {
                if($newsRow->id_user != null){
                    $userRanking[] = new Ranking($newsRow->id_user,$newsRow->punts,$newsRow->news);
                }
            }
            return $userRanking;
        }
}
?>