<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CategoryController extends Controller
{
    protected string $upload;
    protected array $sizes = [50, 150, 300, 600, 1200];

    public function __construct()
    {
        $this->upload = env('UPLOAD_DIR', 'uploads/categories/');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function index()
    {
        $data = Category::all();
        return response()->json($data)
            ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function store(Request $request)
    {
        $dir = public_path($this->upload);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($request->hasFile('image') && $request->input("name") != "") {
            $file = $request->file("image");
            $fileName = $this->saveImage($file);
            $item = Category::create(['name' => $request->input("name"), 'image' => $fileName]);
            return response()->json($item, 201);
        }
        return response()->json("Bad request", 400);
    }


    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image'
    ]);

    $category = Category::findOrFail($id);
    $category->name = $request->input('name');

    if ($request->hasFile('image')) {
        $category->image = $request->file('image')->store('images');
    }

    $category->save();

    return response()->json($category);
}



    protected function deleteImage(int $id) {
        $item = Category::find($id);
        foreach ($this->sizes as $size) {
            $path = public_path($this->upload.$size."_".$item->image);
            if(file_exists($path))
                unlink($path);
        }
    }

    protected function saveImage(UploadedFile $file) {
        $fileName = uniqid(). ".webp";
        $manager = new ImageManager(new Driver());
        foreach ($this->sizes as $size) {
            $imageSave = $manager->read($file);
            $imageSave->scale(width: $size);
            $path = public_path($this->upload.$size."_".$fileName);
            $imageSave->toWebp()->save($path);
        }
        return $fileName;
    }


    public function destroy(int $id)
    {
        $category = Category::with('products')->findOrFail($id);

        // Delete associated product images
        foreach ($category->products as $product) {
            foreach ($this->sizes as $size) {
                $productImagePath = public_path($this->upload . $size . "_" . $product->image);
                if (file_exists($productImagePath)) {
                    unlink($productImagePath);
                }
            }
            $product->delete();
        }

        $this->deleteImage($id);

        $category->delete();

    }
    public function show($id)
    {
    $category = Category::findOrFail($id);
    return response()->json($category);
    }

}