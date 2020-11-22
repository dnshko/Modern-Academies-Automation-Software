<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class approve_leave extends Admin_Controller {

    function __construct() {
        parent::__construct();
    }

    function unauthorized() {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    function index() {

        if (!$this->rbac->hasPrivilege('approve_leave', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'Attendance/approve_leave');
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['class_id'] = $class_id = '';
        $data['section_id'] = $section_id = '';
        $data['results'] = array();
        $listaudit = $this->apply_leave_model->get(null, null, null);
        if (isset($_POST['class_id']) && $_POST['class_id'] != '') {
            $data['class_id'] = $class_id = $_POST['class_id'];
        }

        if (isset($_POST['section_id']) && $_POST['section_id'] != '') {
            $data['section_id'] = $section_id = $_POST['section_id'];
        }
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
        } else {
            $listaudit = $this->apply_leave_model->get(null, $class_id, $section_id);
        }

        $data['results'] = $listaudit;

        $this->load->view('layout/header');
        $this->load->view('admin/approve_leave/index', $data);
        $this->load->view('layout/footer');
    }

    public function get_details() {
        $userdata = $this->customlib->getUserData();
        $role_id = $userdata["role_id"];
        $can_edit = 1;

        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            $myclasssubjects = $this->apply_leave_model->canApproveLeave($userdata["id"], $this->input->post('class_id'), $this->input->post('section_id'));
            $can_edit = $myclasssubjects;
        }

        if ($can_edit == 0) {

            $data = array('status' => 0, 'error' => $this->lang->line('not_authoried'));
        } else {
            $data = $this->apply_leave_model->get($_POST['id'], null, null);
            $data['status'] = 1;
            $data['from_date'] = date($this->customlib->getSchoolDateFormat(), strtotime($data['from_date']));
            $data['to_date'] = date($this->customlib->getSchoolDateFormat(), strtotime($data['to_date']));
            $data['apply_date'] = date($this->customlib->getSchoolDateFormat(), strtotime($data['apply_date']));
        }
        echo json_encode($data);
    }

    public function add() {

        $student_id = '';
        $this->form_validation->set_rules('apply_date', $this->lang->line('apply') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('from_date', $this->lang->line('from') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('to_date', $this->lang->line('to') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('student', $this->lang->line('student'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {

            $msg = array(
                'apply_date' => form_error('apply_date'),
                'from_date' => form_error('from_date'),
                'to_date' => form_error('to_date'),
                'student' => form_error('student'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $student_session_id = $this->apply_leave_model->get_studentsessionId($_POST['class'], $_POST['section'], $_POST['student']);

            $data = array(
                'apply_date' => date('Y-m-d', strtotime($this->input->post('apply_date'))),
                'from_date' => date('Y-m-d', strtotime($this->input->post('from_date'))),
                'to_date' => date('Y-m-d', strtotime($this->input->post('to_date'))),
                'student_session_id' => $student_session_id['id'],
                'reason' => $this->input->post('message'),
                'request_type' => '1'
            );

            if ($this->input->post('leave_id') == '') {

                $leave_id = $this->apply_leave_model->add($data);
                $data['id'] = $leave_id;
            } else {
                $data['id'] = $this->input->post('leave_id');
                $this->apply_leave_model->add($data);
            }

            if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                $fileInfo = pathinfo($_FILES["userfile"]["name"]);
                $img_name = $data['id'] . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["userfile"]["tmp_name"], "./uploads/student_leavedocuments/" . $img_name);
                $data = array('id' => $data['id'], 'docs' => $img_name);
                $this->apply_leave_model->add($data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function searchByClassSection($class_id, $student_id) {

        $section_id = $_REQUEST['section_id'];
        $resultlist = $this->student_model->searchByClassSection($class_id, $section_id);

        $data['resultlist'] = $resultlist;
        $data['select_id'] = $student_id;

        $this->load->view('admin/approve_leave/_student_list', $data);
    }

    public function status() {
        $userdata = $this->customlib->getUserData();
        $role_id = $userdata["role_id"];
        $can_edit = 1;

        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            $myclasssubjects = $this->apply_leave_model->canApproveLeave($userdata["id"], $this->input->post('class_id'), $this->input->post('section_id'));
            $can_edit = $myclasssubjects;
        }

        if ($can_edit == 0) {
            $msg = array('leave' => $this->lang->line('not_authoried'));
            $array = array('status' => 0, 'error' => $this->lang->line('not_authoried'));
        } else {
            if ($_POST['status'] == 1) {
                $data['approve_by'] = $this->customlib->getStaffID();
            } else {
                $data['approve_by'] = 0;
            }

            $data['status'] = $_POST['status'];
            $this->db->where('id', $_POST['id']);
            $this->db->update('student_applyleave', $data);
            $msg = array('leave' => $this->lang->line('success_message'));
            $array = array('status' => 1, 'success' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function remove_leave() {
        $userdata = $this->customlib->getUserData();
        $role_id = $userdata["role_id"];
        $can_edit = 1;

        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            $myclasssubjects = $this->apply_leave_model->canApproveLeave($userdata["id"], $this->input->post('class_id'), $this->input->post('section_id'));
            $can_edit = $myclasssubjects;
        }

        if ($can_edit == 0) {

            $array = array('status' => 0, 'error' => $this->lang->line('not_authoried'));
        } else {
            $this->apply_leave_model->remove_leave($_POST['id']);
            $array = array('status' => 1, 'success' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function download($doc) {

        $this->load->helper('download');
        $filepath = "./uploads/student_leavedocuments/" . $doc;
        $data = file_get_contents($filepath);
        $name = $doc;

        force_download($name, $data);
    }

}
