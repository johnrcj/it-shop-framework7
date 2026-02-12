<?php
class MUser extends MY_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function check_user_status($usr_uid) {
        $info = $this->db->get_where("t_user", ['uid' => $usr_uid])->row();
        if ($info == null) {
            return RES_ERROR_USR_EXIT;
        } else {
            if ($info->status == 0) {
                return RES_ERROR_USR_BLOCK;
            } else if ($info->status < 0) {
                return RES_ERROR_USR_EXIT;
            }
        }

        return RES_SUCCESS;
    }
}