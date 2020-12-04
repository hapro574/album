<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2020 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Tue, 10 Nov 2020 06:56:08 GMT
 */
if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['create_album'];

$post = [];
$error = [];
$post['id'] = $nv_Request->get_title('id', 'post,get',0);
$post['albumname'] = $nv_Request->get_title('albumname', 'post', '');
$post['description'] = $nv_Request->get_textarea('description', 'post', '');
$post['submit'] = $nv_Request->get_title('submit', 'post', '');


if ($nv_Request->isset_request("submit", "post")) {
    $post['albumname'] = $nv_Request->get_title('albumname', "post", '');
    $post['description'] = $nv_Request->get_textarea('description', '', NV_ALLOWED_HTML_TAGS, 1);
    if (isset($_FILES, $_FILES['uploadfile'], $_FILES['uploadfile']['tmp_name']) and is_uploaded_file($_FILES['uploadfile']['tmp_name'])) {
        // Khởi tạo Class upload
        $upload = new NukeViet\Files\Upload($admin_info['allow_files_type'], $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT);

        // Thiết lập ngôn ngữ, nếu không có dòng này thì ngôn ngữ trả về toàn tiếng Anh
        $upload->setLanguage($lang_global);

        // Tải file lên server
        $upload_info = $upload->save_file($_FILES['uploadfile'], NV_UPLOADS_REAL_DIR . '/' . $module_name, false, $global_config['nv_auto_resize']);
    } else {
        $error[] = "Chưa chọn ảnh";
    }

    if ($post['albumname'] == '') {
        $error[] = "Chưa nhập tên album";
    } else {
        $albumname = test_input($post["albumname"]);
        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/",$albumname)) {
            $error[] = 'Tên album chỉ bao gồm ký tự và khoảng trắng';
        }
    }

    if (empty($error)) {
        try {
            if ($post['id'] > 0) {
                // update
                $sql = "UPDATE nv4_vi_albuminfo SET albumname=:albumname, image:=image, description:= description,
                    updatetime=:updatetime WHERE id= " . $post['id'];
                $stmt = $db->prepare($sql);
                $stmt->bindValue("updatetime", 0);
            } else {
                // insert
                $sql = "INSERT INTO nv4_vi_albuminfo(albumname, image, description, status, weight, creattime)
                    VALUES (:albumname, :image, :description, :status, :weight, :creattime)";
                $stmt = $db->prepare($sql);
                $stmt->bindValue("status", 1);

                $_sql= "SELECT COUNT(*) FROM nv4_vi_albuminfo";
                $weight = $db->query($_sql)->fetchColumn();
                $stmt->bindValue("weight", ($weight + 1));
                $stmt->bindValue("creattime", NV_CURRENTTIME);
            }
            $stmt->bindParam("albumname", $post['albumname']);
            $stmt->bindParam("image", $upload_info['basename']);
            $stmt->bindParam("description", $post['description']);
            $exe = $stmt->execute();
            if ($exe) {
                if ($post['id'] > 0) {
                    $error[] = 'Cập nhật album thành công';
                } else {
                    $error[] = 'Thêm album thành công';
                }
            } else {
                $error[] = 'Lỗi không thực hiện được';
            }
        } catch (PDOException $e) {
            print_r($e);die;
        }
    }
} else if ($post['id'] > 0) {
    // tồn tại id thì thực hiện lấy dữ liệu của id đó
    $sql = "SELECT * FROM nv4_vi_albuminfo WHERE id = " . $post['id'];
    $post = $db->query($sql)->fetch();
} else {
    $post['albumname'] = '';
    $post['description'] = '';
}


// ------------------------------
// Viết code xử lý chung vào đây
// ------------------------------

$xtpl = new XTemplate('create_album.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('POST', $post);
$xtpl->assign('ERROR', implode('<br>', $error));
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$xtpl->assign('POST', $post);
if (!empty($error)) {
    $xtpl->assign('error', implode("<br/>", $error));
    $xtpl->parse('main.error');
}
// -------------------------------
// Viết code xuất ra site vào đây
// -------------------------------

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
