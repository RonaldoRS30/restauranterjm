<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends CI_Controller {

    private $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id')) {
            redirect('login');
        }
        $this->load->model('categoria_model');
        $this->load->library('form_validation');
        $this->load->helper(['url', 'text']);
    }

    public function index()
    {
        $q = trim((string) $this->input->get('q', true));
        $page = (int) $this->input->get('page');
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $this->per_page;
        $total = $this->categoria_model->count_busqueda($q);
        $data['categorias'] = $this->categoria_model->get_paginado($this->per_page, $offset, $q);
        $data['q'] = $q;
        $data['pagination_links'] = $this->_pagination_links(site_url('categorias'), $total, $this->per_page);
        $this->load->view('categorias/index', $data);
    }

    private function _pagination_links($base_url, $total_rows, $per_page)
    {
        if ($total_rows <= $per_page) {
            return '';
        }
        $this->load->library('pagination');
        $config = [
            'base_url'             => $base_url,
            'total_rows'           => $total_rows,
            'per_page'             => $per_page,
            'page_query_string'    => true,
            'query_string_segment' => 'page',
            'use_page_numbers'     => true,
            'reuse_query_string'   => true,
            'num_links'            => 4,
            'full_tag_open'        => '<nav class="flex flex-wrap justify-center items-center gap-2 mt-8" aria-label="Paginación">',
            'full_tag_close'       => '</nav>',
            'first_tag_open'       => '<span class="inline-flex">',
            'first_tag_close'      => '</span>',
            'last_tag_open'        => '<span class="inline-flex">',
            'last_tag_close'       => '</span>',
            'cur_tag_open'         => '<span class="inline-flex items-center px-3 py-2 rounded-xl bg-slate-900 text-white text-sm font-bold">',
            'cur_tag_close'        => '</span>',
            'num_tag_open'         => '<span class="inline-flex">',
            'num_tag_close'        => '</span>',
            'prev_tag_open'        => '<span class="inline-flex">',
            'prev_tag_close'       => '</span>',
            'next_tag_open'        => '<span class="inline-flex">',
            'next_tag_close'       => '</span>',
            'attributes'           => ['class' => 'inline-flex items-center px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition'],
        ];
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }

    public function crear()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('titulo', 'Título', 'required|max_length[150]');
            if ($this->form_validation->run()) {
                $titulo = $this->input->post('titulo', TRUE);
                $slug   = $this->_slug_unico($titulo);

                if (empty($_FILES['logo']['name'])) {
                    $this->session->set_flashdata('error', 'Debes subir un logo para la categoría.');
                    redirect('categorias/crear');
                    return;
                }

                $logo = $this->_subir_logo();
                if ($logo === false || $logo === null) {
                    $this->session->set_flashdata('error', 'No se pudo subir el logo. Usa JPG, PNG, GIF o WebP (máx. 2 MB).');
                    redirect('categorias/crear');
                    return;
                }

                $this->categoria_model->insert([
                    'titulo' => $titulo,
                    'slug'   => $slug,
                    'logo'   => $logo,
                    'estado' => 1,
                ]);
                $this->session->set_flashdata('success', 'Categoría creada.');
                redirect('categorias');
                return;
            }
        }

        $this->load->view('categorias/form', [
            'accion'    => 'crear',
            'categoria' => null,
        ]);
    }

    public function editar($id = null)
    {
        $id = (int) $id;
        $row = $this->categoria_model->get_by_id($id);
        if (!$row) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('titulo', 'Título', 'required|max_length[150]');
            if ($this->form_validation->run()) {
                $titulo = $this->input->post('titulo', TRUE);
                $slug   = $this->_slug_unico($titulo, $id);
                $update = [
                    'titulo' => $titulo,
                    'slug'   => $slug,
                ];

                if (!empty($_FILES['logo']['name'])) {
                    $nuevo = $this->_subir_logo();
                    if ($nuevo === false) {
                        $this->session->set_flashdata('error', 'No se pudo subir el logo.');
                        redirect('categorias/editar/' . $id);
                        return;
                    }
                    if (!empty($row->logo) && is_file(FCPATH . $row->logo)) {
                        @unlink(FCPATH . $row->logo);
                    }
                    $update['logo'] = $nuevo;
                }

                $this->categoria_model->update($id, $update);
                $this->session->set_flashdata('success', 'Categoría actualizada.');
                redirect('categorias');
                return;
            }
        }

        $this->load->view('categorias/form', [
            'accion'    => 'editar',
            'categoria' => $row,
        ]);
    }

    public function eliminar($id = null)
    {
        $id = (int) $id;
        $row = $this->categoria_model->get_by_id($id);
        if (!$row) {
            show_404();
        }
        $this->categoria_model->update($id, ['estado' => 0]);
        $this->session->set_flashdata('success', 'Categoría desactivada.');
        redirect('categorias');
    }

    public function activar($id = null)
    {
        $id = (int) $id;
        $row = $this->categoria_model->get_by_id($id);
        if (!$row) {
            show_404();
        }
        $this->categoria_model->update($id, ['estado' => 1]);
        $this->session->set_flashdata('success', 'Categoría activada.');
        redirect('categorias');
    }

    private function _slug_unico($titulo, $exclude_id = null)
    {
        $base = url_title(convert_accented_characters($titulo), '-', TRUE);
        if ($base === '') {
            $base = 'categoria';
        }
        $slug = $base;
        $i = 1;
        while ($this->categoria_model->slug_exists($slug, $exclude_id)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /**
     * @return string|null Ruta relativa tipo uploads/categorias/archivo.png, o null si no hubo archivo
     * @return false en error de subida cuando sí había archivo
     */
    private function _subir_logo()
    {
        if (empty($_FILES['logo']['name'])) {
            return null;
        }

        $path = FCPATH . 'uploads/categorias/';
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        $config = [
            'upload_path'   => $path,
            'allowed_types' => 'gif|jpg|jpeg|png|webp',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE,
        ];
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('logo')) {
            return false;
        }

        $data = $this->upload->data();
        return 'uploads/categorias/' . $data['file_name'];
    }
}
