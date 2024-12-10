<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'gender', 'hobbies', 'resume', 'city', 'bio', 'created_at'];

    public function get_users($limit, $offset, $search = '', $gender = '')
{
    $builder = $this->db->table('users');
    
    // Apply search filter
    if (!empty($search)) {
        $builder->like('name', $search);
        $builder->orLike('email', $search);
    }

    // Apply gender filter
    if (!empty($gender)) {
        $builder->where('gender', $gender);
    }

    // Fetch paginated data
    $data = $builder->limit($limit, $offset)->get()->getResultArray();

    // Count total rows for the filtered query
    $builder->resetQuery();
    if (!empty($search)) {
        $builder->like('name', $search);
        $builder->orLike('email', $search);
    }
    if (!empty($gender)) {
        $builder->where('gender', $gender);
    }
    $totalRows = $builder->countAllResults();

    return ['data' => $data, 'total_rows' => $totalRows];
}

    
}

?>