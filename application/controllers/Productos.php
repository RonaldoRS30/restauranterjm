<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Hub de productos: índice por categorías; cada categoría tendrá su propia lógica en productos/categoria/{id}.
 */
class Productos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id')) {
            redirect('login');
        }
        $this->load->model('categoria_model');
        $this->load->model('producto_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['categorias'] = $this->categoria_model->get_activas();
        $this->load->view('productos/index', $data);
    }

    /**
     * Vista base por categoría (aquí enlazarás lógica distinta según $id o slug en el futuro).
     */
    public function categoria($id = null)
    {
        $id = (int) $id;
        if ($id < 1) {
            redirect('productos');
        }

        $cat = $this->categoria_model->get_by_id($id);
        if (!$cat || !(int) $cat->estado) {
            $this->session->set_flashdata('error', 'Categoría no encontrada o inactiva.');
            redirect('productos');
        }

        $data['categoria']       = $cat;
        $data['productos']       = $this->producto_model->get_by_categoria($id);
        $data['productos_count'] = $this->producto_model->count_by_categoria($id);
        $this->load->view('productos/categoria', $data);
    }

    /**
     * Alta de producto para la categoría indicada (precios guardados; no se listan en esta vista).
     */
    public function agregar($id_categoria = null)
    {
        $id_categoria = (int) $id_categoria;
        if ($id_categoria < 1) {
            redirect('productos');
        }

        $cat = $this->categoria_model->get_by_id($id_categoria);
        if (!$cat || !(int) $cat->estado) {
            $this->session->set_flashdata('error', 'Categoría no encontrada o inactiva.');
            redirect('productos');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[200]');
            $this->form_validation->set_rules('stock', 'Stock', 'required|integer|greater_than_equal_to[0]');
            $this->form_validation->set_rules('precio', 'Precio', 'trim|callback_decimal_opcional');
            $this->form_validation->set_rules('precio_costo', 'Precio costo', 'trim|callback_decimal_opcional');
            $this->form_validation->set_rules('precio_venta', 'Precio venta', 'trim|callback_decimal_opcional');

            if ($this->form_validation->run()) {
                $this->producto_model->insert([
                    'id_categoria'  => $id_categoria,
                    'nombre'        => $this->input->post('nombre', TRUE),
                    'stock'         => (int) $this->input->post('stock'),
                    'precio'        => $this->_decimal_or_null($this->input->post('precio')),
                    'precio_costo'  => $this->_decimal_or_null($this->input->post('precio_costo')),
                    'precio_venta'  => $this->_decimal_or_null($this->input->post('precio_venta')),
                    'estado'        => 1,
                ]);
                $this->session->set_flashdata('success', 'Producto agregado.');
                redirect('productos/categoria/' . $id_categoria);
                return;
            }
        }

        $data['categoria'] = $cat;
        $this->load->view('productos/agregar', $data);
    }

    public function decimal_opcional($str)
    {
        $str = trim((string) $str);
        if ($str === '') {
            return TRUE;
        }
        if (!is_numeric(str_replace(',', '.', $str))) {
            $this->form_validation->set_message('decimal_opcional', 'El campo {field} debe ser un número válido.');
            return FALSE;
        }
        return TRUE;
    }

    private function _decimal_or_null($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return round((float) str_replace(',', '.', $value), 2);
    }
}
