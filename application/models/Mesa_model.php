<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mesa_model extends CI_Model {

    protected $table = 'mesas';

    public function listar_activas()
    {
        if (!$this->db->table_exists($this->table)) {
            return [];
        }
        return $this->db->where('activo', 1)
            ->order_by('orden', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_by_id($id)
    {
        if (!$this->db->table_exists($this->table)) {
            return null;
        }
        return $this->db->get_where($this->table, ['id' => (int) $id])->row();
    }

    public function set_estado($id, $estado)
    {
        if (!in_array($estado, ['libre', 'ocupada'], true)) {
            return false;
        }
        if (!$this->db->table_exists($this->table)) {
            return false;
        }
        $this->db->where('id', (int) $id)->update($this->table, ['estado' => $estado]);
        return true;
    }
}
