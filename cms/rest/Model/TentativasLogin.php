<?php

namespace Model;
/**
 * @Entity @Table(name="tentativas_login")
 * */
class TentativasLogin {

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    protected $credencial;

    /** @Column(type="datetime") * */
    protected $time;

    public function getId() {
        return $this->id;
    }

    public function getCredencial() {
        return $this->credencial;
    }

    public function getTime() {
        return $this->time;
    }

    public function setCredencial($credencial) {
        $this->credencial = $credencial;
        return $this;
    }

    public function setTime($time) {
        $this->time = $time;
        return $this;
    }

}
