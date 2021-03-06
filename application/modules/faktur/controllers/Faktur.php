<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Faktur extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

        $this->data['role_label_id'] = '';
        $this->data['search']        = '';
        $this->data['limit']         = $this->_limit;
        $this->data['offset']        = $this->_offset;

        $_module_id               = $this->library_module->_module_id(strtolower(controller));
        $this->data['breadcrumb'] = $this->library_module->_module_breadcrumb($_module_id);

        $this->data['today']       = date('Y-m-d');
        $this->data['_is_allowed'] = $this->_is_allowed(array(role_admin)) ? '' : 'hidden destroy';
        $this->load->model('model_faktur');
        $this->data['status'] = $this->input->get('status');

    }
    /*****************************************************************************/
    public function index()
    {

        $this->_set_referer();
        if (!$this->_is_allowed(array(role_admin))) {
            redirect(base . '/login');
        }
        $param_faktur['table'] = 'faktur';
        $total_rows            = $this->model_generic->_count($param_faktur);
        $_cek_faktur           = $this->model_generic->_cek($param_faktur);
        $config_base_url       = base . '/' . controller;
        $this->data['search']  = '';
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
            $this->data['search'] = $this->input->get('search');
            $this->db->or_like('faktur.pemilik_nama', $this->input->get('search'));
            $this->db->or_like('faktur.faktur_merk', $this->input->get('search'));
            $this->db->or_like('faktur.faktur_jenis', $this->input->get('search'));
            $this->db->or_like('faktur.faktur_type', $this->input->get('search'));
        }

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('faktur_disabled', 0);
        }
        $faktur_all = $this->model_faktur->_get_faktur($this->_limit, $this->_offset);
        // opn($faktur_all);exit();
        $this->data['faktur_all'] = $faktur_all;

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;
        $this->pagination->initialize($config);
        $this->data['paging']     = $this->pagination->create_links();
        $this->data['total_rows'] = $total_rows;

        $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

        if ($this->input->get('view')) {
            $view = $this->input->get('view');
            switch ($view) {
                case 'grid':
                    $this->data['body'] = $this->parser->parse('_container_view_grid.html', $this->data, true);
                    break;
                case 'list':
                    $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
                    break;
                default:
                    $this->data['body'] = $this->parser->parse('_container_view_grid.html', $this->data, true);
                    break;
            }
        } else {
            $this->data['body'] = $this->parser->parse('_container_view_grid.html', $this->data, true);
        }

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function admin()
    {
        $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            $param_faktur['table'] = 'faktur';
            $total_rows            = $this->model_generic->_count($param_faktur);
            $_cek_faktur           = $this->model_generic->_cek($param_faktur);
            $config_base_url       = base . '/' . controller;
            $this->data['search']  = '';
            if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
                $config_base_url      = $config_base_url . '?search=' . $this->input->get('search');
                $this->data['search'] = $this->input->get('search');
                $this->db->or_like('faktur.pemilik_nama', $this->input->get('search'));
                $this->db->or_like('faktur.kendaraan_merk', $this->input->get('search'));
                $this->db->or_like('faktur.kendaraan_jenis', $this->input->get('search'));
                $this->db->or_like('faktur.kendaraan_type', $this->input->get('search'));
                $total_rows = $this->model_generic->_count($param_faktur);

                $this->db->or_like('faktur.pemilik_nama', $this->input->get('search'));
                $this->db->or_like('faktur.kendaraan_merk', $this->input->get('search'));
                $this->db->or_like('faktur.kendaraan_jenis', $this->input->get('search'));
                $this->db->or_like('faktur.kendaraan_type', $this->input->get('search'));

            }
            $faktur_all = $this->model_generic->_get($param_faktur, $this->_limit, $this->_offset);
            // opn(lq());
            // opn($faktur_all);exit();
            $this->data['faktur_all'] = $faktur_all;
            $this->data['total_rows'] = $total_rows;

            $this->load->library('pagination');
            $config['base_url']             = $config_base_url;
            $config['total_rows']           = $total_rows;
            $config['query_string_segment'] = 'offset';
            $config['per_page']             = $this->_limit;
            $this->pagination->initialize($config);
            $this->data['paging']    = $this->pagination->create_links();
            $this->data['searchbar'] = $this->parser->parse('_searchbar.html', $this->data, true);

            $this->data['body'] = $this->parser->parse('_container_view_list.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/
    public function add()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            if ($this->input->post()) {
                $param_faktur            = $this->input->post();
                $param_faktur['table']   = 'faktur';
                $param_faktur['user_id'] = $this->_user_id;
                $this->model_generic->_insert($param_faktur);
                $this->_goto_referer();
                // opn($param);exit();
            } else {

                $this->data['body'] = $this->parser->parse('_add.html', $this->data, true);
                $this->parser->parse('dashboard/_index.html', $this->data, false);
            }
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    public function edit()
    {
        // $this->_set_referer();
        if ($this->_is_allowed(array(role_admin))) {
            $arg = func_get_args();
            if (isset($arg[0])) {
                $faktur_id = $arg[0];
                if ($this->input->post()) {
                    $param_faktur            = $this->input->post();
                    $param_faktur['table']   = 'faktur';
                    $param_faktur['user_id'] = $this->_user_id;
                    $this->db->where('faktur_id', $faktur_id);
                    $_cek_faktur = $this->model_generic->_cek($param_faktur);
                    if ($_cek_faktur) {
                        $this->db->where('faktur_id', $faktur_id);
                        $this->model_generic->_update($param_faktur);
                        $this->_goto_referer();
                    }
                    // opn($param);exit();
                } else {

                    $param_faktur['table'] = 'faktur';
                    $this->db->where('faktur_id', $faktur_id);
                    $faktur_detail = $this->model_generic->_get($param_faktur);
                    foreach ($faktur_detail as $key => $value) {
                        $value->faktur_tanggal = date('Y-m-d', strtotime($value->faktur_tanggal));
                    }
                    $this->data['faktur_detail'] = $faktur_detail;

                    $this->data['body'] = $this->parser->parse('_edit.html', $this->data, true);
                    $this->parser->parse('dashboard/_index.html', $this->data, false);
                }

            }
        } else {
            redirect(base . '/login');
        }
    }
    /*****************************************************************************/
    public function detail()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $faktur_id             = $arg[0];
            $param_faktur['table'] = 'faktur';
            $this->db->where('faktur_id', $faktur_id);
            $faktur_detail = $this->model_generic->_get($param_faktur);
            foreach ($faktur_detail as $key => $value) {
                $value->faktur_tanggal = date('d M Y', strtotime($value->faktur_tanggal));
            }
            $this->data['faktur_detail'] = $faktur_detail;

            $this->data['body'] = $this->parser->parse('_detail.html', $this->data, true);
            $this->parser->parse('dashboard/_index.html', $this->data, false);
        }
    }
    /*****************************************************************************/
    public function delete()
    {
        $arg = func_get_args();
        if (isset($arg[0])) {
            $faktur_id = $arg[0];
            if ($this->_is_allowed(array(role_admin))) {
                $param_faktur['table'] = 'faktur';
                $this->db->where('faktur_id', $faktur_id);
                $this->model_generic->_del($param_faktur);

                redirect(base . '/' . controller . '/admin');
            }
        }
    }
    /*****************************************************************************/
    public function faktur_ajax()
    {
        // if ($this->input->is_ajax_request()) {
        $param_faktur['table'] = 'faktur';
        $total_rows            = $this->model_generic->_count($param_faktur);
        $config_base_url       = base . '/' . controller . '/admin';

        if (!$this->_is_allowed(array(role_admin))) {
            $this->db->where('faktur_disabled', '0');
        }
        if ($this->input->get('search') && strlen($this->input->get('search')) >= 2) {
            $config_base_url     = $config_base_url . '?search=' . $this->input->get('search');
            $result[0]['search'] = $this->input->get('search');
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('faktur_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('faktur_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('faktur.faktur_nomor', $this->input->get('search'));
            $this->db->or_like('faktur.pemilik_nama', $this->input->get('search'));
            $this->db->or_like('faktur.kendaraan_merk', $this->input->get('search'));
            $this->db->or_like('faktur.kendaraan_jenis', $this->input->get('search'));
            $this->db->or_like('faktur.kendaraan_type', $this->input->get('search'));
            $this->db->group_end();

            $total_rows = $this->model_generic->_count($param_faktur);
            if (!$this->_is_allowed(array(role_admin))) {
                $this->db->where('faktur_disabled', '0');
            }
            if ($this->input->get('status')) {
                $this->db->group_start();
                $this->db->where('faktur_status', $this->input->get('status'));
                $this->db->group_end();
            }
            $this->db->group_start();
            $this->db->or_like('faktur.faktur_nomor', $this->input->get('search'));
            $this->db->or_like('faktur.pemilik_nama', $this->input->get('search'));
            $this->db->or_like('faktur.kendaraan_merk', $this->input->get('search'));
            $this->db->or_like('faktur.kendaraan_jenis', $this->input->get('search'));
            $this->db->or_like('faktur.kendaraan_type', $this->input->get('search'));
            $this->db->group_end();

        }

        if (is_numeric($this->input->get('status'))) {
            $this->db->where('faktur_status', $this->input->get('status'));
            $total_rows = $this->model_generic->_count($param_faktur);
            $this->db->where('faktur_status', $this->input->get('status'));
        }
        if (is_numeric($this->input->get('id'))) {
            $this->db->where('faktur_id', $this->input->get('id'));
            $total_rows = $this->model_generic->_count($param_faktur);
            $this->db->where('faktur_id', $this->input->get('id'));
        }
        $faktur_all = $this->model_faktur->_get_faktur($this->_limit, $this->_offset);
        foreach ($faktur_all as $key => $value) {
            $value->faktur_tanggal = date('d M Y', strtotime($value->faktur_tanggal));
            $value->toggle_btn     = ($value->faktur_disabled) ? 'default' : 'success';
            $value->toggle_icon    = ($value->faktur_disabled) ? 'toggle-off' : 'toggle-on';
            $value->id = $value->faktur_id;
            $value->text = $value->faktur_nomor;
        }
        $this->data['faktur_all'] = $faktur_all;
        // opn($total_rows);exit();
        // opn(lq());
        // opn($faktur_all);
        // exit();

        $this->load->library('pagination');
        $config['base_url']             = $config_base_url;
        $config['total_rows']           = $total_rows;
        $config['query_string_segment'] = 'offset';
        $config['per_page']             = $this->_limit;
        $this->pagination->initialize($config);

        $result[0]['paging']     = $this->pagination->create_links();
        $result[0]['_tbody']     = $this->parser->parse('_data_view_list.html', $this->data, true);
        $result[0]['_gridview']  = $this->parser->parse('_data_view_grid.html', $this->data, true);
        // $result[0]['faktur_all'] = $faktur_all;
        $result[0]['total_rows'] = $total_rows;

        header('Content-Type: application/json');
        echo json_encode($result);

        // }
    }
    /*****************************************************************************/
    public function faktur_toggle()
    {
        if ($this->input->post()) {
            $param          = $this->input->post();
            $param['table'] = 'faktur';
            $this->db->where('faktur_id', $param['faktur_id']);
            $this->db->where('faktur_disabled != ' . $param['faktur_disabled']);
            $_cek_module = $this->model_generic->_cek($param);
            if ($_cek_module) {
                $this->db->where('faktur_id', $param['faktur_id']);
                $this->model_generic->_update($param);

                $return['toggle_btn']  = ($param['faktur_disabled'] == 1) ? 'default' : 'success';
                $return['toggle_icon'] = ($param['faktur_disabled'] == 1) ? 'toggle-off' : 'toggle-on';

                echo json_encode($return);

            } else {

                $return['toggle_btn']  = ($param['faktur_disabled'] == 0) ? 'success' : 'default';
                $return['toggle_icon'] = ($param['faktur_disabled'] == 0) ? 'toggle-on' : 'toggle-off';

                echo json_encode($return);
            }
            // opn($_cek_module);exit();

        }
    }
    /*****************************************************************************/

    /*****************************************************************************/
}

/* End of file Faktur.php */
/* Location: ./application/controllers/Faktur.php */
