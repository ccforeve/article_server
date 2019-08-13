<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class Distribution extends BatchAction
{
    protected $admin_id;

    public function __construct($admin_id)
    {
        $this->admin_id = $admin_id;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {
    $.ajax({
        method: 'post',
        url: '/admin/presale/distribution',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            admin_id: {$this->admin_id}
        },
        success: function (ret) {
        $.pjax.reload('#pjax-container');
            if(ret.code == 0) {
                toastr.success(ret.message);
            } else {
                toastr.error(ret.message);
            }
        }
    });
});

EOT;

    }
}
