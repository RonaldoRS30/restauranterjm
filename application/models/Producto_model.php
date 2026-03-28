<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    protected $table = 'productos';

    public function count_by_categoria($id_categoria)
    {
        return (int) $this->db->where('id_categoria', (int) $id_categoria)
            ->where('estado', 1)
            ->count_all_results($this->table);
    }

    public function count_by_categoria_busqueda($id_categoria, $q = '')
    {
        $this->db->where('id_categoria', (int) $id_categoria)->where('estado', 1);
        $this->_aplicar_busqueda($q);
        return (int) $this->db->count_all_results($this->table);
    }

    public function get_by_categoria($id_categoria)
    {
        return $this->db->where('id_categoria', (int) $id_categoria)
            ->where('estado', 1)
            ->order_by('nombre', 'ASC')
            ->get($this->table)
            ->result();
    }

    /**
     * @param int $id_categoria
     * @param int $limit
     * @param int $offset
     * @param string $q búsqueda por nombre
     */
    public function get_by_categoria_paginado($id_categoria, $limit, $offset, $q = '')
    {
        $this->db->where('id_categoria', (int) $id_categoria)->where('estado', 1);
        $this->_aplicar_busqueda($q);
        return $this->db->order_by('nombre', 'ASC')
            ->limit((int) $limit, (int) $offset)
            ->get($this->table)
            ->result();
    }

    private function _aplicar_busqueda($q)
    {
        $q = trim((string) $q);
        if ($q !== '') {
            $this->db->like('nombre', $q);
        }
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int) $id])->row();
    }

    public function get_by_id_con_categoria($id)
    {
        return $this->db->select('p.*, c.titulo AS categoria_titulo, c.id AS id_categoria')
            ->from($this->table . ' p')
            ->join('categorias c', 'c.id = p.id_categoria', 'left')
            ->where('p.id', (int) $id)
            ->get()
            ->row();
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

    /** Baja lógica */
    public function desactivar($id)
    {
        return $this->update($id, ['estado' => 0]);
    }

    /**
     * Productos activos agrupados por categoría para POS (JSON-friendly).
     * Usa LEFT JOIN: si la categoría falta, está inactiva o el id_categoria no coincide,
     * el producto igual aparece bajo "Sin categoría" (no se pierde en un INNER JOIN).
     */
    public function get_grouped_for_pos()
    {
        if (!$this->db->table_exists($this->table)) {
            return [];
        }
        $rows = $this->db->select('p.id, p.nombre, p.precio_venta, c.id AS cat_id, c.titulo AS cat_titulo, c.estado AS cat_estado')
            ->from($this->table . ' p')
            ->join('categorias c', 'c.id = p.id_categoria', 'left')
            ->where('p.estado', 1)
            ->order_by('c.titulo', 'ASC')
            ->order_by('p.nombre', 'ASC')
            ->get()
            ->result();

        $grouped = [];
        foreach ($rows as $r) {
            if ($r->cat_id !== null) {
                $cid = (int) $r->cat_id;
                $titulo = (string) $r->cat_titulo;
                if ((int) $r->cat_estado !== 1) {
                    $titulo .= ' (inactiva)';
                }
            } else {
                $cid = 0;
                $titulo = 'Sin categoría';
            }

            if (!isset($grouped[$cid])) {
                $grouped[$cid] = [
                    'id'        => $cid,
                    'titulo'    => $titulo,
                    'productos' => [],
                ];
            }
            $pv = $r->precio_venta !== null ? (float) $r->precio_venta : 0.0;
            $grouped[$cid]['productos'][] = [
                'id'     => (int) $r->id,
                'nombre' => $r->nombre,
                'precio' => $pv,
            ];
        }
        return array_values($grouped);
    }

    public function get_precio_venta($producto_id)
    {
        $row = $this->db->select('precio_venta, nombre, stock, estado')
            ->where('id', (int) $producto_id)
            ->get($this->table)
            ->row();
        if (!$row || !(int) $row->estado) {
            return null;
        }
        return $row;
    }
}
