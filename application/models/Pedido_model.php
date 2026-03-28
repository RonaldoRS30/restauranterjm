<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedido_model extends CI_Model {

    protected $table_pedidos  = 'pedidos';
    protected $table_detalle = 'pedido_detalle';

    public function get_abierto_por_mesa($mesa_id)
    {
        if (!$this->db->table_exists($this->table_pedidos)) {
            return null;
        }
        return $this->db->where('mesa_id', (int) $mesa_id)
            ->where('estado', 'abierto')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get($this->table_pedidos)
            ->row();
    }

    public function get_by_id($id)
    {
        if (!$this->db->table_exists($this->table_pedidos)) {
            return null;
        }
        return $this->db->get_where($this->table_pedidos, ['id' => (int) $id])->row();
    }

    public function crear_abierto($mesa_id, $usuario_id, $tipo = 'salon')
    {
        $tipos = ['salon', 'para_llevar', 'delivery', 'barra'];
        if (!in_array($tipo, $tipos, true)) {
            $tipo = 'salon';
        }
        $data = [
            'mesa_id'    => $mesa_id ? (int) $mesa_id : null,
            'tipo'       => $tipo,
            'estado'     => 'abierto',
            'usuario_id' => $usuario_id ? (int) $usuario_id : null,
            'subtotal'   => 0,
            'servicio_monto' => 0,
            'total'      => 0,
        ];
        $this->db->insert($this->table_pedidos, $data);
        return $this->db->insert_id();
    }

    public function get_detalles($pedido_id)
    {
        if (!$this->db->table_exists($this->table_detalle)) {
            return [];
        }
        return $this->db->select('d.*, p.nombre AS producto_nombre')
            ->from($this->table_detalle . ' d')
            ->join('productos p', 'p.id = d.producto_id', 'left')
            ->where('d.pedido_id', (int) $pedido_id)
            ->order_by('d.id', 'ASC')
            ->get()
            ->result();
    }

    public function agregar_o_sumar_linea($pedido_id, $producto_id, $cantidad, $precio_unitario)
    {
        $pedido_id = (int) $pedido_id;
        $producto_id = (int) $producto_id;
        $cantidad = (float) $cantidad;
        $precio_unitario = round((float) $precio_unitario, 2);
        if ($cantidad <= 0) {
            return false;
        }

        $ex = $this->db->where('pedido_id', $pedido_id)
            ->where('producto_id', $producto_id)
            ->get($this->table_detalle)
            ->row();

        $sub = round($cantidad * $precio_unitario, 2);

        if ($ex) {
            $nueva_cant = (float) $ex->cantidad + $cantidad;
            $nuevo_sub = round($nueva_cant * $precio_unitario, 2);
            $this->db->where('id', (int) $ex->id)->update($this->table_detalle, [
                'cantidad'       => $nueva_cant,
                'precio_unitario'=> $precio_unitario,
                'subtotal_linea' => $nuevo_sub,
            ]);
        } else {
            $this->db->insert($this->table_detalle, [
                'pedido_id'        => $pedido_id,
                'producto_id'      => $producto_id,
                'cantidad'         => $cantidad,
                'precio_unitario'  => $precio_unitario,
                'subtotal_linea'   => $sub,
            ]);
        }

        $this->recalcular_totales($pedido_id);
        return true;
    }

    public function actualizar_cantidad_linea($detalle_id, $cantidad)
    {
        $cantidad = (float) $cantidad;
        if ($cantidad <= 0) {
            return $this->eliminar_linea($detalle_id);
        }
        $line = $this->db->get_where($this->table_detalle, ['id' => (int) $detalle_id])->row();
        if (!$line) {
            return false;
        }
        $pu = (float) $line->precio_unitario;
        $this->db->where('id', (int) $detalle_id)->update($this->table_detalle, [
            'cantidad'       => $cantidad,
            'subtotal_linea' => round($cantidad * $pu, 2),
        ]);
        $this->recalcular_totales((int) $line->pedido_id);
        return true;
    }

    public function eliminar_linea($detalle_id)
    {
        $line = $this->db->get_where($this->table_detalle, ['id' => (int) $detalle_id])->row();
        if (!$line) {
            return false;
        }
        $pid = (int) $line->pedido_id;
        $this->db->where('id', (int) $detalle_id)->delete($this->table_detalle);
        $this->recalcular_totales($pid);
        return true;
    }

    public function recalcular_totales($pedido_id)
    {
        $pedido_id = (int) $pedido_id;
        $row = $this->db->select_sum('subtotal_linea', 's')
            ->where('pedido_id', $pedido_id)
            ->get($this->table_detalle)
            ->row();
        $sum = $row && $row->s !== null ? (float) $row->s : 0.0;

        $pedido = $this->db->get_where($this->table_pedidos, ['id' => $pedido_id])->row();
        if (!$pedido) {
            return;
        }
        $pct = (float) $pedido->servicio_pct;
        $servicio = round($sum * ($pct / 100.0), 2);
        $total = round($sum + $servicio, 2);

        $this->db->where('id', $pedido_id)->update($this->table_pedidos, [
            'subtotal'       => $sum,
            'servicio_monto' => $servicio,
            'total'          => $total,
        ]);
    }

    public function actualizar_servicio_pct($pedido_id, $pct)
    {
        $pct = max(0.0, min(100.0, (float) $pct));
        $this->db->where('id', (int) $pedido_id)->update($this->table_pedidos, ['servicio_pct' => $pct]);
        $this->recalcular_totales($pedido_id);
    }

    public function cerrar_y_descontar_stock($pedido_id)
    {
        $pedido_id = (int) $pedido_id;
        $pedido = $this->get_by_id($pedido_id);
        if (!$pedido || $pedido->estado !== 'abierto') {
            return ['ok' => false, 'msg' => 'Pedido no válido o ya cerrado.'];
        }

        $lineas = $this->get_detalles($pedido_id);
        foreach ($lineas as $ln) {
            $prod = $this->db->get_where('productos', ['id' => (int) $ln->producto_id])->row();
            if (!$prod) {
                return ['ok' => false, 'msg' => 'Producto no encontrado en línea de pedido.'];
            }
            $necesario = (float) $ln->cantidad;
            $stock = (float) $prod->stock;
            if ($stock < $necesario) {
                return ['ok' => false, 'msg' => 'Stock insuficiente para: ' . $prod->nombre . ' (hay ' . $stock . ', se piden ' . $necesario . ').'];
            }
        }

        $this->db->trans_start();
        foreach ($lineas as $ln) {
            $prod = $this->db->get_where('productos', ['id' => (int) $ln->producto_id])->row();
            $nuevo_stock = (int) floor((float) $prod->stock - (float) $ln->cantidad);
            if ($nuevo_stock < 0) {
                $nuevo_stock = 0;
            }
            $this->db->where('id', (int) $ln->producto_id)->update('productos', ['stock' => $nuevo_stock]);
        }
        $this->db->where('id', $pedido_id)->update($this->table_pedidos, [
            'estado'     => 'cuenta_cerrada',
            'cerrado_at' => date('Y-m-d H:i:s'),
        ]);
        if (!empty($pedido->mesa_id)) {
            $this->db->where('id', (int) $pedido->mesa_id)->update('mesas', ['estado' => 'libre']);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'msg' => 'Error al cerrar la cuenta.'];
        }
        return ['ok' => true, 'msg' => 'Cuenta cerrada y stock actualizado.'];
    }

    public function anular($pedido_id)
    {
        $pedido = $this->get_by_id((int) $pedido_id);
        if (!$pedido || $pedido->estado !== 'abierto') {
            return false;
        }
        $n = (int) $this->db->where('pedido_id', (int) $pedido_id)->count_all_results($this->table_detalle);
        if ($n > 0) {
            return false;
        }
        $this->db->where('id', (int) $pedido_id)->update($this->table_pedidos, ['estado' => 'anulado']);
        if (!empty($pedido->mesa_id)) {
            $this->db->where('id', (int) $pedido->mesa_id)->update('mesas', ['estado' => 'libre']);
        }
        return true;
    }

    public function listar_del_dia($fecha = null)
    {
        if (!$this->db->table_exists($this->table_pedidos)) {
            return [];
        }
        $fecha = $fecha ?: date('Y-m-d');
        $ini = $fecha . ' 00:00:00';
        $fin = $fecha . ' 23:59:59';
        return $this->db->select('p.*, m.nombre AS mesa_nombre, m.codigo AS mesa_codigo')
            ->from($this->table_pedidos . ' p')
            ->join('mesas m', 'm.id = p.mesa_id', 'left')
            ->where('p.created_at >=', $ini)
            ->where('p.created_at <=', $fin)
            ->order_by('p.id', 'DESC')
            ->get()
            ->result();
    }
}
