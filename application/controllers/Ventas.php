<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Punto de venta y comandas (pedidos vinculados a productos y mesas).
 */
class Ventas extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id')) {
            redirect('login');
        }
        $this->load->model('mesa_model');
        $this->load->model('pedido_model');
        $this->load->model('producto_model');
    }

    public function pos()
    {
        $mesa_id = (int) $this->input->get('mesa_id');
        $mesas = $this->mesa_model->listar_activas();
        $categorias_pos = $this->producto_model->get_grouped_for_pos();
        $mesa_activa = null;
        $pedido = null;
        $lineas = [];

        if ($mesa_id > 0) {
            $mesa_activa = $this->mesa_model->get_by_id($mesa_id);
            if ($mesa_activa) {
                $pedido = $this->pedido_model->get_abierto_por_mesa($mesa_id);
                if ($pedido) {
                    $lineas = $this->pedido_model->get_detalles($pedido->id);
                }
            }
        }

        $pos_ok = $this->db->table_exists('pedidos') && $this->db->table_exists('mesas');

        $data = [
            'mesas'           => $mesas,
            'categorias_pos'  => $categorias_pos,
            'mesa_activa'     => $mesa_activa,
            'mesa_id_get'     => $mesa_id,
            'pedido'          => $pedido,
            'lineas'          => $lineas,
            'pos_ok'          => $pos_ok,
            'usuario_nombre'  => $this->session->userdata('nombre_completo') ?: $this->session->userdata('usuario'),
        ];
        $this->load->view('ventas/pos', $data);
    }

    public function comandas()
    {
        $fecha = $this->input->get('fecha', true) ?: date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = date('Y-m-d');
        }
        $data['fecha'] = $fecha;
        $data['pedidos'] = $this->db->table_exists('pedidos')
            ? $this->pedido_model->listar_del_dia($fecha)
            : [];
        $data['pos_ok'] = $this->db->table_exists('pedidos');
        $this->load->view('ventas/comandas', $data);
    }

    public function api_abrir_pedido()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        if (!$this->db->table_exists('pedidos')) {
            return $this->_json(['ok' => false, 'msg' => 'Ejecuta application/sql/restaurant_gestion.sql']);
        }
        $mesa_id = (int) $this->input->post('mesa_id');
        $tipo = $this->input->post('tipo', true) ?: 'salon';
        if ($mesa_id < 1) {
            return $this->_json(['ok' => false, 'msg' => 'Mesa no válida.']);
        }
        $mesa = $this->mesa_model->get_by_id($mesa_id);
        if (!$mesa) {
            return $this->_json(['ok' => false, 'msg' => 'Mesa no encontrada.']);
        }
        $ex = $this->pedido_model->get_abierto_por_mesa($mesa_id);
        if ($ex) {
            return $this->_json(['ok' => true, 'pedido_id' => (int) $ex->id, 'msg' => 'Ya hay una cuenta abierta.']);
        }
        $uid = (int) $this->session->userdata('id');
        $pid = $this->pedido_model->crear_abierto($mesa_id, $uid, $tipo);
        $this->mesa_model->set_estado($mesa_id, 'ocupada');
        return $this->_json(['ok' => true, 'pedido_id' => (int) $pid]);
    }

    public function api_agregar_linea()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        if (!$this->db->table_exists('pedido_detalle')) {
            return $this->_json(['ok' => false, 'msg' => 'Tablas POS no instaladas.']);
        }
        $pedido_id = (int) $this->input->post('pedido_id');
        $producto_id = (int) $this->input->post('producto_id');
        $cantidad = (float) $this->input->post('cantidad');
        if ($cantidad <= 0) {
            $cantidad = 1;
        }
        $pedido = $this->pedido_model->get_by_id($pedido_id);
        if (!$pedido || $pedido->estado !== 'abierto') {
            return $this->_json(['ok' => false, 'msg' => 'Pedido no válido.']);
        }
        $p = $this->producto_model->get_precio_venta($producto_id);
        if (!$p) {
            return $this->_json(['ok' => false, 'msg' => 'Producto no disponible.']);
        }
        if ($p->precio_venta === null || (float) $p->precio_venta <= 0) {
            return $this->_json(['ok' => false, 'msg' => 'El producto no tiene precio de venta.']);
        }
        $this->pedido_model->agregar_o_sumar_linea($pedido_id, $producto_id, $cantidad, (float) $p->precio_venta);
        return $this->_json(['ok' => true, 'payload' => $this->_pedido_payload($pedido_id)]);
    }

    public function api_actualizar_linea()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $detalle_id = (int) $this->input->post('detalle_id');
        $cantidad = (float) $this->input->post('cantidad');
        $line = $this->db->get_where('pedido_detalle', ['id' => $detalle_id])->row();
        if (!$line) {
            return $this->_json(['ok' => false, 'msg' => 'Línea no encontrada.']);
        }
        $pedido = $this->pedido_model->get_by_id((int) $line->pedido_id);
        if (!$pedido || $pedido->estado !== 'abierto') {
            return $this->_json(['ok' => false, 'msg' => 'Pedido cerrado.']);
        }
        $this->pedido_model->actualizar_cantidad_linea($detalle_id, $cantidad);
        return $this->_json(['ok' => true, 'payload' => $this->_pedido_payload((int) $line->pedido_id)]);
    }

    public function api_eliminar_linea()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $detalle_id = (int) $this->input->post('detalle_id');
        $line = $this->db->get_where('pedido_detalle', ['id' => $detalle_id])->row();
        if (!$line) {
            return $this->_json(['ok' => false, 'msg' => 'Línea no encontrada.']);
        }
        $pedido_id = (int) $line->pedido_id;
        $pedido = $this->pedido_model->get_by_id($pedido_id);
        if (!$pedido || $pedido->estado !== 'abierto') {
            return $this->_json(['ok' => false, 'msg' => 'Pedido cerrado.']);
        }
        $this->pedido_model->eliminar_linea($detalle_id);
        return $this->_json(['ok' => true, 'payload' => $this->_pedido_payload($pedido_id)]);
    }

    public function api_servicio()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $pedido_id = (int) $this->input->post('pedido_id');
        $pct = (float) $this->input->post('servicio_pct');
        $pedido = $this->pedido_model->get_by_id($pedido_id);
        if (!$pedido || $pedido->estado !== 'abierto') {
            return $this->_json(['ok' => false, 'msg' => 'Pedido no válido.']);
        }
        $this->pedido_model->actualizar_servicio_pct($pedido_id, $pct);
        return $this->_json(['ok' => true, 'payload' => $this->_pedido_payload($pedido_id)]);
    }

    public function api_cerrar()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $pedido_id = (int) $this->input->post('pedido_id');
        $res = $this->pedido_model->cerrar_y_descontar_stock($pedido_id);
        return $this->_json(['ok' => $res['ok'], 'msg' => $res['msg']]);
    }

    public function api_anular()
    {
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $pedido_id = (int) $this->input->post('pedido_id');
        if ($this->pedido_model->anular($pedido_id)) {
            return $this->_json(['ok' => true, 'msg' => 'Pedido anulado y mesa liberada.']);
        }
        return $this->_json(['ok' => false, 'msg' => 'Solo se puede anular una cuenta sin ítems.']);
    }

    private function _pedido_payload($pedido_id)
    {
        $pedido = $this->pedido_model->get_by_id($pedido_id);
        $lineas = $this->pedido_model->get_detalles($pedido_id);
        $out_lines = [];
        foreach ($lineas as $ln) {
            $out_lines[] = [
                'id'               => (int) $ln->id,
                'producto_nombre'  => $ln->producto_nombre,
                'cantidad'         => (float) $ln->cantidad,
                'precio_unitario'  => (float) $ln->precio_unitario,
                'subtotal_linea'   => (float) $ln->subtotal_linea,
            ];
        }
        return [
            'pedido' => $pedido ? [
                'id'             => (int) $pedido->id,
                'subtotal'       => (float) $pedido->subtotal,
                'servicio_pct'   => (float) $pedido->servicio_pct,
                'servicio_monto' => (float) $pedido->servicio_monto,
                'total'          => (float) $pedido->total,
                'estado'         => $pedido->estado,
            ] : null,
            'lineas' => $out_lines,
        ];
    }

    private function _json($data)
    {
        $this->output
            ->set_content_type('application/json; charset=utf-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
