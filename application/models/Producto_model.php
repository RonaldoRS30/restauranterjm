<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base para lógica futura por categoría (listados, CRUD, reglas distintas).
 */
class Producto_model extends CI_Model {

    protected $table = 'productos';

    public function count_by_categoria($id_categoria)
    {
        return (int) $this->db->where('id_categoria', (int) $id_categoria)
            ->where('estado', 1)
            ->count_all_results($this->table);
    }

    public function get_by_categoria($id_categoria)
    {
        return $this->db->where('id_categoria', (int) $id_categoria)
            ->where('estado', 1)
            ->order_by('nombre', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}
