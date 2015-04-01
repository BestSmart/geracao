<?php

namespace Model;


/**
 * @Entity @Table(name="credenciais")
 * */
class Credencial extends DefaultModel {

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;

    /** @Column(type="string") * */
    protected $login;

    /** @Column(type="string") * */
    protected $senha;

    /** @Column(type="string") * */
    protected $email;

    /** @Column(type="string") * */
    protected $salt;

    public function getId() {
        return $this->id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function setLogin($login) {
        $this->login = $login;
        return $this;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }
    
    public function expose() {
        $array = parent::expose();
        unset($array['senha']);
        unset($array['salt']);
        return $array;
    }

}
