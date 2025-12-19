<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Nếu chưa đăng nhập
        if (!$session->get('logged_in')) {
            
            // Nếu là gọi API (JSON) -> Trả về JSON lỗi
            if ($request->isAJAX() || strpos($request->getUri()->getPath(), 'backend/') !== false) {
                return service('response')
                    ->setJSON([
                        'success' => false, 
                        'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
                        'code' => 401
                    ])
                    ->setStatusCode(401);
            }
            
            // Nếu truy cập trực tiếp vào View -> Chuyển hướng về login
            return redirect()->to(base_url('login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Không làm gì sau khi xử lý xong
    }
}