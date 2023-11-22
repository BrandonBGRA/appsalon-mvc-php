<?php

namespace Model;

class AdminCita extends ActiveRecord {
    protected static $tabla = 'citasServicios';
    protected static $columnasDB = ['id', 'hora', 'cliente', 'email', 'telefono', 'servicio', 'precio'];

    public $id;
    public $hora;
    public $cliente;
    public $email;
    public $telefono;
    public $servicio;
    public $precio;

    public function __construct()
    {
        $this->id = $args['id'] ?? null;
        $this->hora = $args['id'] ?? '';
        $this->cliente = $args['id'] ?? '';
        $this->email = $args['id'] ?? '';
        $this->telefono = $args['id'] ?? '';
        $this->servicio = $args['id'] ?? '';
        $this->precio = $args['id'] ?? '';
    }
}