<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
  
class RestaurantController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        return view('restaurant');
    }
}