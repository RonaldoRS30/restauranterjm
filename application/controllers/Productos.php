<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Hub de productos + CRUD por categoría (paginación y búsqueda).
 */
class Productos extends CI_Controller {

    private $per_page_productos = 12;
    private $per_page_cats_hub  = 12;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id')) {
            redirect('login');
        }
        $this->load->model('categoria_model');
        $this->load->model('producto_model');
        $this->load->library('form_validation');
        $this->load->helper('form');
    }

    public function index()
    {
        $q = trim((string) $this->input->get('q', true));
        $page = (int) $this->input->get('page');
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $this->per_page_cats_hub;
        $total = $this->categoria_model->count_activas_busqueda($q);
        $data['categorias'] = $this->categoria_model->get_activas_paginado($this->per_page_cats_hub, $offset, $q);
        $data['q'] = $q;
        $data['pagination_links'] = $this->_pagination_links(
            site_url('productos'),
            $total,
            $this->per_page_cats_hub
        );
        $this->load->view('productos/index', $data);
    }

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

        $q = trim((string) $this->input->get('q', true));
        $page = (int) $this->input->get('page');
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $this->per_page_productos;
        $total = $this->producto_model->count_by_categoria_busqueda($id, $q);
        $productos = $this->producto_model->get_by_categoria_paginado($id, $this->per_page_productos, $offset, $q);

        $base = site_url('productos/categoria/' . $id);
        $data['categoria'] = $cat;
        $data['productos'] = $productos;
        $data['productos_count'] = $this->producto_model->count_by_categoria($id);
        $data['q'] = $q;
        $data['pagination_links'] = $this->_pagination_links($base, $total, $this->per_page_productos);
        $this->load->view('productos/categoria', $data);
    }

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
            $this->form_validation->set_rules('precio_costo', 'Precio costo', 'trim|callback_decimal_opcional');
            $this->form_validation->set_rules('precio_venta', 'Precio venta', 'trim|callback_decimal_opcional');

            if ($this->form_validation->run()) {
                $this->producto_model->insert([
                    'id_categoria' => $id_categoria,
                    'nombre'       => $this->input->post('nombre', true),
                    'stock'        => (int) $this->input->post('stock'),
                    'precio_costo' => $this->_decimal_or_null($this->input->post('precio_costo')),
                    'precio_venta' => $this->_decimal_or_null($this->input->post('precio_venta')),
                    'estado'       => 1,
                ]);
                $this->session->set_flashdata('success', 'Producto agregado.');
                redirect('productos/categoria/' . $id_categoria);
                return;
            }
        }

        $data['categoria'] = $cat;
        $this->load->view('productos/agregar', $data);
    }

    public function editar($id = null)
    {
        $id = (int) $id;
        $row = $this->producto_model->get_by_id_con_categoria($id);
        if (!$row) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[200]');
            $this->form_validation->set_rules('stock', 'Stock', 'required|integer|greater_than_equal_to[0]');
            $this->form_validation->set_rules('precio_costo', 'Precio costo', 'trim|callback_decimal_opcional');
            $this->form_validation->set_rules('precio_venta', 'Precio venta', 'trim|callback_decimal_opcional');

            if ($this->form_validation->run()) {
                $this->producto_model->update($id, [
                    'nombre'       => $this->input->post('nombre', true),
                    'stock'        => (int) $this->input->post('stock'),
                    'precio_costo' => $this->_decimal_or_null($this->input->post('precio_costo')),
                    'precio_venta' => $this->_decimal_or_null($this->input->post('precio_venta')),
                ]);
                $this->session->set_flashdata('success', 'Producto actualizado.');
                redirect('productos/categoria/' . (int) $row->id_categoria);
                return;
            }
        }

        $data['producto'] = $row;
        $this->load->view('productos/editar', $data);
    }

    public function eliminar($id = null)
    {
        $id = (int) $id;
        $row = $this->producto_model->get_by_id($id);
        if (!$row) {
            show_404();
        }
        $cid = (int) $row->id_categoria;
        $this->producto_model->desactivar($id);
        $this->session->set_flashdata('success', 'Producto desactivado (ya no aparece en listados ni en POS).');
        redirect('productos/categoria/' . $cid);
    }

    public function decimal_opcional($str)
    {
        $str = trim((string) $str);
        if ($str === '') {
            return true;
        }
        if (!is_numeric(str_replace(',', '.', $str))) {
            $this->form_validation->set_message('decimal_opcional', 'El campo {field} debe ser un número válido.');
            return false;
        }
        return true;
    }

    private function _decimal_or_null($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return round((float) str_replace(',', '.', $value), 2);
    }

    private function _pagination_links($base_url, $total_rows, $per_page)
    {
        if ($total_rows <= $per_page) {
            return '';
        }
        $this->load->library('pagination');
        $config = [
            'base_url'            => $base_url,
            'total_rows'          => $total_rows,
            'per_page'            => $per_page,
            'page_query_string'   => true,
            'query_string_segment'=> 'page',
            'use_page_numbers'    => true,
            'reuse_query_string'  => true,
            'num_links'           => 4,
            'full_tag_open'       => '<nav class="flex flex-wrap justify-center items-center gap-2 mt-8" aria-label="Paginación">',
            'full_tag_close'      => '</nav>',
            'first_tag_open'      => '<span class="inline-flex">',
            'first_tag_close'     => '</span>',
            'last_tag_open'       => '<span class="inline-flex">',
            'last_tag_close'      => '</span>',
            'cur_tag_open'        => '<span class="inline-flex items-center px-3 py-2 rounded-xl bg-slate-900 text-white text-sm font-bold">',
            'cur_tag_close'       => '</span>',
            'num_tag_open'        => '<span class="inline-flex">',
            'num_tag_close'       => '</span>',
            'prev_tag_open'       => '<span class="inline-flex">',
            'prev_tag_close'      => '</span>',
            'next_tag_open'       => '<span class="inline-flex">',
            'next_tag_close'      => '</span>',
            'attributes'          => ['class' => 'inline-flex items-center px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition'],
        ];
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }
}
