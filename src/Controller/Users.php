<?php
namespace AdminModule\Controller;

use AdminModule\Controller\Shared as SharedController;
use PPI\Framework\Http\Request as Request;

class Users extends SharedController
{
    public function listAction(Request $request)
    {
        // Get user entities
        $users = $this->getService('admin.users.storage')->getAll();

        return $this->render('AdminModule:users:list.html.php', compact('users'));
    }

    public function editAction(Request $request)
    {
        $user_id = $request->get('user_id');

        if ($user_id == 0) {
            $user = $this->getService('admin.users.storage')->getBlankEntity();
        } else {
            $user = $this->getService('admin.users.storage')->getById($user_id);
        }

        return $this->render('AdminModule:users:edit.html.php', compact('user'));
    }

    public function saveAction(Request $request)
    {
        /**
         * @todo validate data
         */

        $user = $request->get();

        // Validate input
        if ($user) {

        }
        // Save User (Create or Update)

        if ($errors == '') {

        }
    }
}
