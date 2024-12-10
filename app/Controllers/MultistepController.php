<?php
namespace App\Controllers;
use App\Models\UserModel; 

class MultistepController extends BaseController {
    public function index($id = null) {
        $data = [];

        if ($id) {
            // Fetch user data if ID is provided
            // $userModel = new UserModel();
            // $data['user'] = $userModel->find($id);  
            $data['id']=$id;
        }
else{
    $data['id']=null; 
}
        return view('multistep', $data);  
    }

    public function insertData()
    {
        $response = ['status' => false, 'message' => 'Form submission failed!'];

        
        $userModel = new UserModel();

        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'    => 'required',
            'email'   => 'required|valid_email[users.email]',
            'gender'  => 'required',
            'city'    => 'required',
            'bio'     => 'required',
        ]);

        
        if (!$validation->withRequest($this->request)->run()) {
            $response['errors'] = $validation->getErrors();
        } else {
            $data = $this->request->getPost();

        
            if (isset($data['hobbies']) && is_array($data['hobbies'])) {
                $data['hobbies'] = implode(',', $data['hobbies']);
            }


            if ($file = $this->request->getFile('resume')) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads', $newName);
                    $data['resume'] = $newName;
                }
            }

        
            if ($userModel->insert($data)) {
                $response = [
                    'status' => true,
                    'message' => 'Form submitted successfully!',
                ];
            }
        }

        return $this->response->setJSON($response);
    }

    public function viewData()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->findAll(); 
        return view('data_view', $data);
    }
    // public function fetchData()
    // {
    //     $userModel = new UserModel();
       

    //     $limit = $this->request->getVar('limit') ?? 5;  
    // $offset = $this->request->getVar('offset') ?? 0;

    // $data = $userModel->get_users($limit, $offset);
    // $total_rows = $userModel->get_total_users();
    

      
    //     echo json_encode([
    //         'status' => true,
    //         'data' => $data,
    //         'total_rows' => $total_rows,
    //         'limit' => $limit,
    //     ]);
    // }
    public function fetchData() {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? '';
        $gender = $this->request->getGet('gender') ?? '';
    
        $userModel = new \App\Models\UserModel();
        $result = $userModel->get_users($limit, $offset, $search,$gender);
    
        return $this->response->setJSON([
            'status' => true,
            'data' => $result['data'],
            'total_rows' => $result['total_rows'],
        ]);
    }
    

    public function getFormData($id)
    {
        $userModel = new UserModel();
        $data = $userModel->find($id);

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Data not found']);
        }
    }
    public function updateData($id) {
        $response = ['status' => false, 'message' => 'Update failed!'];

        $userModel = new UserModel();
        $data = $this->request->getPost();

        if (isset($data['hobbies']) && is_array($data['hobbies'])) {
            $data['hobbies'] = implode(',', $data['hobbies']);
        }

        
        if ($file = $this->request->getFile('resume')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);
                $data['resume'] = $newName;
            }
        }


        if ($userModel->update($id, $data)) {
            $response = [
                'status' => true,
                'message' => 'Data updated successfully!',
            ];
        }

        return $this->response->setJSON($response);
    }
    public function delete($id)
    {
        $userModel = new UserModel();

        
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'User not found.'
            ]);
        }

    
        if ($userModel->delete($id)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'User deleted successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to delete user.'
            ]);
        }
    }
    
}

?>