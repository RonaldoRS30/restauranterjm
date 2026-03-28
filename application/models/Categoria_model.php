<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria_model extends CI_Model {

    protected $table = 'categorias';

    public function get_all()
    {
        return $this->db->order_by('id', 'DESC')->get($this->table)->result();
    }

    public function get_activas()
    {
        return $this->db->where('estado', 1)
            ->order_by('titulo', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int) $id])->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', (int) $id)->update($this->table, $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Slug único; en edición excluye el id indicado.
     */
    public function slug_exists($slug, $exclude_id = null)
    {
        $this->db->where('slug', $slug);
        if ($exclude_id !== null) {
            $this->db->where('id !=', (int) $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }
}
